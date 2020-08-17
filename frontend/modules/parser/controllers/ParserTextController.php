<?php
/**
 * Created by PhpStorm.
 * User: 2000
 * Date: 12.08.2020
 * Time: 11:37
 */

namespace app\modules\parser\controllers;


use app\models\residentname\City;
use app\models\residentname\Country;
use app\models\residentname\CountryInfoForMask;
use app\models\rhyme\HagenOrf;
use app\modules\parser\models\ParserCsv;
use yii\web\Controller;

class ParserTextController extends Controller
{
    public function actionIndex()
    {

        $fileName = 'hagen-orf.txt';

        $rows = file($fileName);

        //Поэтому более корректное определение рифмы:
        //слуховое совпадение ударного гласного и последующих за ним согласных звуков в окончании слов.


        //если ударение на последний слог, то берём две последние буквы

        $count = 0;
        $parentId = 1;
        $mainWord = 1;
        $id = 1;

        foreach ($rows as $row) {

            $val = mb_convert_encoding($row, 'utf-8', 'cp1251');
            preg_match_all("/\D/", $val, $matches);

            if (isset($matches[0]) && (count($matches[0])) < 5) {
                $parentId = $id;
                $mainWord = 1;
                continue;
            }


            $vals = explode('|', trim($val));
            $vals = array_map(function ($item) {
                return trim($item);
            }, $vals);


            $pos = mb_strpos($vals[1], '\'');
            $accent = mb_substr($vals[1], $pos - 1);


            $newHagenOrf = new HagenOrf();

            $newHagenOrf->id = $id;
            $newHagenOrf->parent_id = ($mainWord) ? 0 : $parentId;
            $newHagenOrf->word = $vals[0];
            $newHagenOrf->word_with_accent = $vals[1];
            $newHagenOrf->accent = $accent;

            if (!$newHagenOrf->save()) {
               echo '<pre>';print_r($newHagenOrf->errors);die();
            }

            if ($mainWord) {
                $mainWord = 0;
            }

            echo "<pre>";
            print_r($newHagenOrf->attributes);


            if ($count == 1000) {
                die();
            }


            $id++;
//            $count++;
        }


    }

}