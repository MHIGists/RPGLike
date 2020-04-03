<?php
    /**
     * Created by PhpStorm.
     * User: Kirito
     * Date: 4/1/2020
     * Time: 10:28 PM
     */
    
    namespace TheClimbing\RPGLike;
    
    
    class Utils
    {
        static function get_class_name($classname)
        {
            if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
            return $pos;
        }
    }