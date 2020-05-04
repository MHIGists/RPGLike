<?php
    
    declare(strict_types=1);
    
    namespace TheClimbing\RPGLike\Players;
    
    use TheClimbing\RPGLike\Forms\RPGForms;
    use TheClimbing\RPGLike\RPGLike;
    use TheClimbing\RPGLike\Skills\BaseSkill;
    use TheClimbing\RPGLike\Skills\SkillsManager;

    class RPGPlayer
    {
        private $playerName;

        /* @var BaseSkill[] */
        private $skills = [];
        
        private $str = 1;
        private $strModifier = 1.0;
        private $strBonus = 0;
        
        private $vit = 1;
        private $vitModifier = 1.0;
        private $vitBonus = 1;
        
        private $def = 1;
        private $defModifier = 1.0;
        private $defBonus = 1;
        
        private $dex = 1;
        private $dexModifier = 1.0;
        private $dexBonus = 1;
        
        private $level = 1;

        public $spleft = 0;
        
        public function __construct(string $playerName, array $modifiers)
        {
            $this->playerName = $playerName;
            $this->setDEFModifier($modifiers['defModifier']);
            $this->setVITModifier($modifiers['vitModifier']);
            $this->setSTRModifier($modifiers['strModifier']);
            $this->setDEXModifier($modifiers['dexModifier']);
        }
        
        public function getName() : string
        {
            return $this->playerName;
        }
        
        public function setSTR(int $str) : void
        {
            $this->str = $str;
            $this->calcSTRBonus();;
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
            $this->calcVITBonus();;
            RPGLike::getInstance()->applyVitalityBonus(PlayerManager::getServerPlayer($this->getName()));
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
            $this->calcDEXBonus();
            RPGLike::getInstance()->applyDexterityBonus(PlayerManager::getServerPlayer($this->getName()));
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
            $this->calcDEFBonus();
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
        public function unlockSkill(string $skillNamespace, string $skillName, bool $form = true)
        {
            $skill = $skillNamespace . $skillName;
            $this->skills[$skillName] = new $skill($this->getName(), $skillNamespace);
            if($form){
                RPGForms::descriptionSkillForm(PlayerManager::getServerPlayer($this->getName()), $this->skills[$skillName]->getDescription());
            }
        }
        /* @return BaseSkill */
        public function getSkill(string $skillName)
        {
            return $this->skills[$skillName];
        }
        /* @return  BaseSkill[] */
        public function getSkills()
        {
            return $this->skills;
        }
        /* @return string[] */
        public function getSkillNames() : array
        {
            return array_keys($this->skills);
        }
        public function getAttributes() : array
        {
            return [
                'STR' => $this->getSTR(),
                'VIT' => $this->getVIT(),
                'DEF' => $this->getDEF(),
                'DEX' => $this->getDEX(),
            ];
        }
        public function getAttribute(string $attribute) : int
        {
            return $this->getAttributes()[$attribute];
        }
        public function checkForSkills()
        {
            foreach(SkillsManager::getSkills() as $key =>  $skill) {
                foreach($skill['unlockConditions'] as $key1 => $value){
                    if($this->getAttribute($key1) >= $value){
                        $namespace = $skill['namespace'];
                        if(array_key_exists($key, $this->skills) == false){
                            $this->unlockSkill($namespace, $key);
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
            
            $this->skills = [];
            
            $player = PlayerManager::getServerPlayer($this->getName());
            RPGLike::getInstance()->applyDexterityBonus($player);
            RPGLike::getInstance()->applyVitalityBonus($player);
        }
    }
    