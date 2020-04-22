<?php
    
    declare(strict_types=1);
    
    namespace TheClimbing\RPGLike\Players;
    
    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Skills\BaseSkill;
    use TheClimbing\RPGLike\Skills\SkillsManager;

    class RPGPlayer
    {
        private $playerName = '';
        
        private $skills = [];
        
        private $str = 1;
        private $strModifier = null;
        private $strBonus = 0;
        
        private $vit = 1;
        private $vitModifier = null;
        private $vitBonus = 1;
        
        private $def = 1;
        private $defModifier = null;
        private $defBonus = 1;
        
        private $dex = 1;
        private $dexModifier = null;
        private $dexBonus = 1;
        
        private $level = 1;
        
        private $skillsManager = null;
        
        public function __construct(string $playerName, array $modifiers, SkillsManager $skillsManager)
        {
            $this->playerName = $playerName;
            $this->setDEFModifier($modifiers['defModifier']);
            $this->setVITModifier($modifiers['vitModifier']);
            $this->setSTRModifier($modifiers['strModifier']);
            $this->setDEXModifier($modifiers['dexModifier']);
            $this->skillsManager = $skillsManager;
        }
        
        public function getName() : string
        {
            return $this->playerName;
        }
        
        public function setSTR(int $str) : void
        {
            $this->str = $str;
        }
        public function getSTR() : int
        {
            return $this->str;
        }
        public function setSTRModifier(float $strModifier)
        {
            $this->strModifier = $strModifier;
        }
        public function getSTRModifier() : float
        {
            return $this->strModifier;
        }
        public function calcSTRBonus()
        {
            $this->strBonus = $this->getSTR() * $this->getSTRModifier();
        }
        public function getSTRBonus() : float
        {
            return $this->strBonus;
        }
        public  function setVIT(int $vit) : void
        {
            $this->vit = $vit;
        }
        public function getVIT() : int
        {
            return $this->vit;
        }
        public function setVITModifier(float $vitModifier)
        {
            $this->vitModifier  = $vitModifier;
        }
        public function getVITModifier() : float
        {
            return $this->vitModifier;
        }
        public function calcVITBonus()
        {
            $this->vitBonus = $this->getVIT() * $this->getVITModifier();
        }
        public function getVITBonus() : int
        {
            return (int) ceil($this->vitBonus);
        }
        public function setDEX(int $dex) : void
        {
            $this->dex = $dex;
        }
        public function getDEX() : int
        {
            return $this->dex;
        }
        public function setDEXModifier(float $dexModifier)
        {
            $this->dexModifier = $dexModifier;
        }
        public function getDEXModifier() : float
        {
            return $this->dexModifier;
        }
        public function calcDEXBonus()
        {
            $this->dexBonus = $this->getDex() * $this->getDEXModifier();
        }
        public function getDEXBonus() : float
        {
            return $this->dexBonus;
        }
        public function setDEF(int $def) : void
        {
            $this->def = $def;
        }
        public function getDEF() : int
        {
            return $this->def;
        }
        public function setDEFModifier(float $defModifier)
        {
            $this->defModifier = $defModifier;
        }
        public function getDEFModifier() : float
        {
            return $this->defModifier;
        }
        public function calcDEFBonus()
        {
            $this->defBonus = $this->getDEF() * $this->getDEFModifier();
        }
        public function getDEFBonus() : float
        {
            return $this->defBonus;
        }
        public function setLevel(int $level)
        {
            $this->level = $level;
        }
        public function getLevel() : int
        {
            return $this->level;
        }
        public function unlockSkill(string $skillNamespace)
        {
            $this->skills[] = new $skillNamespace(RPGLike::getInstance());
        }
        public function getSkill(string $skilkName) : BaseSkill
        {
            $skill = $this->skills[$skilkName];
            if($skill instanceof BaseSkill)
            {
                return $skill;
            }
        }
        public function getSkills() : array
        {
            return $this->skills;
        }
        public function getSkillNames() : array
        {
            $skills = [];
            foreach($this->getSkills() as $skill) {
                $skills[] = $skill->getName();
            }
            return $skills;
        }
        public function getAttributes() : array
        {
            $temp = [
                'STR' => $this->getSTR(),
                'VIT' => $this->getVIT(),
                'DEF' => $this->getDEF(),
                'DEX' => $this->getDEX(),
            ];
            return $temp;
        }
        public function checkForSkills()
        {
        
        }
        public function reset()
        {
            $this->setLevel(1);
            
            $this->setDEX(1);
            $this->setSTR(1);
            $this->setVIT(1);
            $this->setDEF(1);
            
            $this->calcDEXBonus();
            $this->calcDEFBonus();
            $this->calcVITBonus();
            $this->calcSTRBonus();
        }
    }
    