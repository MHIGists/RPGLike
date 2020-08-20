<?php


namespace TheClimbing\RPGLike\Skills;

use function array_key_exists;
use function array_keys;
use function is_null;

use TheClimbing\RPGLike\RPGLike;


class SkillsManager
{
    public static $defaultNamespace = "\\TheClimbing\\RPGLike\\Skills\\";

    public static $skills;
    private static $main;

    public function __construct(RPGLike $rpg)
    {
        self::$main = $rpg;
        foreach ($rpg->config['Skills'] as $key => $skill) {
            self::registerSkill($key, $skill);
        }
    }

    public static function registerSkill(string $skillName, array $values): void
    {
        self::$skills[$skillName] = $values;

        if (array_key_exists("namespace", $values)) {
            if (is_null($values['namespace']) || $values['namespace'] == "" || empty($values['namespace'])) {
                self::$main->getLogger()->info("Skill: $skillName doesn't have namespace. Using default one.");
                self::$skills[$skillName]['namespace'] = self::$defaultNamespace . $skillName;
            } else {
                self::$skills[$skillName]['namespace'] = $values['namespace'];
            }
        }else{
            self::$main->getLogger()->info("Skill: $skillName doesn't have namespace. Using default one.");
            self::$skills[$skillName]['namespace'] = self::$defaultNamespace . $skillName;
        }
        if (array_key_exists($skillName, self::$main->getMessages()['Skills'])) {
            self::$skills[$skillName]["description"] = self::$main->getMessages()['Skills'][$skillName];
        }
    }

    public static function skillRegistered(string $skillName): bool
    {
        return array_key_exists($skillName, self::$skills);
    }

    public static function getSkill(string $skillName): array
    {
        return self::$skills[$skillName];
    }

    public static function getSkills()
    {
        return self::$skills;
    }

    public static function getSkillDescription(string $skillName)
    {
        return self::$skills[$skillName]['description'];
    }

    public static function getAvailableSkills(): array
    {
        return array_keys(self::$skills);
    }

    public static function getSkillNamespace(string $skillName)
    {
        return self::$skills[$skillName]['namespace'];
    }
}