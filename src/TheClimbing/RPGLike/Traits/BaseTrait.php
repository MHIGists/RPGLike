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

    public function __construct(string $name, array $blocks, array $levels, int $blockBreaks)
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
        if (array_search($event->getBlock()->getName(), $this->blocks) != false) {
            $this->count += 1;
            $drops = $event->getDrops();
            $drops[] = $drops[mt_rand(0, 99) < $this->getBlockDropChance()];
            $event->setDrops($drops);
            $event->getPlayer()->sendMessage('You just got 1 additional drop!');
        }
    }

    public function getBlockBreaks(): int
    {
        return $this->count; //TODO add block saves
    }

    public function getCurrentLevel(): int
    {
        return $this->currentLevel;
    }

    #[Pure] public function getBlockDropChance(): int
    {
        return $this->levels[$this->getCurrentLevel()]['drop_chance'];
    }

    private function restorePlayerTrait(int $blockBreaks)
    {
        $this->count = $blockBreaks;
        foreach ($this->levels as $key => $level) {
            if ($this->count > $level['requirement']) {
                $this->currentLevel = $key;
            }
        }
    }
}