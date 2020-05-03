<?php


namespace TheClimbing\RPGLike\Skills;

use function array_key_exists;
use function array_keys;

class SkillsManager
{
    public static $namespace = "\\TheClimbing\\RPGLike\\Skills\\";
    public static $skills = [
        "SkillName" => [
            "namespace" => "",
            "unlockConditions" => [
                "DEX" => 10,
            ]
        ]
    ];

    public static  function registerSkill(string $skillName, array $values) : void
    {
        self::$skills[$skillName] = $values;
    }
    public static function isSkillRegistered(string $skillName) : bool
    {
        return array_key_exists($skillName, self::$skills);
    }
    public static function getSkill(string $skillName) : array
    {
        return self::$skills[$skillName];
    }
    public static function getSkillMessages(string $skillName)
    {
        return self::$skills[$skillName]['messages'];//???
    }
    public static function getAvailableSkills() : array
    {
        return array_keys(self::$skills);
    }
}