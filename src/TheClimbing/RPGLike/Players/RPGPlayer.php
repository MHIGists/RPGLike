<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Players;

use pocketmine\block\CoalOre;
use pocketmine\block\Stone;
use pocketmine\block\Wood;
use pocketmine\entity\Attribute;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Skills\BaseSkill;
use TheClimbing\RPGLike\Skills\Coinflip;
use TheClimbing\RPGLike\Skills\DoubleStrike;
use TheClimbing\RPGLike\Skills\Explosion;
use TheClimbing\RPGLike\Skills\Fortress;
use TheClimbing\RPGLike\Skills\HealthRegen;
use TheClimbing\RPGLike\Skills\Tank;

class RPGPlayer extends Player
{


    public $movementSpeed = 0;
    public $spleft = 0;
    public $xplevel = 0;
    /* @var BaseSkill[] */
    private $skills = [];
    private $traits = [];
    private $str = 1;
    private $strModifier = 0.15;
    private $strBonus = 0;
    private $vit = 1;
    private $vitModifier = 0.175;
    private $vitBonus = 1;
    private $def = 1;
    private $defModifier = 0.1;
    private $defBonus = 1;
    private $dex = 1;
    private $dexModifier = 0.0002;
    private $dexBonus = 1;
    private $config;
    private $blocks = [
        'Stone' => [
            'level' => 0,
            'count' => 0
        ],
        'Wood' => [
            'level' => 0,
            'count' => 0
        ],
        'Coal Ore' => [
            'level' => 0,
            'count' => 0
        ],
        'Iron Ore' => [
            'level' => 0,
            'count' => 0
        ],
        'Gold Ore' => [
            'level' => 0,
            'count' => 0
        ],
        'Diamond Ore' => [
            'level' => 0,
            'count' => 0
        ],
        'Lapis Ore' => [
            'level' => 0,
            'count' => 0
        ]
    ];

    public function __construct($interface, $ip, $port)
    {
        parent::__construct($interface, $ip, $port);

        $this->config = RPGLike::getInstance()->getConfig();
        $modifiers = $this->getModifiers();
        if ($modifiers != false) {
            $this->setDEFModifier($modifiers['DEF']);
            $this->setVITModifier($modifiers['VIT']);
            $this->setSTRModifier($modifiers['STR']);
            $this->setDEXModifier($modifiers['DEX']);
        }

        $this->calcVITBonus();
        $this->calcDEXBonus();
        $this->addSkills();
    }
    public function addStone(){
        $this->blocks['Stone']['count'] += 1;
        print_r($this->blocks['Stone']);
    }
    public function addWood(){
        $this->blocks['Stone']['count'] += 1;
    }
    public function addCoal(){
        $this->blocks['Stone']['count'] += 1;
    }
    public function addIron(){
        $this->blocks['Stone']['count'] += 1;
    }
    public function addGold(){
        $this->blocks['Stone']['count'] += 1;
    }
    public function addDiamond(){
        $this->blocks['Stone']['count'] += 1;
    }
    public function addLapis(){
        $this->blocks['Stone']['count'] += 1;
    }
    public function checkBlocks(){
        foreach ($this->getBlocksConfig() as $blockName => $blockArray) {
            foreach ($blockArray as $blockLevel => $blockLevelArray) {
                foreach ($blockLevelArray as $key => $value) {
                    if ($key == 'dropChance'){
                        continue;
                    }
                    if ($this->getBlockCount($blockName) >= $value && $this->getBlockLevel($blockName) < $blockLevel){
                        $this->blocks[$blockName]['level'] += 1;
                        $this->sendMessage('You now have ' . $this->getBlockDropChance($blockName) . '% chance to get bonus drops');
                    }
                }

            }
        }
    }
    public function getBlocksConfig(){
        return $this->config->getNested('Blocks');
    }
    public function getBlockLevel(string $blockName){
        return $this->blocks[$blockName]['level'];
    }
    public function getBlockCount(string $blockName){
        return $this->blocks[$blockName]['count'];
    }
    public function getBlockDropChance(string $blockName){
        if ($this->getBlockLevel($blockName) == 0){
            return -1;
        }
        $blocks = $this->getBlocksConfig();
        return $blocks[$blockName][$this->getBlockLevel($blockName)]['dropChance'];
    }
    /* @return array|false */
    public function getModifiers()
    {
        $modifiers = $this->config->getNested('modifiers');
        if ($modifiers !== null) {
            return $modifiers;
        } else {
            return false;
        }

    }

    public function calcVITBonus(): void
    {
        $this->vitBonus = $this->getVIT() * $this->getVITModifier();
    }

    public function getVIT(): int
    {
        return $this->vit;
    }

    public function setVIT(int $vit): void
    {
        $this->vit = $vit;
        $this->calcVITBonus();
    }

    public function getVITModifier(): float
    {
        return $this->vitModifier;
    }

    public function setVITModifier(float $vitModifier): void
    {
        $this->vitModifier = $vitModifier;
    }

    public function calcDEXBonus(): void
    {
        $this->dexBonus = $this->getDex() * $this->getDEXModifier();
    }

    public function getDEX(): int
    {
        return $this->dex;
    }

    public function setDEX(int $dex): void
    {
        $this->dex = $dex;
        $this->calcDEXBonus();
        $this->applyDexterityBonus();
    }

    public function getDEXModifier(): float
    {
        return $this->dexModifier;
    }

    public function setDEXModifier(float $dexModifier): void
    {
        $this->dexModifier = $dexModifier;
    }

    public function addSkills(): void
    {
        /* @var BaseSkill[] */
        $skills = [
            new Coinflip($this),
            new DoubleStrike($this),
            new Explosion($this),
            new Fortress($this),
            new HealthRegen($this),
            new Tank($this)
        ];
        foreach ($skills as $skill) {
            $this->skills[$skill->getName()] = $skill;
        }
        foreach ($this->config->getAll()['Skills'] as $skillName => $skill) {
            if ($this->getSkill($skillName) === null) {
                $this->skills[$skillName] = new $skill['namespace']($this);
            }
        }
    }

    public function getSkill(string $skillName): ?BaseSkill
    {
        if ($this->hasSkill($skillName)) {
            return $this->skills[$skillName];
        } else {
            return null;
        }
    }

    public function hasSkill(string $skillName): bool
    {
        if (isset($this->skills[$skillName])) {
            return true;
        } else {
            return false;
        }
    }

    public function applyDexterityBonus()
    {
        $dex = $this->getDEXBonus();
        $movement = $this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
        $movement->setValue($movement->getValue() * (1 + $dex));
        $this->movementSpeed = $movement->getValue() * (1 + $dex);
    }

    public function getDEXBonus(): float
    {
        return $this->dexBonus;
    }

    public function checkSkillLevel()
    {
        foreach ($this->skills as $skill) {
            $skill->checkLevel();
        }
    }

    /* @return  BaseSkill[] */
    public function getSkills()
    {
        return $this->skills;
    }

    public function onPickupXp(int $xpValue): void
    {
        if ($this->config->get('keep-xp') == true) {
            $xpValue = 0;
        }
        parent::onPickupXp($xpValue);
    }
    public function checkForSkills()
    {
        foreach ($this->skills as $skill) {
            $skillBaseUnlock = $skill->getBaseUnlock();
            $firstKey = array_key_first($skillBaseUnlock);
            if (is_array($skillBaseUnlock[$firstKey])) {
                $req = 0;
                foreach ($skillBaseUnlock[$firstKey] as $key => $value) {
                    if ($this->getAttribute($key) >= $value) {
                        $req += 1;
                    }
                }
                if ($req == count($skillBaseUnlock[$firstKey])) {
                    $skill->unlock();
                    RPGForms::skillHelpForm($this, $skill->getName(), false);
                }
            } else {
                if ($this->getAttribute($firstKey) >= $skillBaseUnlock[$firstKey]) {
                    $skill->unlock();
                    RPGForms::skillHelpForm($this, $skill->getName(), false);
                }
            }

        }
    }

    public function getAttribute(string $attribute): int
    {
        return $this->getAttributes()[$attribute];
    }

    public function getAttributes(): array
    {
        return [
            'STR' => $this->getSTR(),
            'VIT' => $this->getVIT(),
            'DEF' => $this->getDEF(),
            'DEX' => $this->getDEX(),
        ];
    }

    public function getSTR(): int
    {
        return $this->str;
    }

    public function setSTR(int $str): void
    {
        $this->str = $str;
        $this->calcSTRBonus();
    }

    public function calcSTRBonus(): void
    {
        $this->strBonus = $this->getSTR() * $this->getSTRModifier();
    }

    public function getSTRModifier(): float
    {
        return $this->strModifier;
    }

    public function setSTRModifier(float $strModifier): void
    {
        $this->strModifier = $strModifier;
    }

    public function getDEF(): int
    {
        return $this->def;
    }

    public function setDEF(int $def): void
    {
        $this->def = $def;
        $this->calcDEFBonus();
    }

    public function calcDEFBonus(): void
    {
        $this->defBonus = $this->getDEF() * $this->getDEFModifier();
    }

    public function getDEFModifier(): float
    {
        return $this->defModifier;
    }

    public function setDEFModifier(float $defModifier)
    {
        $this->defModifier = $defModifier;
    }

    public function resetSkills()
    {
        foreach ($this->skills as $skill) {
            $skill->reset();
        }
    }

    public function applyDamageBonus(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        if ($damager instanceof RPGPlayer) {
            $baseDamage = $event->getBaseDamage();
            $event->setBaseDamage($baseDamage + $damager->getSTRBonus());
        }

    }

    public function getSTRBonus(): float
    {
        return $this->strBonus;
    }

    public function applyVitalityBonus()
    {
        $this->setMaxHealth(20 + $this->getVITBonus());
        $this->setHealth(20 + $this->getVITBonus());
    }

    public function getVITBonus(): int
    {
        return (int)ceil($this->vitBonus);
    }

    public function applyDefenseBonus(EntityDamageByEntityEvent $event): void
    {
        $receiver = $event->getEntity();
        if ($receiver instanceof RPGPlayer) {
            $receiver->setAbsorption($receiver->getAbsorption() + $receiver->getDEFBonus());

        }
    }

    public function getDEFBonus(): float
    {
        return $this->defBonus;
    }

    public function getHealthRegenBonus()
    {
        return floor($this->getVITBonus() / 3);
    }

    public function savePlayerVariables(): void
    {
        $playerVars = [
            'attributes' => $this->getAttributes(),
            'skills' => $this->getSkillNames(),
            'spleft' => $this->getSPleft(),
            'level' => $this->getXPLevel(),
        ];
        $players = $this->config->getNested('Players');
        $players[$this->getName()] = $playerVars;
        $this->config->setNested('Players', $players);
        $this->config->save();
    }

    /* @return string[] */
    public function getSkillNames(): array
    {
        $skills = [];
        foreach ($this->skills as $skill) {
            if ($skill->isUnlocked()) {
                $skills[] = $skill->getName();
            }
        }
        return $skills;
    }

    public function getSPleft()
    {
        return $this->spleft;
    }

    public function setSPleft(int $spleft)
    {
        $this->spleft = $spleft;
    }

}
    