<?php

class Regex {

    public static function match($str, $regex) {
        if (!preg_match($regex, $str)) {
            return false;
        }
        return true;
    }

}
