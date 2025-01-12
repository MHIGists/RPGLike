<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\ItemIds;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Items\UncommonTierItem;
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
        if ($player instanceof RPGPlayer) {
            $joinTime = (((int)(microtime(true) * 1000) - $player->getFirstPlayed()));
            if (($joinTime / 1000) < 300) {
                RPGForms::welcomeForm($player);
            }
            $player->restorePlayerVariables();
            $player->getInventory()->addItem(new UncommonTierItem(ItemIds::STONE_SWORD, 1, 'Uncommon Sword', ['damage' => 1])); //TODO
        }
    }

    public function blockPickup(PlayerBlockPickEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer) {
            foreach ($player->getTraits() as $trait) {
                $trait->blockPickup($event);
            }
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer) {
            $playerSkills = $player->getSkills();
            foreach ($playerSkills as $playerSkill) {
                if ($playerSkill->isUnlocked()) {
                    if ($playerSkill->isAOE()) {
                        $playerSkill->checkRange();
                    }
                }
            }
        }
    }

    public function playerDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof RPGPlayer) {
			if ($this->main->config['keep-xp'] === true) {
                $event->setXpDropAmount(0);
            } else {
				$player->setDEX(1);
				$player->setSTR(1);
				$player->setVIT(1);
				$player->setDEF(1);
				$player->resetSkills();
				$player->setExperienceLevel(0);
			}
        }
    }

    public function arrowHit(ProjectileHitEntityEvent $event)
    {
        $player = $event->getEntity()->getOwningEntity();
        $entity_hit = $event->getEntityHit();
        if ($player instanceof RPGPlayer) {
            $explosion = $player->getSkill('Explosion');
            if ($explosion->isUnlocked()) {
                $explosion->damageEvent($player, $entity_hit);
            }
        }
    }

    public function dealDamageEvent(EntityDamageByEntityEvent $event)
    {
        $damager = $event->getDamager();
        $receiver = $event->getEntity();
        if ($damager instanceof RPGPlayer && $receiver instanceof RPGPlayer) {
            if ($damager->getParty()){
                if($damager->getParty()->playerInThisParty($receiver->getName())){
                    $event->cancel();
                }
            }
            $coinflip = $damager->getSkill('Coinflip');
            $doublestrike = $damager->getSkill('DoubleStrike');
            $damager->applyDamageBonus($event);
            $damager->applyDefenseBonus($event);
            if ($coinflip->isUnlocked()) {
                $coinflip->passiveEffect($event);
            }
            if ($doublestrike->isUnlocked()) {
                $doublestrike->passiveEffect($event);
            }
            foreach ($damager->getTraits() as $trait) {
                $trait->entityKill($event);
            }
        }
    }

    public function onLevelUp(PlayerExperienceChangeEvent $event)
    {
        $player = $event->getEntity();
        if ($player instanceof RPGPlayer) {
            $player->shareEXP($event);
            $new_lvl = $event->getNewLevel();
            $old_level = $player->getXpLvl();
            if ($new_lvl !== null) {
                if ($new_lvl > $old_level) {

                    $player->xplevel = $new_lvl;
                    $spleft = $new_lvl - $old_level;
                    $player->setSPleft($player->getSPleft() + $spleft);
                    $player->setHealth($player->getMaxHealth());
                    $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                    $player->setAirSupplyTicks($player->getMaxAirSupplyTicks());
                    if ($player->xplevel ==  1){
                        $player->sendMessage(Utils::parseKeywords(RPGLike::getInstance()->consts, RPGLike::getInstance()->getMessages()->get('level_up_message')));
                    }
                }
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
