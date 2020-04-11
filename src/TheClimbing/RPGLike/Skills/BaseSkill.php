<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 3/29/2020
     * Time: 4:59 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function floor;

    use pocketmine\Player;
    
    use pocketmine\entity\Effect;
    use pocketmine\entity\EffectInstance;
    
    use pocketmine\level\Level;
    use pocketmine\math\Vector3;
    
    use TheClimbing\RPGLike\RPGLike;
    
    class BaseSkill
    {
        public $rpg;
        public $name = '';
        public $type = '';
        public $description;
        public $cooldown = 0; //TODO implement this
        public $range = 0;
        public $baseUnlock = 10;
        public $upgrade = 30;// TODO implement this
        public $attribute = '';
        public $effect;
        
        
        public function __construct(RPGLike $rpg, string $name = '', string $type = '', string $description = '', int $cooldown = 0, int $range = 0, string $attribute = '',  $effect = null)
        {
            $this->name = $name;
            $this->type = $type;
            $this->description = $description;
            $this->cooldown = $cooldown;
            $this->range = $range;
            $this->attribute = $attribute;
            $this->effect = $effect;
            $this->rpg = $rpg;
            
        }
        
        public function getName() : string
        {
            return $this->name;
        }
        
        public function getType() : string
        {
            return $this->type;
        }
        
        public function getDescription() : string
        {
            return $this->description;
        }
        
        public function getCooldown() : int
        {
            return $this->cooldown;
        }
        
        public function getRange() : int
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
            if($this->rpg->playerHasSkill($this->getName(), $playerName) == false) {
                $this->rpg->players[$playerName]['skills'][] = $this->getName();
            }
        }
        
        public function getAttribute() : string
        {
            return $this->attribute;
        }
        
        public function getEffect()
        {
            return $this->effect;
        }
        
        
        public function isSkillUnlocked(string $playerName) : bool
        {
            if($this->baseUnlock <= $this->rpg->getPlayerAttribute($playerName, $this->getAttribute())) {
                return true;
            } else {
                return false;
            }
        }
        
        public function checkRange(Player $player, array $func = []) : void
        {
            if($this->range > 0) {
                $level = $player->getLevel();
                $players = $this->getNearestEntities($player->getPosition()->asVector3(), $this->range, $level, false);
                if(!empty($players)){
                    foreach($players as $player) {
                        if(!empty($func)){
                            $this->setEffect($player, $func);
                        }else{
                            $this->setEffect($player, $this->getEffect());
                        }
                    }
                }
            }
        }
        
        public function getNearestEntities(Vector3 $pos, ?int $maxDistance, Level $level, bool $includeDead = false) : array
        {
            $nearby = [];
            
            $minX = ((int) floor($pos->x - $maxDistance)) >> 4;
            $maxX = ((int) floor($pos->x + $maxDistance)) >> 4;
            $minZ = ((int) floor($pos->z - $maxDistance)) >> 4;
            $maxZ = ((int) floor($pos->z + $maxDistance)) >> 4;
            
            $currentTargetDistSq = $maxDistance ** 2;
            
            for($x = $minX ; $x <= $maxX ; ++$x) {
                for($z = $minZ ; $z <= $maxZ ; ++$z) {
                    foreach((($chunk = $level->getChunk($x, $z)) !== null ? $chunk->getEntities() : []) as $entity) {
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
        
        public function setEffect(Player $player, $effect) : void
        {
            if(is_callable($effect)){
                call_user_func($effect['objAndFunc'], $effect['params']);
            }else{
                $effect = new EffectInstance(Effect::getEffect($effect), 2, 1);
                $player->addEffect($effect);
            }
            
        }
        
        
    }