<?php
/**
 * Created by PhpStorm.
 * User: 2000
 * Date: 20.08.2020
 * Time: 15:19
 */

namespace app\modules\parser\models;


use yii\base\Model;

class RegexHelper extends Model
{
    public static function getRussianSymbols($string)
    {
        preg_match_all("/[а-яё]/iu", trim($string), $matches);
        $res = (isset($matches[0])) ? implode($matches[0]) : false;

        return $res;
    }

    public static function getNumeral($string)
    {
        preg_match_all("/[0-9\.\,]/iu", trim($string), $matches);
        $res = (isset($matches[0])) ? implode($matches[0]) : false;

        return $res;
    }
}