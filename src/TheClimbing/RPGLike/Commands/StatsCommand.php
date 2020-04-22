<?php
    
    namespace TheClimbing\RPGLike\Commands;
    
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;
    
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\RPGLike;

    class StatsCommand extends Command
    {
        private $loader;
        public function __construct(RPGLike $rpg)
        {
            parent::__construct('stats');
            $this->loader = $rpg;
            $this->setDescription('Shows your current stats');
            $this->setPermission('rpglike.stats');
            $this->setAliases(['stats, statistics']);
        }
        public function execute(CommandSender $sender, string $commandLabel, array $args)
        {
            if($sender instanceof Player && $sender->hasPermission($this->getPermission())){
                $this->loader->statsForm(PlayerManager::getPlayer($sender->getName()));
            }
            if($sender->isOp()){
                $this->loader->statsForm(PlayerManager::getPlayer($sender->getName()));
            }
        }
    }