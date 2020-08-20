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
use app\models\rhyme\NamesOrf;
use app\models\rhyme\NounsMorf;
use app\modules\parser\models\ParserCsv;
use app\modules\parser\models\ParserText;
use yii\web\Controller;

class ParserTextController extends Controller
{
    public function actionIndex()
    {

        $fileName = 'txt/names.txt';

        $rows = ParserText::getRows($fileName);

        $limit = 10000;
        $offset = 0;

        $count  = 1000000 / 10000;

        for ($i = 0; $i < $count; $i++) {
            $nounsMorfs = \Yii::$app->db->createCommand('SELECT * FROM nouns_morf limit :limit offset :offset')
                ->bindValues([
                        ':limit' => $limit,
                        ':offset' => $offset,
                    ]
                )
                ->queryAll();


            foreach ($nounsMorfs as $nounsMorf) {
                \Yii::$app->db->createCommand()->update('hagen_orf', [
                    'gender' => $nounsMorf['gender'],
                    'wcase' => $nounsMorf['wcase']
                ], ['word' => $nounsMorf['word']])->execute();

            }

            $offset = $offset + 10000;
        }

        die();




        die('end');

        $HagenOrfs = HagenOrf::find()
            ->limit(100000)
            ->offset(2000)
            ->all();

        foreach ($HagenOrfs as $hagenOrf) {
            $nounsMorf = NounsMorf::find()
                ->where(['word' => $hagenOrf->word])
                ->asArray()
                ->one();

            if ($nounsMorf) {
                $hagenOrf->gender = $nounsMorf['gender'];
                $hagenOrf->wcase = $nounsMorf['wcase'];
                $hagenOrf->save();
            }
        }


        echo "<pre>";
        print_r($HagenOrfs);
        die();

        $id = 1;

        foreach ($rows as $row) {
            $ros = 'Селива\'н (Селива\'нович, Селива\'новна)';

            preg_match("/(?P<mainName>.*?)(?P<formsName>\(.*?\))/u", $row, $matches);

            if (is_array($matches) && empty($matches)) {
                preg_match("/(?P<mainName>.*)/u", $row, $matches);
            }


            $arrRes['mainName'] = $matches['mainName'] ?? '';
            $arrRes['formsName'] = $matches['formsName'] ?? '';

            if (!empty($arrRes['mainName'])) {
                //сделать массив из 3


                $pos = mb_strpos($arrRes['mainName'], '\'');
                $accent = mb_substr($arrRes['mainName'], $pos - 1);

                $NamesOrf = new NamesOrf();
                $NamesOrf->parent_id = 0;
                $NamesOrf->word = trim(str_replace('\'', '', $arrRes['mainName']));
                $NamesOrf->word_with_accent = $arrRes['mainName'];
                $NamesOrf->accent = $accent;
                $NamesOrf->save();

                $parent_id = $NamesOrf->id;

            }

            if (isset($arrRes['formsName']) && !empty($arrRes['formsName'])) {

                $formsName = str_replace(['(', ')'], '', $arrRes['formsName']);
                $formsNameArr = explode(', ', $formsName);

                foreach ($formsNameArr as $formsName) {
                    $NamesOrf = new NamesOrf();
                    $NamesOrf->parent_id = $parent_id;

                    $accent = mb_substr($formsName, $pos - 1);

                    $NamesOrf->word = trim(str_replace('\'', '', $formsName));
                    $NamesOrf->word_with_accent = $formsName;
                    $NamesOrf->accent = $accent;
                    $NamesOrf->save();
                }
            }

            $id++;
        }


    }

}