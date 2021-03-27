<?php


namespace App\Utils;


class StringUtils {

    public static function isBlank($secStr) {
        if (!isset($secStr) || $secStr == null || $secStr == "") {
            return true;
        }
        if (is_array($secStr) && count($secStr) == 0) {
            return true;
        }

        if (is_string($secStr) && trim($secStr) == "") {
            return true;
        }
        return false;
    }
}
