<?php

class emailobfuscator
{
    private static $whitelist = [];

    public static function whitelistEmail($email)
    {
        self::$whitelist[] = $email;
    }

    public static function encodeEmailLinks($matches)
    {
        $mail = $matches[1];
        $mail = str_rot13($mail); // ROT13-Transformation
        $mail = str_replace('@', '#', $mail); // Ersetze @ durch #, um E-Mailadressen von weiteren RegEx auszuschließen

        return 'javascript:decryptUnicorn(' . $mail . ')';
    }

    public static function encodeEmail($matches)
    {
        if (($_SERVER['REQUEST_METHOD'] == 'POST' && self::in_array_r($matches[0], $_POST)) || self::in_array_r($matches[0], self::$whitelist)) {
            return $matches[0];
        }
        return $matches[1] . '<span class=unicorn><span>_at_</span></span>' . $matches[2];
    }

    private static function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }
}