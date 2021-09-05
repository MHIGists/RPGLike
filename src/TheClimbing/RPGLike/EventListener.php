<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\Players\PlayerManager;
use TheClimbing\RPGLike\Players\RPGPlayer;

class EventListener implements Listener
{
    private $main;

    public function __construct(RPGLike $rpg)
    {
        $this->main = $rpg;
    }

    public function playerCreate(PlayerCreationEvent $event)
    {
        $event->setPlayerClass("TheClimbing\RPGLike\Players\RPGPlayer");
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $playerSkills = $player->getSkills();
        if (!empty($playerSkills)) {
            foreach ($playerSkills as $playerSkill) {
                if ($playerSkill->getMaxEntInRange() > 1) {
                    $playerSkill->checkRange();
                }
            }
        }

        if ($player->getXpLevel() < $player->xplevel) {
            $player->setXpLevel($player->xplevel);
        }
    }

    public function dealDamageEvent(EntityDamageByEntityEvent $event)
    {
        $player = $event->getDamager();
        $coinflip = $player->getSkill('Coinflip');
        $doublestrike = $player->getSkill('DoubleStrike');
        $explosion = $player->getSkill('Explosion');
        if ($player instanceof RPGPlayer) {
            $player->applyDamageBonus($event);
            $player->applyDefenseBonus($event);
            if ($coinflip->isUnlocked()) {
                $coinflip->setCritChance($event);
            }
            if ($doublestrike->isUnlocked()) {
                $doublestrike->setPlayerAttackCD($event);
            }
            if ($explosion->isUnlocked()) {
                $explosion->damageEvent($event);
            }
        }

    }

    public function onLevelUp(PlayerExperienceChangeEvent $event)
    {
        $player = $event->getEntity();

        $new_lvl = $event->getNewLevel();
        $old_level = $event->getOldLevel();

        if ($new_lvl !== null) {
            if ($new_lvl > $old_level && $new_lvl > $player->xplevel) {
                if ($player instanceof RPGPlayer) {
                    $player->xplevel = $new_lvl;
                    $spleft = $new_lvl - $old_level;

                    $player->checkForSkills();
                    RPGForms::upgradeStatsForm($player, $spleft);
                    $player->applyVitalityBonus();
                    $player->applyDexterityBonus();
                    if ($player->getSkill('Tank')->isUnlocked()) {
                        $player->getSkill('Tank')->setPlayerHealth($player);
                    }
                    if ($player->getSkill('Fortress')->isUnlocked()) {
                        $player->getSkill('Fortress')->setDefense($player);
                    }
                    $player->checkSkillLevel();
                }
            }
        }

    }

    public function healthRegen(EntityRegainHealthEvent $event)
    {
        $player = $event->getEntity();
        $healthRegen = $player->getSkill('HealthRegen');
        if ($player instanceof Player) {
            if ($healthRegen->isUnlocked()) {
                $healthRegen->healthRegen($event);
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();

        if ($player instanceof RPGPlayer && $this->main->config['keep-xp'] != true) {
            $player->setDEX(1);
            $player->setSTR(1);
            $player->setVIT(1);
            $player->setDEF(1);
            $player->resetSkills();
            $player->setXpLevel(1);
        }

        $player->applyVitalityBonus();
        $player->applyDexterityBonus();
    }
    public function blockDestroy(BlockBreakEvent $event){
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $drops = $event->getDrops();
        $player->checkBlocks();
        switch ($block->getName()){
            case 'Stone':
                $player->addBlockCount('Stone');
                if (mt_rand(0, 99) < $player->getBlockDropChance('Stone')) {
                    $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                    $event->setDrops($drops);
                    $player->sendMessage('You just got extra drops!');
                }
                break;
            case 'Oak Wood':
            case 'Spruce Wood':
            case 'Birch Wood':
            case 'Jungle Wood':
                $player->addBlockCount('Wood');
            if (mt_rand(0, 99) < $player->getBlockDropChance('Wood')) {
                $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                $event->setDrops($drops);
            }
                break;
            case 'Coal Ore':
                $player->addBlockCount('CoalOre');
                if (mt_rand(0, 99) < $player->getBlockDropChance('Coal Ore')) {
                    $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                    $event->setDrops($drops);
                }
                break;
            case 'Iron Ore':
                $player->addBlockCount('IronOre');
                if (mt_rand(0, 99) < $player->getBlockDropChance('Iron Ore')) {
                    $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                    $event->setDrops($drops);
                }
                break;
            case 'Gold Ore':
                $player->addBlockCount('GoldOre');
                if (mt_rand(0, 99) < $player->getBlockDropChance('Gold Ore')) {
                    $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                    $event->setDrops($drops);
                }
                break;
            case 'Diamond Ore':
                $player->addBlockCount('DiamondOre');
                if (mt_rand(0, 99) < $player->getBlockDropChance('Diamond Ore')) {
                    $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                    $event->setDrops($drops);
                }
                break;
            case 'Lapis Ore':
                $player->addBlockCount('LapisOre');
                if (mt_rand(0, 99) < $player->getBlockDropChance('Stone')) {
                    $drops[] = $drops[mt_rand(0, count($drops) - 1)];
                    $event->setDrops($drops);
                }
                break;
        }

    }
    public function onLeave(PlayerQuitEvent $event)
    {
        $event->getPlayer()->savePlayerVariables();
    }

}
