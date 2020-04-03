<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/8/2020
     * Time: 9:29 PM
     */
    
    namespace TheClimbing\RPGLike;
    
    use pocketmine\Player;
    
    use pocketmine\event\Listener;
    
    use pocketmine\event\entity\EntityDamageEvent;
    use pocketmine\event\entity\EntityDamageByEntityEvent;
    
    use pocketmine\event\player\PlayerQuitEvent;
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
    
        public function onJoin (PlayerJoinEvent $event)
        {
            $player = $event->getPlayer();
            $playerName = $player->getName();
            
            if ($this->rpg->playerExists($player) === false) {
                $this->rpg->setUpPlayer($player, true);
            }
            
           
            $this->rpg->setPlayerDamage($playerName);
            $this->rpg->setPlayerVitality($player);
            $this->rpg->setPlayerDefense($playerName);
            $this->rpg->setPlayerDexterity($playerName);
            $this->rpg->setPlayerMovement($player);
            
        }
        public function dealDamageEvent (EntityDamageByEntityEvent $event)
        {
            $dealer = $event->getDamager();
            if ($dealer instanceof Player){
                $playerName = $dealer->getName();
                $bonusDMG = $this->rpg->getPlayerDamage($playerName);
                $event->setBaseDamage($event->getFinalDamage() * $bonusDMG);
            }
        }
        
        public function receiveDmgEvent(EntityDamageEvent $event){
            $receiver = $event->getEntity();
            if ($receiver instanceof Player){
                $playerName = $receiver->getName();
                if ($event->canBeReducedByArmor()){
                    $receiver->setAbsorption($receiver->getAbsorption() * $this->rpg->getPlayerDefense($playerName));
                }
            }
        }
        
        public function onLevelUp (PlayerExperienceChangeEvent $event)
        {
            $player = $event->getEntity();
            
            $new_lvl = $event->getNewLevel();
            $old_level = $event->getOldLevel();
            
            if ($new_lvl !== null) {
                if ($new_lvl > $old_level){
                    if ($player instanceof Player) {
                        $spleft = $new_lvl - $old_level;
                        
                        $playerName = $player->getName();
                        
                        $level = $this->rpg->getPlayerLevel($playerName);
                        
                        $this->rpg->setPlayerLevel($playerName, $level + 1);

                        $this->rpg->upgradeStatsForm($player, $spleft);
                        
                        $this->rpg->saveVars();
                        
                    }
                }
            }
        }
        public function onDeath(PlayerDeathEvent $event){
            $this->rpg->wipePlayer($event->getPlayer()->getName());
        }
        public function onRespawn(PlayerRespawnEvent $event){
            $player = $event->getPlayer();
            
            if ($player->getXpLevel() == 0){
                $this->rpg->setUpPlayer($player);
            }
        }
        public function onLogout(PlayerQuitEvent $event){
            $this->rpg->saveVars();
        }
    }