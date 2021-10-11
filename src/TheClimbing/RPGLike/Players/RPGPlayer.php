<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Players;


use pocketmine\entity\Attribute;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\level\Position;

use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Skills\BaseSkill;
use TheClimbing\RPGLike\Skills\Coinflip;
use TheClimbing\RPGLike\Skills\DoubleStrike;
use TheClimbing\RPGLike\Skills\Explosion;
use TheClimbing\RPGLike\Skills\Fortress;
use TheClimbing\RPGLike\Skills\HealingAura;
use TheClimbing\RPGLike\Skills\Tank;
use TheClimbing\RPGLike\Systems\PartySystem;
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
    private float $defModifier = 0.5;
    private int $defBonus = 1;
    private int $dex = 1;
    private float $dexModifier = 0.005;
    private $dexBonus = 1;

    /**
     * @var array
     */
    public array $invites = [];
    public string $partyName = '';

    private Config $config;

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
        if (array_key_exists($this->getName(), $players->getAll())) {
            $block_breaks = $players[$this->getName()]['block_breaks'];
        }
        foreach ($traits as $key => $value) {
            if ($block_breaks != 0) {
                $this->traits[$key] = new BaseTrait($key, $value['blocks'], $value['levels'], $value['action'], $block_breaks);
            } else {
                $this->traits[$key] = new BaseTrait($key, $value['blocks'], $value['levels'], $value['action']);
            }

        }

    }
    protected function onDeath(): void
    {
	$this->doCloseInventory();
	$ev = new PlayerDeathEvent($this, $this->getDrops(), null, $this->getXpDropAmount());
	$ev->call();
	if(!$ev->getKeepInventory()){
	    foreach($ev->getDrops() as $item){
	        $this->level->dropItem($this, $item);
            }
	    if($this->inventory !== null){
	        $this->inventory->setHeldItemIndex(0);
	        $this->inventory->clearAll();
	    }
	    if($this->armorInventory !== null){
	        $this->armorInventory->clearAll();
	    }
	}
	if($ev->getDeathMessage() != ""){
	    $this->server->broadcastMessage($ev->getDeathMessage());
	}
    }
    
    protected function respawn(): void
    {
	if($this->server->isHardcore()){
	    $this->setBanned(true);
	    return;
	}
	$ev = new PlayerRespawnEvent($this, $this->getSpawn());
	$ev->call();
	$realSpawn = Position::fromObject($ev->getRespawnPosition()->add(0.5, 0, 0.5), $ev->getRespawnPosition()->getLevelNonNull());
	$this->teleport($realSpawn);
	$this->setSprinting(false);
	$this->setSneaking(false);
	$this->extinguish();
	$this->setAirSupplyTicks($this->getMaxAirSupplyTicks());
	$this->deadTicks = 0;
	$this->noDamageTicks = 60;
	$this->removeAllEffects();
	$this->setHealth($this->getMaxHealth());
	foreach($this->attributeMap->getAll() as $attr){
            if ($this->config->getNested('keep-xp') === true) {
                if($attr->getId() === Attribute::EXPERIENCE or $attr->getId() === Attribute::EXPERIENCE_LEVEL){
	            $attr->markSynchronized(false);
	            continue;
                }
            }
        $attr->resetToDefault();
        }
        $this->sendData($this);
        $this->sendData($this->getViewers());
        $this->sendSettings();
        $this->sendAllInventories();
        $this->spawnToAll();
        $this->scheduleUpdate();
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
        $this->vitBonus = (int)($this->getVIT() * $this->getVITModifier());
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
            new HealingAura($this),
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
            if (!$skill->isUnlocked()) {
                $skillBaseUnlock = $skill->getBaseUnlock();
                $met_criteria = 0;
                foreach ($skillBaseUnlock as $key => $value) {

                    if ($this->getAttribute($key) >= $value) {
                        $met_criteria++;
                    }
                }
                if ($met_criteria == count($skillBaseUnlock)) {
                    $skill->unlock();
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
            $event->setBaseDamage($event->getFinalDamage() - $receiver->getDEFBonus());
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
        $playersConfig = RPGLike::getInstance()->getPlayers();
        $playerVars = [
            'attributes' => $this->getAttributes(),
            'skills' => $this->getSkillNames(),
            'spleft' => $this->getSPleft(),
            'level' => $this->getXPLevel(),
            'blocks' => $this->getBrokenBlocks()
        ];
        $players = $playersConfig->getAll();
        $players[$this->getName()] = $playerVars;
        $playersConfig->setAll($players);
        $playersConfig->save();
    }

    public function getBrokenBlocks(): array
    {
        $broken_blocks = [];
        foreach ($this->traits as $key => $trait) {
            $broken_blocks[$key] = $trait->getBlockBreaks();
        }
        return $broken_blocks;
    }

    public function restorePlayerVariables()
    {
        $cachedPlayer = $this->getPlayerVariables();
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

            $this->applyDexterityBonus();
            $this->applyVitalityBonus();

            $this->setSPleft($cachedPlayer['spleft']);
            if (!empty($cachedPlayer['skills'])) {
                foreach ($cachedPlayer['skills'] as $skill) {
                    $this->getSkill($skill)->unlock(true);
                }
            }
            foreach ($this->skills as $skill) {
                $skill->checkLevel(true);
            }
            foreach ($cachedPlayer['blocks'] as $trait => $block_count) {
                $this->traits[$trait]->restorePlayerTrait($block_count);
            }
        }
    }

    public function getPlayerVariables()
    {
        $players = RPGLike::getInstance()->getPlayers()->getAll();
        if (array_key_exists($this->getName(), $players)) {
            return $players[$this->getName()];
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

    public function setExperienceLevel(int $level)
    {
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

    public function getConfig(): Config
    {
        return $this->config;
    }
    public function hasPartyInvite(){
        if ($this->invites['party'] != ''){
            return true;
        }
        return false;
    }
    public function sendPartyInvite(string $party_key){
        $this->invites['party'] = $party_key;
        $this->sendMessage("You've been invited to join into party: " . $party_key . ". You can accept or decline using /party accept|decline");
    }
    public function removePartyInvite(){
        $this->invites['party'] = '';
    }
    public function getParty(){
        return PartySystem::getPlayerParty($this->partyName);
    }
    public function getPartyInvite(): string
    {
        return $this->invites['party'];
    }
    public function shareEXP(PlayerExperienceChangeEvent $event){
        $party = $this->getParty();
        if ($party != false){
            $members = $party->getPartyMembers();
            foreach ($members as $member) {
                if ($member == $this){
                    continue;
                }else{
                    $member->getBonusExp($event->getNewProgress() - $event->getOldProgress());
                }
            }
        }
    }
    public function getBonusExp(float $progress){
        $this->setXpProgress($this->getXpProgress() + $progress);//Not sure if this won't cause any issues
    }
}
    
