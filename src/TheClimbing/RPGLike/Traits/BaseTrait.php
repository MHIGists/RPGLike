<?php


namespace TheClimbing\RPGLike\Traits;


use JetBrains\PhpStorm\Pure;
use pocketmine\event\block\BlockBreakEvent;

class BaseTrait
{
    private string $name;
    private array $blocks;
    private array $levels;
    private int $count = 0;
    private int $currentLevel = 0;

    public function __construct(string $name, array $blocks, array $levels, int $blockBreaks = 0)
    {
        $this->name = $name;
        $this->blocks = $blocks;
        $this->levels = $levels;
        $this->restorePlayerTrait($blockBreaks);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    public function setLevels(array $levels): void
    {
        $this->levels = $levels; //TODO maybe add command to change settings on-demand
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }

    public function setBlocks(array $blocks)
    {
        $this->blocks = $blocks;
    }

    public function blockBreak(BlockBreakEvent $event)
    {
        if (array_search($event->getBlock()->getName(), $this->blocks) !== false) {
            $this->count += 1;
            $this->checkLevel();
            $drops = $event->getDrops();
            $drop_chance = $this->getBlockDropChance();
            if ($drop_chance > 0){
                if ($this->tryChance($drop_chance)){
                    $drops[] = $drops[0];
                    $event->setDrops($drops);
                    $event->getPlayer()->sendMessage('You just got 1 additional drop!');
                }
            }
        }
    }

    public function getBlockBreaks(): int
    {
        return $this->count;
    }

    public function getCurrentLevel(): int
    {
        return $this->currentLevel;
    }

    #[Pure] public function getBlockDropChance(): int
    {
        if ($this->getCurrentLevel() == 0){
            return 0;
        }
        return $this->levels[$this->getCurrentLevel()]['drop_chance'];
    }

    public function restorePlayerTrait(int $blockBreaks)
    {
        $this->count = $blockBreaks;
        $this->checkLevel();
    }
    public function checkLevel(){
        foreach ($this->levels as $key => $level) {
            if ($this->count > $level['requirement']) {
                $this->currentLevel = $key;
            }
        }
    }
    public function tryChance(int $drop_chance): bool
    {
        if (mt_rand(0, 99) < $drop_chance){
            return true;
        }
        return false;
    }
}