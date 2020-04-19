<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike;
    
    use pocketmine\Player;
    
    use pocketmine\event\Listener;
    
    use pocketmine\event\entity\EntityDamageByEntityEvent;

    use pocketmine\event\player\PlayerMoveEvent;
    use pocketmine\event\player\PlayerRespawnEvent;
    use pocketmine\event\player\PlayerJoinEvent;
    
    use pocketmine\event\player\PlayerExperienceChangeEvent;
    use pocketmine\event\player\PlayerDeathEvent;
    
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
            $playerName = $player->getName();
            
            if($this->rpg->playerExists($player) === false) {
                $this->rpg->setUpPlayer($player, true);
            }
            
            $this->rpg->calcDamage($playerName);
            $this->rpg->calcVitality($playerName);
            $this->rpg->calcDefense($playerName);
            $this->rpg->calcDexterity($playerName);
            
            $this->rpg->applyVitalityBonus($player);
            $this->rpg->applyDexterityBonus($player);
            
        }
        public function onMove(PlayerMoveEvent $event)
        {
            $player = $event->getPlayer();
            $playerName = $player->getName();
            $playerSkills = $this->rpg->getPlayerSkills($playerName);
            if(!empty($playerSkills)){
                foreach($playerSkills as $playerSkill){
                    $this->rpg->getSkillsManager()->getSkill($playerSkill)->checkRange($player);
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
                    if($player instanceof Player) {
                        $spleft = $new_lvl - $old_level;
                        
                        $playerName = $player->getName();
                        
                        $level = $this->rpg->getPlayerLevel($playerName);
                        $this->rpg->setPlayerLevel($playerName, $level + 1);
                        $this->rpg->upgradeStatsForm($player, $spleft);
    
                        $this->rpg->applyVitalityBonus($player);
                        $this->rpg->applyDexterityBonus($player);
                        
                        $this->rpg->saveVars();
                        
                    }
                }
            }
        }
        
        public function onDeath(PlayerDeathEvent $event)
        {
            $this->rpg->wipePlayer($event->getPlayer()->getName());
        }
        
        public function onRespawn(PlayerRespawnEvent $event)
        {
            $player = $event->getPlayer();
            
            if($player->getXpLevel() == 0) {
                $this->rpg->setUpPlayer($player);
            }
        }
    }