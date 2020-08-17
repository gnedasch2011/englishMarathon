<?php

namespace app\modules\parser\controllers;


use app\models\residentname\CaseCityzen;
use app\models\residentname\Cases;
use app\models\residentname\City;
use app\models\residentname\CityDeclinesNouns;
use app\models\residentname\Country;
use app\models\residentname\CountryDeclinesNouns;
use app\models\residentname\DeclinedNouns;
use app\models\residentname\DeclinesNounsValue;
use app\models\residentname\NounseValue;
use app\models\residentname\Url;
use app\modules\parser\models\FileHelper;
use app\modules\parser\models\Parser;
use app\modules\parser\models\ParserUniversal;
use app\modules\parser\models\SaveItems;
use phpQuery;
use GuzzleHttp\Client;
use yii\helpers\Inflector;
use yii\web\Controller; // подключаем Guzzle



class ParserUniversalController extends Controller
{

    const DIR_IMG_PRODUCT = "/web/images/products/";
    const DIR_FILES_PRODUCT = "/web/files/products/";

    /**
     * Главное действо
     * @return string
     */
    public function actionIndex()
    {


        $config = [

            'host' => 'https://wikilivres.ru/',
            'uri' => '%D0%9E%D1%80%D1%84%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9_%D1%81%D0%BB%D0%BE%D0%B2%D0%B0%D1%80%D1%8C_%D1%80%D1%83%D1%81%D1%81%D0%BA%D0%BE%D0%B3%D0%BE_%D1%8F%D0%B7%D1%8B%D0%BA%D0%B0_(%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%BB%D0%B8%D1%87%D0%BD%D1%8B%D1%85_%D0%B8%D0%BC%D1%91%D0%BD)',

            'itemList' => [
                'listItems' => '.mw-parser-output',
                'itemBlock' => 'li',
            ],

            'forDetailPage' => [
                'name' => 'h1',
                'model' => '.bread__item:eq(3)',
                'price' => '.price:eq(0)',
                'priceAction' => '.old_price',
                'brand' => '.product_brand a',
                'imgMain' => [
                    'href' => '.product_image a'
                ],
                'attr' => [
                    'tableSelector' => '.features',
                    'rowSelector' => 'li',
                ],

            ],
        ];


        $parser = new ParserUniversal($config);

        $uls =  $parser->getItems();

        foreach ($uls as $ul) {
            $ul = pq($ul);
            
            echo "<pre>"; print_r($ul->text());
      }


    }


    public
    function returnIdCase($name_rus)
    {
        $cases = Cases::find()->asArray()->all();

        foreach ($cases as $case) {
            if ($name_rus == $case['name_rus']) {
                return $case['id'];
            }
        }

        return false;
    }


}