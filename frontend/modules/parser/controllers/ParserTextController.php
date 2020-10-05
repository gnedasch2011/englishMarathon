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
use yii\debug\models\timeline\Search;
use yii\web\Controller;

class ParserTextController extends Controller
{
    public function actionIndex()
    {
        set_time_limit ( 60000);

        $fileName = 'txt/synonymys.txt';

        $rows = ParserText::getRows($fileName);

        foreach ($rows as $row) {
           
            $arr = explode('|', $row);
            $mainWord = array_shift($arr);
            
            //добавить слово, если его нет, если есть, то вернуть

            $wordMainId = $this->insertWord('synonyms.synonymys', ['name' => trim($mainWord)]);

            foreach ($arr as $word) {

                $id_relations_synonymys = $this->insertWord('synonyms.synonymys', ['name' => trim($word)]);
                 print_r($id_relations_synonymys);echo ',';
                \Yii::$app->db->createCommand()->insert('synonyms.relations', [
                    'id_synonymys' => $wordMainId,
                    'id_relations_synonymys' => $id_relations_synonymys,
                ])->execute();
            }
        }

        die('ok');
    }


    public function findRow($word, $dataBaseWithTable, $field)
    {
        $res = [];

        $res = \Yii::$app->db->createCommand('SELECT * FROM ' . $dataBaseWithTable . ' where ' . $field . ' = :' . $field)
            ->bindValues([
                    ':' . $field => trim($word),
                ]
            )
            ->queryOne();

        return $res;
    }

    /**
     * @param $dataBaseWithTable
     * @param $fieldValue array['name'=>'maks']
     * @return mixed|string
     * @throws \yii\db\Exception
     */
    public function insertWord($dataBaseWithTable, $fieldValue)
    {

        $findWord = $this->findRow(trim(array_values($fieldValue)[0]), $dataBaseWithTable, trim(array_keys($fieldValue)[0]));


        if ($findWord) {
            return $findWord['id'];
        }

        \Yii::$app->db->createCommand()->insert($dataBaseWithTable, $fieldValue)->execute();

        $lastId = \Yii::$app->db->getLastInsertID();

        return $lastId;
    }

}