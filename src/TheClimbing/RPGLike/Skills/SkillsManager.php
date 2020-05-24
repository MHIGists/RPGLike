<?php


namespace TheClimbing\RPGLike\Skills;

use function is_array;
use function array_key_exists;
use function array_keys;
use function is_null;

use TheClimbing\RPGLike\RPGLike;


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

                    RPGLike::getInstance()->getLogger()->info("Skill: $skillName doesn't have namespace. Using default one.");

                    self::$skills[$skillName]['namespace'] = self::$defaultNamespace;
                    $values['namespace'] = self::$defaultNamespace;
                }else{
                    self::$skills[$skillName]['namespace'] = $values['namespace'];
                }
            }
            if (array_key_exists($skillName, RPGLike::$messages['Skills']))
            {
                self::$skills[$skillName]["description"] = RPGLike::$messages['Skills'][$skillName];
            }
            $namespace = $values['namespace'] . $skillName;

            $skill = new $namespace('', '', true);
            self::$skills[$skillName]['unlockConditions'] = $skill->getBaseUnlock();
            unset($skill);
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
    public static function getSkillDescription(string $skillName)
    {
        return self::$skills[$skillName]['description'];
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