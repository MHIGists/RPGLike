<?php
    
    declare(strict_types = 1);
    
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function floor;
    use function call_user_func;
    use function array_slice;
    use function is_callable;
    
    use pocketmine\Player;
    
    use pocketmine\entity\Effect;
    use pocketmine\entity\EffectInstance;
    
    use pocketmine\level\Level;
    use pocketmine\math\Vector3;
    /**
     * Class BaseSkill
     * @package TheClimbing\RPGLike\Skills
     */
    class BaseSkill
    {
        public static $namespace = "\\TheClimbing\\RPGLike\\Skills\\";
        /**
         * @var string
         */
        private $name ;
        
        /**
         * @var string
         */
        private $type ;
        /**
         * @var array
         */
        private $description ;
        /**
         * @var int
         */
        private $cooldown ; //TODO implement this
        /**
         * @var int
         */
        private $range ;
        /**
         * @var array
         */
        private $skillUpgrades = [10, 20, 30];
        /**
         * @var string
         */
        private $attribute;
    
        /**
         * That's including the source
         *
         * @var int
         */
        private $maxEntInRange;
        
        /**
         * @var null
         */
        private $effect;
    
    
        /**
         * @var int
         */
        private $skillLevel = 0;
    
    
        /**
         * BaseSkill constructor.
         *
         * @param string                       $name
         * @param string                       $type
         * @param array                        $description
         * @param int                          $cooldown
         * @param int                          $range
         * @param string                       $attribute
         * @param int                          $maxEntInRange
         * @param null                         $effect
         */
        public function __construct( string $name = '', string $type = '', array $description = [], int $cooldown = 0, int $range = 0, string $attribute = '', int $maxEntInRange = 1, $effect = null )
        {
            $this->name = $name;
            $this->type = $type;
            $this->description = $description;
            $this->cooldown = $cooldown;
            $this->range = $range;
            $this->attribute = $attribute;
            $this->maxEntInRange = $maxEntInRange;
            $this->effect = $effect;
        }
       
        /**
         * @param string $name
         */
        protected function setName(string $name) : void
        {
            $this->name = $name;
        }
    
        /**
         * @return string
         */
        public function getName() : string
        {
            return $this->name;
        }
    
        /**
         * @param string $type
         */
        protected function setType(string $type) : void
        {
            $this->type = $type;
        }
    
        /**
         * @return string
         */
        public function getType() : string
        {
            return $this->type;
        }
    
        /**
         * @param array $description
         */
        protected function setDescription(array $description) : void
        {
            $this->description = $description;
        }
    
        /**
         * @return array
         */
        public function getDescription() : array
        {
            return $this->description;
        }
        protected function setCooldown(int $cooldown) : void
        {
            $this->cooldown = $cooldown;
        }
        /**
         * @return int
         */
        public function getCooldown() : int
        {
            return $this->cooldown;
        }
        protected function setRange(int $range) : void
        {
            $this->range = $range;
        }
        /**
         * @return int
         */
        public function getRange() : int
        {
            return $this->range;
        }
    
        /**
         * @param int $baseUnlock
         */
        protected function setBaseUnlock(int $baseUnlock) : void
        {
            $this->skillUpgrades[0] = $baseUnlock;
        }
    
        /**
         * @return int
         */
        public function getBaseUnlock() : int
        {
            return $this->skillUpgrades[0];
        }
    
        /**
         * Sets the required amount of points needed to upgrade the skill
         *
         * @param array $upgrades
         */
        protected function setUpgrades(array $upgrades) : void
        {
            $this->skillUpgrades = $upgrades;
        }
    
        /**
         * @return array
         */
        public function getUpgrades() : array
        {
            return $this->skillUpgrades;
        }
    
        /**
         * @param string $attribute
         */
        protected function setAttribute(string $attribute) : void
        {
            $this->attribute = $attribute;
        }
        /**
         * @return string
         */
        public function getAttribute() : string
        {
            return $this->attribute;
        }
    
        /**
         * @param int $maxEnt
         */
        protected function setMaxEntInRange(int $maxEnt) : void
        {
            $this->maxEntInRange = $maxEnt;
        }
    
        /**
         * @return int
         */
        public function getMaxEntInRange() : int
        {
            return $this->maxEntInRange;
        }
    
        /**
         * @param $id
         */
        protected  function setEffect($id) : void
        {
            $this->effect = $id;
        }
        /**
         * @return int|null
         */
        public function getEffect() : ?int
        {
            return $this->effect;
        }
    
        /**
         * @param int $skillLevel
         */
        public function setSkillLevel(int $skillLevel) : void
        {
            $this->skillLevel = $skillLevel;
        }
    
        /**
         * @return int
         */
        public function getSkillLevel() : int
        {
            return $this->skillLevel;
        }
        
        
        /**
         * @param Player $player
         * @param array              $func
         */
        public function checkRange(Player $player, array $func = []) : void
        {
            if($this->range > 0) {
                $level = $player->getLevel();
                $players = $this->getNearestEntities($player->getPosition()->asVector3(), $this->range, $level, $this->getMaxEntInRange());
                if(!empty($players)){
                    foreach($players as $player) {
                        if(!empty($func)){
                            $this->setPlayerEffect($player, $func);
                        }else{
                            $this->setPlayerEffect($player, $this->getEffect());
                        }
                    }
                }
            }
        }
    
        /**
         * Basically taken from source.
         *
         * @param Vector3 $pos
         * @param int|null                 $maxDistance
         * @param Level $level
         * @param int                      $maxEntities
         * @param bool                     $includeDead
         *
         * @return array
         */
        public function getNearestEntities(Vector3 $pos, ?int $maxDistance, Level $level, int $maxEntities, bool $includeDead = false) : array
        {
            $nearby = [];
            
            $minX = ((int) floor($pos->x - $maxDistance)) >> 4;
            $maxX = ((int) floor($pos->x + $maxDistance)) >> 4;
            $minZ = ((int) floor($pos->z - $maxDistance)) >> 4;
            $maxZ = ((int) floor($pos->z + $maxDistance)) >> 4;
            
            $currentTargetDistSq = $maxDistance ** 2;
            
            for($x = $minX ; $x <= $maxX ; ++$x) {
                for($z = $minZ ; $z <= $maxZ ; ++$z) {
                    $entities = ($chunk = $level->getChunk($x, $z)) !== null ? $chunk->getEntities() : [];
                    if(count($entities) > $maxEntities){
                        $entities = array_slice($entities, 0, $maxEntities);
                    }
                    foreach($entities as $entity) {
                        if(!($entity instanceof Player) or $entity->isClosed() or $entity->isFlaggedForDespawn() or (!$includeDead and !$entity->isAlive())) {
                            continue;
                        }
                        $distSq = $entity->distanceSquared($pos);
                        if($distSq < $currentTargetDistSq) {
                            $currentTargetDistSq = $distSq;
                            $nearby[] = $entity;
                        }
                    }
                }
            }
            return $nearby;
        }
    
        /**
         * Sets vanilla effects or applies a callable.
         * Needs testing!
         *
         * @param Player $player
         * @param                    $effect
         */
        public function setPlayerEffect(Player $player, $effect) : void
        {
            if(is_callable($effect)){
                call_user_func($effect['objAndFunc'], $effect['params']);
            }else{
                $effect = new EffectInstance(Effect::getEffect($effect), 2, $this->getSkillLevel());
                $player->addEffect($effect);
            }
            
        }
        
    }