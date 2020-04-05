<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 4/5/2020
     * Time: 11:50 PM
     */
    
    namespace TheClimbing\RPGLike\Skills;
    
    
    use TheClimbing\RPGLike\RPGLike;

    class Init
    {
        public $skills;
        public function __construct(RPGLike $rpg)
        {
            $skills = $rpg->main->getConfig()->getNested('Skills');
            foreach($skills as $skill) {
                try{
                    $skill = '\TheClimbing\RPGLike\Skills\\' . $skill;
                    $this->skills[] = new $skill($rpg);
                }catch(\Error $error){
                    $rpg->main->getLogger()->alert('No such skill: ' . $skill);
                }
            }
        }
        public function getSkills() : array
        {
            return $this->skills;
        }
        
    }