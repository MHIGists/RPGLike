<?php


namespace TheClimbing\RPGLike\Traits;


class BaseTrait
{
    private $name;
    private $levels;
    private $requirements;

    public function __construct(string $name, array $levels, $requirements)
    {
        $this->name = $name;
        $this->levels = $levels;
        $this->requirements = $requirements;
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
}