<?php
    
    namespace TheClimbing\RPGLike\Commands;
    
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;
    use pocketmine\Player;

    use TheClimbing\RPGLike\Forms\RPGForms;
    use TheClimbing\RPGLike\Players\PlayerManager;
    use TheClimbing\RPGLike\RPGLike;

    class RPGCommand extends Command
    {
        private $loader;
        public function __construct(RPGLike $rpg)
        {
            parent::__construct('rpg');
            $this->loader = $rpg;
            $this->setDescription('Opens RPG Menu');
            $this->setPermission('rpglike.rpgcommand');
        }
        public function execute(CommandSender $sender, string $commandLabel, array $args)
        {
            if($sender instanceof Player && $sender->hasPermission($this->getPermission())){
                $player = PlayerManager::getPlayer($sender->getName());
                if(empty($args)){
                    RPGForms::RPGMenuForm($player);
                }else{
                    $args = array_map('strtolower', $args);
                    switch($args){
                        case "stats":
                            RPGForms::statsForm($player);
                            break;
                        case "skills":
                            RPGForms::skillsHelpForm($player);
                            break;
                        case "help":
                            RPGForms::helpForm($player, $args[1]);
                            break;
                    }
                }
            }else{
                $sender->sendMessage($this->getPermissionMessage());
            }
        }
    }