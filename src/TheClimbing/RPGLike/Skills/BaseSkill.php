<?php

declare(strict_types=1);


namespace TheClimbing\RPGLike\Skills;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use TheClimbing\RPGLike\Players\RPGPlayer;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Tasks\CooldownTask;

use function array_slice;
use function call_user_func;
use function floor;
use function is_callable;

/**
 * Class BaseSkill
 * @package TheClimbing\RPGLike\Skills
 */
class BaseSkill
{
    protected RPGPlayer $owner;

    private string $name;

    private string $type;

    private array $messages;

    private int $cooldown;

    private bool $onCooldown = false;

    private int $range;

    private array $skillLevels;

    private bool $isAOE = false;

    private ?int $effect;

    private int $skillLevel = 0;

    public float $cdStartTime = 0.0;

    private bool $unlocked = false;


    /**
     * BaseSkill constructor.
     *
     * @param RPGPlayer $owner
     * @param string $name
     * @param array $skillLevels
     * @param string $type
     * @param int $cooldown
     * @param int $range
     * @param bool $isAOE
     * @param null $effect
     */
    #[Pure] public function __construct(RPGPlayer $owner, string $name, array $skillLevels, string $type = '', int $cooldown = 0, int $range = 0, bool $isAOE = false, $effect = null)
    {
        $messages = RPGLike::getInstance()->getMessages()['Skills'];
        $this->owner = $owner;
        $this->name = $name;
        $this->type = $type;
        $this->cooldown = $cooldown;
        $this->range = $range;
        $this->effect = $effect;

        $this->skillLevels = $skillLevels;
        $this->messages = $messages[$this->getName()];
    }

    public function unlock(): void
    {
        $this->unlocked = true;
    }

    public function isUnlocked(): bool
    {
        return $this->unlocked;
    }

    public function reset()
    {
        $this->unlocked = false;
        $this->skillLevel = 1;
    }

    public function checkLevel()
    {
        foreach ($this->skillLevels as $key => $value) {
            $criteria = count($value['unlock']);
            $met_criteria = 0;
            foreach ($value['unlock'] as $key1 => $item) {
                if ($this->owner->getAttribute($key1) >= $item){
                    $met_criteria++;
                }
            }
            if ($criteria <= $met_criteria){
                $this->skillLevel = $key;
            }
        }
    }

    public function isActive(): bool
    {
        if ($this->type = 'active') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    public function setCooldownTime(int $cooldown): void
    {
        $this->cooldown = $cooldown;
    }

    /**
     * @return int
     */
    public function getCooldownTime(): int
    {
        return $this->cooldown;
    }

    public function setOnCooldown(): void
    {
        $this->onCooldown = true;
        RPGLike::getInstance()->getScheduler()->scheduleDelayedTask(new CooldownTask($this->owner, $this->getName()), $this->getCooldownTime());
        $this->cdStartTime = time();
    }

    public function isOnCooldown(): bool
    {
        return $this->onCooldown;
    }

    public function removeCooldown(): void
    {
        $this->onCooldown = false;
    }

    public function getRemainingCooldown(string $format): string
    {
        $timediff = time() - $this->cdStartTime;
        $cdsecs = ($this->getCooldownTime() / 20) - $timediff;
        $cdmins = $cdsecs / 60;
        if ($cdmins < 1) {
            $cdmins = 0;
        }
        $cdhours = $cdmins / 60;
        if ($cdhours < 1) {
            $cdhours = 0;
        }
        $format = str_split($format);
        foreach ($format as $key => $item) {
            if ($item == 'H') {
                $format[$key] = round($cdhours, PHP_ROUND_HALF_DOWN);
            }
            if ($item == 'M') {
                $format[$key] = round($cdmins, PHP_ROUND_HALF_DOWN);
            }
            if ($item == 'S') {
                $format[$key] = round($cdsecs, PHP_ROUND_HALF_DOWN);
            }
        }
        return implode('', $format);
    }
    public function isAOE() : bool{
        return $this->isAOE;
    }
    protected function setRange(int $range): void
    {
        $this->range = $range;
    }

    /**
     * @return int
     */
    public function getRange(): int
    {
        return $this->range;
    }


    public function getBaseUnlock(): array
    {
        return $this->skillLevels[1]['unlock'];
    }

    /**
     * @param $id
     */
    protected function setEffect($id): void
    {
        $this->effect = $id;
    }

    /**
     * @return int|null
     */
    public function getEffect(): ?int
    {
        return $this->effect;
    }

    /**
     * @return int
     */
    public function getSkillLevel(): int
    {
        return $this->skillLevel;
    }


    /**
     * @param array $func
     */
    public function checkRange(array $func = []): void
    {
        if ($this->range > 0) {
            $level = $this->owner->getLevel();
            $players = $this->getNearestEntities($this->owner->getPosition()->asVector3(), $this->range, $level, $this->getMaxEntInRange());
            if (!empty($players)) {
                foreach ($players as $player) {
                    if (!empty($func)) {
                        $this->setPlayerEffect($func);
                    } else {
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
     * @param int|null $maxDistance
     * @param Level $level
     * @param int $maxEntities
     * @param bool $includeDead
     *
     * @return array
     */
    public function getNearestEntities(Vector3 $pos, ?int $maxDistance, Level $level, int $maxEntities, bool $includeDead = false): array
    {
        $nearby = [];

        $minX = ((int)floor($pos->x - $maxDistance)) >> 4;
        $maxX = ((int)floor($pos->x + $maxDistance)) >> 4;
        $minZ = ((int)floor($pos->z - $maxDistance)) >> 4;
        $maxZ = ((int)floor($pos->z + $maxDistance)) >> 4;

        $currentTargetDistSq = $maxDistance ** 2;

        for ($x = $minX; $x <= $maxX; ++$x) {
            for ($z = $minZ; $z <= $maxZ; ++$z) {
                $entities = ($chunk = $level->getChunk($x, $z)) !== null ? $chunk->getEntities() : [];
                if (count($entities) > $maxEntities) {
                    $entities = array_slice($entities, 0, $maxEntities);
                }
                foreach ($entities as $entity) {
                    if (!($entity instanceof Player) or $entity->isClosed() or $entity->isFlaggedForDespawn() or (!$includeDead and !$entity->isAlive())) {
                        continue;
                    }
                    $distSq = $entity->distanceSquared($pos);
                    if ($distSq < $currentTargetDistSq) {
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
     * @param callable|int $effect
     */
    public function setPlayerEffect(callable|int $effect): void
    {
        if (!$this->onCooldown) {
            if (is_callable($effect)) {
                call_user_func($effect['objAndFunc'], $effect['params']);
            } else {
                $effect = new EffectInstance(Effect::getEffect($effect), 2, $this->getSkillLevel());
                $this->owner->addEffect($effect);
            }
        }
    }
    public function getSkillDescription() : string
    {
        return $this->messages['description'];
    }
    public function getSkillUnlockConditions() : string
    {
        return $this->messages['unlocks'];
    }
    public function getSkillProcMessage(){
        return $this->messages['proc_message'];
    }
    public function transmitProcMessage(RPGPlayer $player){
        $player->sendMessage($this->getSkillProcMessage());
    }

}