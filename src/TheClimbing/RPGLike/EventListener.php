<?php
    
    declare(strict_types = 1);

    namespace TheClimbing\RPGLike;
    

    use pocketmine\event\entity\EntityRegainHealthEvent;
    use pocketmine\event\player\PlayerCreationEvent;
    use pocketmine\event\Listener;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerExperienceChangeEvent;
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

        public function onJoin(PlayerJoinEvent $event)
        {
            $player = $event->getPlayer();
            if ($player->getXpLevel() <= 0) {
                $player->setXpLevel(1);
            }
            PlayerManager::makePlayer($player);
            $player->applyDexterityBonus();
            $player->applyVitalityBonus();
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

            if ($player->getXpLevel() < $player->xplevel)
            {
                $player->setXpLevel($player->xplevel);
            }
        }

        public function dealDamageEvent(EntityDamageByEntityEvent $event)
        {
            $player = $event->getDamager();
            if ($player instanceof RPGPlayer) {
                $player->applyDamageBonus($event);
                $player->applyDefenseBonus($event);
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

                        RPGForms::upgradeStatsForm($player, $spleft);
                        $player->applyVitalityBonus();
                        $player->applyDexterityBonus();
                    }
                }

            }

        }
        public function healtRegen(EntityRegainHealthEvent $event)
        {
            $player = $event->getEntity();
            if ($player instanceof Player && $event->getRegainReason() == EntityRegainHealthEvent::CAUSE_SATURATION)
            {
                $event->setAmount($event->getAmount() + $player->getHealthRegenBonus());
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

        public function onLeave(PlayerQuitEvent $event)
        {
            $event->getPlayer()->savePlayerVariables();
        }

    }
