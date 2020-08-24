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

    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Tasks\CooldownTask;
    use TheClimbing\RPGLike\Players\RPGPlayer;
    /**
     * Class BaseSkill
     * @package TheClimbing\RPGLike\Skills
     */
    class BaseSkill
    {
        protected $owner;

        private $namespace;

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
        private $cooldown ;

        private $onCooldown = false;
        /**
         * @var int
         */
        private $range ;
        /**
         * @var array
         */
        private $skillUpgrades = [];
    
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

        public $cdStartTime = 0.0;


        /**
         * BaseSkill constructor.
         *
         * @param RPGPlayer $owner
         * @param string $name
         * @param string $type
         * @param int $cooldown
         * @param int $range
         * @param int $maxEntInRange
         * @param null $effect
         */
        public function __construct(RPGPlayer $owner, string $name , string $type = '', int $cooldown = 0, int $range = 0, int $maxEntInRange = 1, $effect = null )
        {

                $this->owner = $owner;
                $this->name = $name;
                $this->type = $type;
                $this->cooldown = $cooldown;
                $this->range = $range;
                $this->maxEntInRange = $maxEntInRange;
                $this->effect = $effect;

                $this->skillUpgrades = RPGLike::getInstance()->skillUnlocks[$this->getName()]['upgrades'];

        }
        public function getOwner() : RPGPlayer
        {
            return $this->owner;
        }
       public function getNamespace() : string
       {
           return $this->namespace;
       }
       public function isActive() : bool
       {
           if ($this->type = 'active')
           {
               return true;
           }else
           {
               return false;
           }
       }

        /**
         * @param string $name
         */
        public function setName(string $name) : void
        {
            $this->name = $name;
            $this->setDescription();
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
        public function setType(string $type) : void
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

        public function setDescription() : void
        {
            $description = RPGLike::getInstance()->getMessages()['Skills'][$this->getName()];
            $this->description = $description;
        }
    
        /**
         * @return array
         */
        public function getDescription() : array
        {
            return $this->description;
        }
        public function setCooldownTime(int $cooldown) : void
        {
            $this->cooldown = $cooldown;
        }
        /**
         * @return int
         */
        public function getCooldownTime() : int
        {
            return $this->cooldown;
        }
        public function setOnCooldown() : void
        {
            $this->onCooldown = true;
            RPGLike::getInstance()->getScheduler()->scheduleDelayedTask(new CooldownTask($this->owner, $this->getName()), $this->getCooldownTime());
            $this->cdStartTime = time();
        }
        public function isOnCooldown() : bool
        {
            return $this->onCooldown;
        }
        public function removeCooldown() : void
        {
            $this->onCooldown = false;
        }
        public function getRemainingCooldown() : float
        {
            $deltaTime = time() - $this->cdStartTime;
            $deltaTime %= 3600;
            return floor($deltaTime / 60);
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
         * @param array $baseUnlock
         */
        protected function setBaseUnlock(array $baseUnlock) : void
        {
            $this->skillUpgrades['unlock'] = $baseUnlock;
        }
        public function getBaseUnlock() : int
        {
            return $this->skillUpgrades['unlock'];
        }
        public function getSkillUpgrades() : array
        {
            return $this->skillUpgrades['upgrades'];
        }

        /**
         * Reqired array see reademe for more info on the required array model
         *
         * @param array $upgrades
         */
        protected function setUpgrades(array $upgrades) : void
        {
            $this->skillUpgrades['upgrades'] = $upgrades;
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

         * @param array              $func
         */
        public function checkRange(array $func = []) : void
        {
            if($this->range > 0) {
                $level = $this->owner->getLevel();
                $players = $this->getNearestEntities($this->owner->getPosition()->asVector3(), $this->range, $level, $this->getMaxEntInRange());
                if(!empty($players)){
                    foreach($players as $player) {
                        if(!empty($func)){
                            $this->setPlayerEffect($func);
                        }else{
                            $this->setPlayerEffect($this->getEffect());
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
         * @param callable|int       $effect
         */
        public function setPlayerEffect($effect) : void
        {
            if (!$this->onCooldown)
            {
                if(is_callable($effect)){
                    call_user_func($effect['objAndFunc'], $effect['params']);
                }else{
                    $effect = new EffectInstance(Effect::getEffect($effect), 2, $this->getSkillLevel());
                    $this->owner->addEffect($effect);
                }
            }

            
        }
        
    }