<?php


namespace TheClimbing\RPGLike\Skills;

use TheClimbing\RPGLike\RPGLike;
use function array_key_exists;
use function array_keys;

class SkillsManager
{
    public static $defaultNamespace = "\\TheClimbing\\RPGLike\\Skills\\";

    public static $skills;

    public function __construct(RPGLike $rpg)
    {
        foreach ($rpg->getConfig()->getNested("Skills") as $key =>  $skill) {
            self::registerSkill($key, $skill);
        }
    }

    public static  function registerSkill(string $skillName, array $values) : void
    {
        self::$skills[$skillName] = $values;
        if (is_array($values)){
            if (array_key_exists("namespace", $values)){
                if (is_null($values['namespace']) || $values['namespace'] == "" || empty($values['namespace'])){

                    RPGLike::getInstance()->getLogger()->info("Skill: $skillName doesn't have namespace. Using default ones.");

                    self::$skills[$skillName]['namespace'] = self::$defaultNamespace;
                }
            }
            if (!array_key_exists("unlockConditions", $values)){
                RPGLike::getInstance()->getLogger()->info("Skill: $skillName doesn't have any unlock conditions.");
                throw new \Error("Please add unlock conditions");//TODO remove this

            }
//            print_r(self::$skills);

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
    public static function getSkills()
    {
        return self::$skills;
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