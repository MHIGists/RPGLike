<?php


namespace TheClimbing\RPGLike\Traits;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use TheClimbing\RPGLike\Players\RPGPlayer;

class BaseTrait
{
    private string $name;
    private array $blocks;
    private array $levels;
    private int $count = 0;
    private int $currentLevel = 0;
    private string $trait_action ;

    public function __construct(string $name, array $blocks, array $levels,string $trait_action, int $blockBreaks = 0)
    {
        $this->name = $name;
        $this->blocks = $blocks;
        $this->levels = $levels;
        $this->trait_action = $trait_action;
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
        $this->levels = $levels;
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
        if ($this->trait_action == 'break'){
            if (array_search($event->getBlock()->getName(), $this->blocks) !== false) {
                $this->count += 1;
                $this->checkLevel();
                $drops = $event->getDrops();
                $drop_chance = $this->getBlockDropChance();
                if ($drop_chance > 0 && $this->tryChance($drop_chance)){

                        $drops[] = $drops[0];
                        $event->setDrops($drops);
                        $event->getPlayer()->sendMessage('You just got 1 additional drop!');

                }
            }
        }
    }
    public function blockPickup(PlayerBlockPickEvent $event){
        if ($this->trait_action == "pickup"){
            $player = $event->getPlayer();
            if ($player instanceof RPGPlayer){
                if (array_search($event->getBlock()->getName(), $this->blocks) !== false) {
                    $this->count += 1;
                    $this->checkLevel();
                    $drop_chance = $this->getBlockDropChance();
                    if ($drop_chance > 0 && $this->tryChance($drop_chance)){
                            $event->getResultItem()->setCount($event->getResultItem()->getCount() + 1);
                            $event->getPlayer()->sendMessage('You just got 1 additional drop!');

                    }
                }
            }
        }
    }
    public function entityKill(EntityDamageByEntityEvent $event){
        if ($this->trait_action == "kill"){
            $damager = $event->getDamager();
            $damaged_target = $event->getEntity();
            $damaged_target_health = $damaged_target->getHealth();
            $damage = $event->getFinalDamage();
            if (($damaged_target_health - $damage) <= 0){
                if ($damager instanceof RPGPlayer){
                    $this->count += 1;
                    $this->checkLevel();
                    $drop_chance = $this->getBlockDropChance();
                    if ($drop_chance > 0 && $this->tryChance($drop_chance)){
                        $damager->spleft += 1;
                        $damager->sendMessage("You just got 1 skill point"); // TODO add economy support??
                    }
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

    public function getBlockDropChance(): int
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