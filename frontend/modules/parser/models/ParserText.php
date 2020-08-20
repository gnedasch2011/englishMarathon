<?php
/**
 * Created by PhpStorm.
 * User: 2000
 * Date: 14.08.2020
 * Time: 12:30
 */

namespace app\modules\parser\models;


use yii\base\Model;

class ParserText extends Model
{
    public static function getRows($fileName)
    {
        $rows = file($fileName);
        return $rows;
    }
}