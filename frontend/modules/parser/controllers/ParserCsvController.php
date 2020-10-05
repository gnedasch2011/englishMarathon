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
use app\modules\parser\models\ParserCsv;
use yii\web\Controller;

class ParserCsvController extends Controller
{
    public function actionIndex()
    {

        $file = 'csv/proverbs/categories2.csv';
        $delimetr = "\t";
        $delimetr = "	";

        (new ParserCsv())->open($file, true, $delimetr)->parse($delimetr, function ($data, ParserCsv $csv, $id) {

            $data['id'] = $id;
//
//            \Yii::$app->db->createCommand()->insert('proverbs.category', [
//                'id' => $data['id'],
//                'name' => $data['name'],
//            ])->execute();
//
//
//            if (isset($data['cat_ids'])) {
//                foreach (explode(',', $data['cat_ids']) as $catId) {
//                    \Yii::$app->db->createCommand()->insert('proverbs.proverbs_category', [
//                        'category_id' => $catId,
//                        'proverbs_id' => $data['id'],
//                    ])->execute();
//                }
//            }


        });

        die('ok');

    }

    public function findCountry($index_name)
    {

        $country = Country::find()
            ->where(['index_name' => trim($index_name)])
            ->one();

        if ($country) {
            return $country->id;
        }
        return false;
    }
}