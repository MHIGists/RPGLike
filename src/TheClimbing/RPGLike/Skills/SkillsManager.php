<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function is_array;

    use TheClimbing\RPGLike\RPGLike;
    
    class SkillsManager
    {
        private static $skills;
        private static $instance;
        
        public function __construct(RPGLike $rpg)
        {
            self::$instance = $this;
            
            $configSkills = $rpg->getConfig()->getNested('Skills');
            foreach($configSkills as $key => $skill) {
                try{
                    if(is_array($skill)){
                        $namespace = $skill[0] . $key;
                        self::$skills[$skill[0]] = new $namespace();
                    }else{
                        $namespace = "\\TheClimbing\\RPGLike\\Skills\\" . $skill;
                        self::$skills[$skill] = new $namespace();
                    }
                }catch(\Error $error){
                    $rpg->getLogger()->alert('No such skill with namespace: ' . $namespace);
                    $rpg->getLogger()->alert($error->getMessage());
                }
            }
        }
        public static function getSkills() : array
        {
            return self::$skills;
        }
        public static function getSkill(string $skillName)
        {
            return self::$skills[$skillName];
        }
        public static function getInstance() : SkillsManager
        {
            return self::$instance;
        }
    }