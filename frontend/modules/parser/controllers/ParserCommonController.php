<?php

namespace app\modules\parser\controllers;


use app\modules\parser\models\Parser;
use app\modules\parser\models\SaveItems;
use phpQuery;
use GuzzleHttp\Client;
use yii\web\Controller; // подключаем Guzzle

class ParserCommonController extends Controller
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
            'host' => 'https://wikilivres.ru/%D0%9E%D1%80%D1%84%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9_%D1%81%D0%BB%D0%BE%D0%B2%D0%B0%D1%80%D1%8C_%D1%80%D1%83%D1%81%D1%81%D0%BA%D0%BE%D0%B3%D0%BE_%D1%8F%D0%B7%D1%8B%D0%BA%D0%B0_(%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%BB%D0%B8%D1%87%D0%BD%D1%8B%D1%85_%D0%B8%D0%BC%D1%91%D0%BD)',
            'uri' => '',

            'forCategory' => [
                'listItems' => '.mw-parser-output',
                'itemBlock' => 'li',
            ],

            'forDetail' => [
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


        $parser = new Parser($config);


//        $resCache = \Yii::$app->cache;
//        $items = $resCache->get('resParser');

        echo "<pre>";
        print_r($parser->getItems((object)$config));
        die();


        if (empty($items)) {
            $forCache = $parser->getItemsInCategory("catalog/massazhnye-kresla");
//            $resCache->set('resParser', $forCache);
        }


        $saveItem = new SaveItems();


        //детальная страница
//        $parser->uri = "/categories/jet/";
//        $parser->getDetailItem($config);


        foreach ($items as $item) {
            if ($saveItem::saveItem($item, $parser->host)) {
                continue;
            } else {
                echo $saveItem::saveItem($item, $parser->host);
            }
        }

    }

}