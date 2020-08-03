<?php
    
    declare(strict_types = 1);

    namespace TheClimbing\RPGLike;
    
    use pocketmine\event\player\PlayerCreationEvent;
    use pocketmine\event\Listener;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    use pocketmine\event\player\PlayerExperienceChangeEvent;
    use pocketmine\event\player\PlayerDeathEvent;

    use pocketmine\event\player\PlayerQuitEvent;
    use TheClimbing\RPGLike\Forms\RPGForms;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\Players\RPGPlayer;

    class EventListener implements Listener
    {
        private $rpg;
        
        public function __construct(RPGLike $rpg)
        {
            $this->rpg = $rpg;
        }
        
        public function onJoin(PlayerJoinEvent $event)
        {
            $player = $event->getPlayer();
            PlayerManager::makePlayer($player);
            RPGLike::getInstance()->applyDexterityBonus($player);
            RPGLike::getInstance()->applyVitalityBonus($player);
        }
        public function playerCreate(PlayerCreationEvent $event)
        {
            $event->setPlayerClass("TheClimbing\RPGLike\Players\RPGPlayer");
            $player = new RPGPlayer($event->getInterface(), $event->getAddress(), $event->getPort());

        }
        public function onMove(PlayerMoveEvent $event)
        {
            $player = $event->getPlayer();
            $playerSkills = $player->getSkills();
            if(!empty($playerSkills)){
                foreach($playerSkills as $playerSkill){
                        $playerSkill->checkRange($player);
                }
            }
        }
        public function dealDamageEvent(EntityDamageByEntityEvent $event)
        {
            $this->rpg->applyDamageBonus($event);
            $this->rpg->applyDefenseBonus($event);
        }
        
        public function onLevelUp(PlayerExperienceChangeEvent $event)
        {
            $player = $event->getEntity();
            
            $new_lvl = $event->getNewLevel();
            $old_level = $event->getOldLevel();
            
            if($new_lvl !== null) {
                if($new_lvl > $old_level) {
                    if($player instanceof RPGPlayer) {
                        $spleft = $new_lvl - $old_level;
                        
                        RPGForms::upgradeStatsForm($player, $spleft);

                        $this->rpg->applyVitalityBonus($player);
                        $this->rpg->applyDexterityBonus($player);

                        
                    }
                }
            }
        }
        
        public function onDeath(PlayerDeathEvent $event)
        {
            $event->getPlayer()->reset();
        }
        public function onLeave(PlayerQuitEvent $event)
        {
            $event->getPlayer()->savePlayerVariables();
        }

    }