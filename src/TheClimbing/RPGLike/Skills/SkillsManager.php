<?php


namespace TheClimbing\RPGLike\Skills;

use TheClimbing\RPGLike\RPGLike;
use function array_key_exists;
use function array_keys;

class SkillsManager
{
    public static $defaultNamespace = "\\TheClimbing\\RPGLike\\Skills\\";
//    public static $skills = [
//        "SkillName" => [
//            "namespace" => "",
//            "unlockConditions" => [
//                "DEX" => 10,
//            ]
//        ]
//    ];
public static $skills;

    public static  function registerSkill(string $skillName, array $values) : void
    {
        self::$skills[$skillName] = $values;
        if (is_array($values)){
            if (!array_key_exists("namespace", self::$skills)){
                RPGLike::getInstance()->getLogger()->error("Skill: $skillName doesn't have namespace. Using default ones.");
                self::$skills['namespace'] = self::$defaultNamespace;
            }
            if (!array_key_exists("unlockConditions", self::$skills)){
                RPGLike::getInstance()->getLogger()->error("Skill: $skillName doesn't have any unlock conditions. Using default ones.");
                self::$skills["unlockConditions"] = [10];
            }
        }
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
    public static function getSkillNamespace(string $skillName)
    {
        return self::$skills[$skillName]['namespace'];
    }
}