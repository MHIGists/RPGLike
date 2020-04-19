<?php
    
    declare(strict_types = 1);
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function is_array;
    
    use TheClimbing\RPGLike\RPGLike;
    
    
    class SkillsManager
    {
        private $skills;
        public function __construct(RPGLike $rpg)
        {
            $configSkills = $rpg->getMain()->getConfig()->getNested('Skills');
            foreach($configSkills as $key => $skill) {
                try{
                    if(is_array($skill)){
                        $namespace = $skill[0] . $key;
                        $this->skills[$skill[0]] = new $namespace($rpg);
                    }else{
                        $namespace = "\\TheClimbing\\RPGLike\\Skills\\" . $skill;
                        $this->skills[$skill] = new $namespace($rpg);
                    }
                }catch(\Error $error){
                    $rpg->getMain()->getLogger()->alert('No such skill with namespace: ' . $namespace);
                }
            }
        }
        public function getSkills() : array
        {
            return $this->skills;
        }
        public function getSkill(string $skillName)
        {
            return $this->skills[$skillName];
        }
    }