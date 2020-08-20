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
use app\modules\parser\models\RegexHelper;
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

            'host' => 'https://ru.wikipedia.org/wiki/Список_стран_по_населению',
            'uri' => '',
            'cache' => true,
            'selectors' => [
                'listItems' => '.standard',
                'itemBlock' => 'tbody tr',
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

        $items = $parser->getItems();

        $count = 0;
        die();

        foreach ($items as $item) {
            $item = pq($item);
            
            if($count>1){
                
                $name = $item->find('td:eq(1)')->text();
                $name = RegexHelper::getRussianSymbols($name);

                $populate = $item->find('td:eq(2)')->text();
                $populate = RegexHelper::getNumeral($populate);


                $percent_populate = $item->find('td:eq(4)')->text();
                $percent_populate = RegexHelper::getNumeral($percent_populate);
                

                $country = Country::find()
                    ->where(['name'=>$name])
                    ->one();


                if(isset($country)){
                    $country->populate=$populate;
                    $country->percent_populate=$percent_populate;

                    if(!$country->save()){
                        echo "<pre>"; print_r($country->errors);die();
                    }

                }


            }

            $count++;
        }

        die();
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