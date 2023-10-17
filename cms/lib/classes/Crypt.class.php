<?php
class Crypt {
    //sha256
    private static $salt = '477b3949c4.88ft477b3949c#488ftpp477b3949c488f@tp';

    private static function unique_salt() {
        return substr(sha1(mt_rand()),0,22);
    }

    public static function hash($password) {
        //$conf = self::$algorithm . self::$cost . '$' . self::unique_salt();
        $conf = self::unique_salt();
        return $conf . md5($conf . $password . self::$salt);
    }

    public static function check($hash, $password) {
        $full_salt = substr($hash, 0, 22);
        $new_hash = $full_salt . md5($full_salt . $password . self::$salt);

        if ($hash == $new_hash)
            return true;
        return false;
    }
}
?>
