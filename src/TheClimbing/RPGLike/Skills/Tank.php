<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Skills;
    
    use pocketmine\Player;
    use TheClimbing\RPGLike\Players\PlayerManager;

    class Tank extends BaseSkill
    {
        public function __construct(string $owner, string $namespace)
        {
            $name = 'Tank';
            $type = 'passive';
            $description = [
                'title' => 'You\'ve unlocked the Tank skill!',
                'content' => '"Tank" increases your health by 15%',
                'exitButton' => 'Sweet!'
            ];
            $cooldown = 0;
            $range = 0;
            $attribute = ["VIT"];
            parent::__construct($owner, $namespace, $name, $type, $description, $cooldown, $range, $attribute);
            $this->setPlayerHealth(PlayerManager::getServerPlayer($owner));
        }
        public function setPlayerHealth(Player $player)
        {
            $health = $player->getMaxHealth();
            $player->setMaxHealth((int)($health * 1.15));
        }
    }