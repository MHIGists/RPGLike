<?php

declare(strict_types=1);

namespace TheClimbing\RPGLike\Players;

use pocketmine\entity\Attribute;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

use TheClimbing\RPGLike\Forms\RPGForms;
use TheClimbing\RPGLike\RPGLike;
use TheClimbing\RPGLike\Skills\BaseSkill;

class RPGPlayer extends Player
{


    /* @var BaseSkill[] */
    private $skills = [];
    private $traits = [];

    private $str = 1;
    private $strModifier = 0.15;
    private $strBonus = 0;

    private $vit = 1;
    private $vitModifier = 0.175;
    private $vitBonus = 1;

    private $def = 1;
    private $defModifier = 0.1;
    private $defBonus = 1;

    private $dex = 1;
    private $dexModifier = 0.0002;
    private $dexBonus = 1;

    private $config;

    public $movementSpeed = 0;
    public $spleft = 0;
    public $xplevel = 0;

    public function __construct($interface, $ip, $port)
    {
        parent::__construct($interface, $ip, $port);

        $this->config = RPGLike::getInstance()->getConfig();
        $modifiers = $this->getModifiers();
        if ($modifiers != false) {
            $this->setDEFModifier($modifiers['DEF']);
            $this->setVITModifier($modifiers['VIT']);
            $this->setSTRModifier($modifiers['STR']);
            $this->setDEXModifier($modifiers['DEX']);
        }

        $this->calcVITBonus();
        $this->calcDEXBonus();
    }

    /* @return array|false */
    public function getModifiers()
    {
        $modifiers = $this->config->getNested('modifiers');
        if ($modifiers !== null) {
            return $modifiers;
        } else {
            return false;
        }

    }

    public function triggerSkills(): void
    {
        if (!empty($this->skills)) {
            foreach ($this->skills as $skill) {
                if ($skill->isActive()) {
                    continue;
                }
                $function = get_class_methods($skill);

                $skill->$function[0]($this);
            }
        }
    }

    public function setSTR(int $str): void
    {
        $this->str = $str;
        $this->calcSTRBonus();
    }

    public function getSTR(): int
    {
        return $this->str;
    }

    public function setSTRModifier(float $strModifier): void
    {
        $this->strModifier = $strModifier;
    }

    public function getSTRModifier(): float
    {
        return $this->strModifier;
    }

    public function calcSTRBonus(): void
    {
        $this->strBonus = $this->getSTR() * $this->getSTRModifier();
    }

    public function getSTRBonus(): float
    {
        return $this->strBonus;
    }

    public function setVIT(int $vit): void
    {
        $this->vit = $vit;
        $this->calcVITBonus();
    }

    public function getVIT(): int
    {
        return $this->vit;
    }

    public function setVITModifier(float $vitModifier): void
    {
        $this->vitModifier = $vitModifier;
    }

    public function getVITModifier(): float
    {
        return $this->vitModifier;
    }

    public function calcVITBonus(): void
    {
        $this->vitBonus = $this->getVIT() * $this->getVITModifier();
    }

    public function getVITBonus(): int
    {
        return (int)ceil($this->vitBonus);
    }

    public function setDEX(int $dex): void
    {
        $this->dex = $dex;
        $this->calcDEXBonus();
        $this->applyDexterityBonus();
    }

    public function getDEX(): int
    {
        return $this->dex;
    }

    public function setDEXModifier(float $dexModifier): void
    {
        $this->dexModifier = $dexModifier;
    }

    public function getDEXModifier(): float
    {
        return $this->dexModifier;
    }

    public function calcDEXBonus(): void
    {
        $this->dexBonus = $this->getDex() * $this->getDEXModifier();
    }

    public function getDEXBonus(): float
    {
        return $this->dexBonus;
    }

    public function setDEF(int $def): void
    {
        $this->def = $def;
        $this->calcDEFBonus();
    }

    public function getDEF(): int
    {
        return $this->def;
    }

    public function setDEFModifier(float $defModifier)
    {
        $this->defModifier = $defModifier;
    }

    public function getDEFModifier(): float
    {
        return $this->defModifier;
    }

    public function calcDEFBonus(): void
    {
        $this->defBonus = $this->getDEF() * $this->getDEFModifier();
    }

    public function getDEFBonus(): float
    {
        return $this->defBonus;
    }

    public function unlockSkill(string $skillNamespace, string $skillName, bool $form = true): void
    {
        $this->skills[$skillName] = new $skillNamespace($this);
        if ($form) {
            RPGForms::skillHelpForm($this, $skillName);
        }
    }

    public function getSkill(string $skillName): ?BaseSkill
    {
        if ($this->hasSkill($skillName)) {
            return $this->skills[$skillName];
        } else {
            return null;
        }
    }

    public function hasSkill(string $skillName): bool
    {
        if (isset($this->skills[$skillName])) {
            return true;
        } else {
            return false;
        }
    }

    /* @return  BaseSkill[] */
    public function getSkills()
    {
        return $this->skills;
    }

    /* @return string[] */
    public function getSkillNames(): array
    {
        return array_keys($this->skills);
    }

    public function getAttributes(): array
    {
        return [
            'STR' => $this->getSTR(),
            'VIT' => $this->getVIT(),
            'DEF' => $this->getDEF(),
            'DEX' => $this->getDEX(),
        ];
    }

    public function getAttribute(string $attribute): int
    {
        return $this->getAttributes()[$attribute];
    }

    public function checkForSkills()
    {
        foreach (RPGLike::getInstance()->skillUnlocks as $skillName => $skillArray) {
            if ($this->hasSkill($skillName))
            {
                continue;
            }
            foreach ($skillArray['unlock'] as $key1 => $value) {

                if (is_array($value)) {
                    $req = 0;
                    foreach ($value as $key2 => $value1) {
                        if ($this->getAttribute($key2) >= $value1) {
                            $req += 1;
                        }
                    }
                    if ($req == count($value))
                    {
                        $namespace = RPGLike::getInstance()->getSkill($skillName)['namespace'];
                        $this->unlockSkill($namespace, $skillName);
                    }

                } else {
                    if ($this->getAttribute($key1) >= $value) {
                        $namespace = RPGLike::getInstance()->getSkill($skillName)['namespace'];
                        $this->unlockSkill($namespace, $skillName);
                    }
                }
            }

        }
    }

    public function setSPleft(int $spleft)
    {
        $this->spleft = $spleft;
    }

    public function getSPleft()
    {
        return $this->spleft;
    }

    public function resetSkills()
    {
        $this->skills = [];
    }

    public function applyDamageBonus(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        if ($damager instanceof RPGPlayer) {
            $baseDamage = $event->getBaseDamage();
            $event->setBaseDamage($baseDamage + $damager->getSTRBonus());
        }

    }

    public function applyVitalityBonus()
    {
        $this->setMaxHealth(20 + $this->getVITBonus());
        $this->setHealth(20 + $this->getVITBonus());
    }

    public function applyDefenseBonus(EntityDamageByEntityEvent $event): void
    {
        $receiver = $event->getEntity();
        if ($receiver instanceof RPGPlayer) {
            $receiver->setAbsorption($receiver->getAbsorption() + $receiver->getDEFBonus());

        }
    }

    public function applyDexterityBonus()
    {
        $dex = $this->getDEXBonus();
        $movement = $this->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
        $movement->setValue($movement->getValue() * (1 + $dex));
        $this->movementSpeed = $movement->getValue() * (1 + $dex);
    }

    public function getHealthRegenBonus()
    {
        return floor($this->getVITBonus() / 3);
    }

    public function savePlayerVariables(): void
    {
        $playerVars = [
            'attributes' => $this->getAttributes(),
            'skills' => $this->getSkillNames(),
            'spleft' => $this->getSPleft(),
            'level' => $this->getXPLevel(),
        ];
        $players = $this->config->getNested('Players');
        $players[$this->getName()] = $playerVars;
        $this->config->setNested('Players', $players);
        $this->config->save();
    }

}
    