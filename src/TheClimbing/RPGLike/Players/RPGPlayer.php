<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Players;


use pocketmine\entity\Attribute;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
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
use TheClimbing\RPGLike\Traits\BaseTrait;

class RPGPlayer extends Player
{
    public int $spleft = 0;
    public int $xplevel = 0;

    /**
     * @var BaseSkill[]
     */
    private array $skills = [];
    /**
     * @var BaseTrait[]
     */
    private array $traits = [];
    private int $str = 1;
    private float $strModifier = 0.15;
    private int $strBonus = 0;
    private int $vit = 1;
    private float $vitModifier = 0.175;
    private int $vitBonus = 1;
    private int $def = 1;
    private float $defModifier = 0.1;
    private int $defBonus = 1;
    private int $dex = 1;
    private float $dexModifier = 0.0002;
    private int $dexBonus = 1;
    private \pocketmine\utils\Config $config;

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
        $traits = $this->config->getNested("Traits");
        $players = RPGLike::getInstance()->getPlayers();
        $block_breaks = 0;
        if (array_key_exists($this->getName(),$players) ){
            $block_breaks = $players[$this->getName()]['block_breaks'];
        }
        foreach ($traits as $key => $value) {
            if ($block_breaks != 0){
                $this->traits[$key] = new BaseTrait($key,$value['blocks'], $value['levels'], $value['action'] , $block_breaks);
            }else{
                $this->traits[$key] = new BaseTrait($key,$value['blocks'], $value['levels'], $value['action']);
            }

        }

    }

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
        $this->vitBonus = (int)($this->getVIT() * $this->getVITModifier()); //TODO why cats it to int?
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
    }

    public function getSkill(string $skillName): ?BaseSkill
    {
        return $this->skills[$skillName];
    }

    public function applyDexterityBonus()
    {
        $dex = $this->getDEXBonus();
        $movement = $this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
        $movement->setValue($movement->getValue() * (1 + $dex));
    }

    public function getDEXBonus(): float
    {
        return $this->dexBonus;
    }

    public function getMovementSpeed(): float
    {
        $this->applyDexterityBonus();
        return $this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->getValue();
    }

    public function checkSkillLevel()
    {
        foreach ($this->skills as $skill) {
            $skill->checkLevel();
        }
    }

    /* @return  BaseSkill[] */
    public function getSkills(): array
    {
        return $this->skills;
    }
    public function checkForSkills()
    {
        foreach ($this->skills as $skill) {
            if (!$skill->isUnlocked()){
                $skillBaseUnlock = $skill->getBaseUnlock();
                $met_criteria = 0;
                foreach ($skillBaseUnlock as $key => $value) {

                    if ($this->getAttribute($key) >= $value){
                        $met_criteria++;
                    }
                }
                if ($met_criteria == count($skillBaseUnlock)){
                    $skill->unlock();
                    RPGForms::skillUnlockForm($this,$skill);
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
        $this->strBonus = (int)($this->getSTR() * $this->getSTRModifier());
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
        $this->defBonus = (int)($this->getDEF() * $this->getDEFModifier());
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
        $origial_knockback = $event->getKnockBack();
        if ($damager instanceof RPGPlayer) {
            $event->setModifier($this->getSTRBonus() + $event->getBaseDamage(), EntityDamageEvent::CAUSE_ENTITY_ATTACK);
        }
        $event->setKnockBack($origial_knockback);

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
            $event->setBaseDamage($event->getBaseDamage() - $receiver->getDEFBonus());
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
            'blocks' => $this->getBrokenBlocks()
        ];
        $players = $this->config->getNested('Players');
        $players[$this->getName()] = $playerVars;
        $this->config->setNested('Players', $players);
        $this->config->save();
    }
    public function getBrokenBlocks() : array
    {
        $broken_blocks = [];
        foreach ($this->traits as $key => $trait) {
            $broken_blocks[$key] =  $trait->getBlockBreaks();
        }
        return $broken_blocks;
    }

    public function restorePlayerVariables()
    {
        $cachedPlayer = $this->getPlayerVariables($this->getName());
        if ($cachedPlayer != false) {
            $attributes = $cachedPlayer['attributes'];
            $this->setDEF($attributes['DEF']);
            $this->setDEX($attributes['DEX']);
            $this->setSTR($attributes['STR']);
            $this->setVIT($attributes['VIT']);

            $this->xplevel = $cachedPlayer['level'];

            $this->calcDEXBonus();
            $this->calcDEFBonus();
            $this->calcVITBonus();
            $this->calcSTRBonus();

            $this->setSPleft($cachedPlayer['spleft']);
            if (!empty($cachedPlayer['skills'])) {
                foreach ($cachedPlayer['skills'] as $skill) {
                    $this->getSkill($skill)->unlock();
                }
            }
            foreach ($this->skills as $skill){
                $skill->checkLevel();
            }
            foreach ($cachedPlayer['blocks'] as $trait => $block_count) {
                $this->traits[$trait]->restorePlayerTrait($block_count);
            }
        }
    }

    public function getPlayerVariables(string $playerName)
    {
        $players = $this->config->getNested('Players');
        if ($players != null) {
            if (array_key_exists($playerName, $players)) {
                return $players[$playerName];
            } else {
                return false;
            }
        } else {
            return false;
        }
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

    public function getSPleft(): int
    {
        return $this->spleft;
    }

    public function setSPleft(int $spleft)
    {
        $this->spleft = $spleft;
    }

    public function getX(): float
    {
        return $this->lastX;
    }

    public function getZ(): float
    {
        return $this->lastZ;
    }
    public function setExperienceLevel(int $level){
        $this->xplevel = $level;
        $this->setXpLevel($level);
    }
    public function getXpLvl(): int
    {
        return $this->xplevel;
    }

    public function getTraits(): array
    {
        return $this->traits;
    }
    public function getConfig(): \pocketmine\utils\Config
    {
        return $this->config;
    }
}
    