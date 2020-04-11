<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 4/5/2020
     * Time: 11:50 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    use function is_array;
    
    use TheClimbing\RPGLike\RPGLike;

    class SkillsManager
    {
        public $skills;
        public function __construct(RPGLike $rpg)
        {
            $skills = $rpg->main->getConfig()->getNested('Skills');
            foreach($skills as $key => $skill) {
                try{
                    if(is_array($skill)){
                        $namespace = $skill[0] . $key;
                        $this->skills[$skill[0]] = new $namespace($rpg);
                    }else{
                        $namespace = "TheClimbing\RPGLike\Skills\\" . $skill;
                        $this->skills[$skill] = new $namespace($rpg);
                    }
                }catch(\Error $error){
                    $rpg->main->getLogger()->alert('No such skill with namespace: ' . $skill);
                }
            }
        }
        public function getSkills() : array
        {
            return $this->skills;
        }
        public function getSkill(string $skillName) : object
        {
            return $this->skills[$skillName];
        }
    }