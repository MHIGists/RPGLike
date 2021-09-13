<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;

use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Players\RPGPlayer;

class EventListener implements Listener
{
    private RPGLike $main;

    public function __construct(RPGLike $rpg)
    {
        $this->main = $rpg;
    }

    public function playerCreate(PlayerCreationEvent $event)
    {
        $event->setPlayerClass("TheClimbing\RPGLike\Players\RPGPlayer");
    }

    public function playerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $player->restorePlayerVariables();

    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer){
            $playerSkills = $player->getSkills();
            foreach ($playerSkills as $playerSkill) {
                if ($playerSkill->isUnlocked()){
                    if ($playerSkill->isAOE()){
                        $playerSkill->checkRange();
                    }
                }
            }
            if ($this->main->config['keep-xp'] === true) {
                $player->setExperienceLevel($player->xplevel);
            }
        }
    }

    public function playerDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer){
            if ($this->main->config['keep-xp'] === true) {
                $event->setXpDropAmount(0);
            }else{
                $player->setDEX(1);
                $player->setSTR(1);
                $player->setVIT(1);
                $player->setDEF(1);
                $player->resetSkills();
                $player->setExperienceLevel(0);
            }
        }
    }
    public function arrowHit(ProjectileHitEntityEvent $event){
        $player = $event->getEntity()->getOwningEntity();
        $entity_hit = $event->getEntityHit();
        if ($player instanceof RPGPlayer){
            $explosion = $player->getSkill('Explosion');
            if($explosion->isUnlocked()){
                $explosion->damageEvent($player, $entity_hit);
            }
        }
    }
    public function dealDamageEvent(EntityDamageByEntityEvent $event)
    {
        $player = $event->getDamager();
        if ($player instanceof RPGPlayer) {
            $coinflip = $player->getSkill('Coinflip');
            $doublestrike = $player->getSkill('DoubleStrike');
            $explosion = $player->getSkill('Explosion');
            $player->applyDamageBonus($event);
            $player->applyDefenseBonus($event);
            if ($coinflip->isUnlocked()) {
                $coinflip->setCritChance($event);
            }
            if ($doublestrike->isUnlocked()) {
                $doublestrike->setPlayerAttackCD($event);
            }

        }
    }

    public function onLevelUp(PlayerExperienceChangeEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof RPGPlayer) {
            $new_lvl = $event->getNewLevel();
            $old_level = $player->getXpLvl();
            if ($new_lvl !== null) {
                if ($new_lvl > $old_level) {

                    $player->xplevel = $new_lvl;
                    $spleft = $new_lvl - $old_level;

                    RPGForms::upgradeStatsForm($player, $spleft);
                    $player->applyVitalityBonus();
                    $player->applyDexterityBonus();
                    $player->setHealth($player->getMaxHealth());
                    $player->setFood($player->getMaxFood());
                    $player->setAirSupplyTicks($player->getMaxAirSupplyTicks());
                }
            }
        }

    }

    public function healthRegen(EntityRegainHealthEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof RPGPlayer) {
            $healthRegen = $player->getSkill('HealthRegen');
            if ($healthRegen->isUnlocked()) {
                $healthRegen->healthRegen($event);
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer) {
            $player->applyVitalityBonus();
            $player->applyDexterityBonus();
        }
    }

    public function blockDestroy(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer) {
            foreach ($player->getTraits() as $trait) {
                $trait->blockBreak($event);
            }
        }
    }

    public function onLeave(PlayerQuitEvent $event)
    {
        $event->getPlayer()->savePlayerVariables();
    }

}
