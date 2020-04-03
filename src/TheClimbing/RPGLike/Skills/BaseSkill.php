<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/29/2020
     * Time: 4:59 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function get_class;
    use function in_array;
    
    use TheClimbing\RPGLike\Utils;
    use TheClimbing\RPGLike\RPGLike;

    class BaseSkill
    {
        public $name = '';
        public $type = '';
        public $description;
        public $cooldown = 0;
        public $range = 0;
        public $baseUnlock = 10;
        public $upgrade = 30;
        public $attribute = '';
        public $rpg;
        
        public $players;
        public $className;
        
        
        public function __construct(string $name, string $type, string $description, int $cooldown, int $range,string $attribute, RPGLike $rpg)
        {
            $this->className = Utils::get_class_name(get_class($this));
            
            $this->name = $name;
            $this->type = $type;
            $this->description = $description;
            $this->cooldown = $cooldown;
            $this->range = $range;
            $this->attribute = $attribute;
            $this->rpg = $rpg;
            
            $this->players[$this->className] = [];
           
            
        }
    
        public function getName()
        {
            return $this->name;
        }
        
        public function getType()
        {
            return $this->type;
        }
        
        public function getDescription()
        {
            return $this->description;
        }
        
        public function getCooldown()
        {
            return $this->cooldown;
        }
        
        public function getRange()
        {
            return $this->range;
        }
        public function setBaseUnlock(int $baseUnlock)
        {
            $this->baseUnlock = $baseUnlock;
        }
        public function getUnlock() : int
        {
            return $this->baseUnlock;
        }
        public function setUpgrade(int $upgrade)
        {
            $this->upgrade = $upgrade;
        }
        public function getUpgrade() : int
        {
            return $this->upgrade;
        }
        public function setPlayer(string $playerName) : void
        {
            if($this->playerHasSkill($playerName) == false){
                $this->players[$this->className][] = $playerName;
            }
        }
        public function getAttribute() : string
        {
            return $this->attribute;
        }
        public function playerHasSkill(string $playerName) : bool
        {
            return in_array($playerName, $this->players[$this->className]);
        }
        public function isSkillUnlocked(string $playerName) : bool
        {
            if($this->baseUnlock <= $this->rpg->getPlayerAttribute($playerName, $this->getAttribute())){
                return true;
            }else
            {
                return false;
            }
        }
        public function getPlayers() : array
        {
            return $this->players;
        }
        
    }