<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/10/2020
     * Time: 2:18 PM
     */
    
    namespace TheClimbing\RPGLike\CustomCommands;
    
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    
    use TheClimbing\RPGLike\Main;

    class LevelUpCommand extends Command
    {
        private $loader;
        
        public function __construct (Main $loader)
        {
            parent::__construct("levelup");
            $this->loader = $loader;
        }
        public function execute (CommandSender $sender, string $commandLabel, array $args)
        {
            $player = $sender->getServer()->getPlayer($sender->getName());
            $player->setXpLevel($args[0]);
        }
    }