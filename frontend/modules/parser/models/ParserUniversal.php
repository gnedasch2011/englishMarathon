<?php

namespace app\modules\parser\models;


use phpQuery;
use GuzzleHttp\Client;
use yii\base\Model; // подключаем Guzzle

class ParserUniversal extends Model
{

//    const SITE_URL = "http://massage-kresla.loc";

    const DIR_IMG_PRODUCT = "/web/images/products/";
    const DIR_FILES_PRODUCT = "/web/files/products/";

    public $host;
    public $uri;
    public $config;
    public $body;


    public function __construct($config)
    {

        $this->host = $config['host'];
        $this->uri = $config['uri'];

        $this->config = (object)$config;
        $this->body = phpQuery::newDocumentFileXHTML('https://wikilivres.ru/%D0%9E%D1%80%D1%84%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9_%D1%81%D0%BB%D0%BE%D0%B2%D0%B0%D1%80%D1%8C_%D1%80%D1%83%D1%81%D1%81%D0%BA%D0%BE%D0%B3%D0%BE_%D1%8F%D0%B7%D1%8B%D0%BA%D0%B0_(%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA_%D0%BB%D0%B8%D1%87%D0%BD%D1%8B%D1%85_%D0%B8%D0%BC%D1%91%D0%BD)');
    }

    public function getItems()
    {
        return $this->body->find($this['config']->itemList['listItems']);

    }



    public function init()
    {
        parent::init();

    }

    /***
     * Инфа с детальной страницы
     * @param $config
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDetailItem($config)
    {
        $config = (object)$config;
        $this->host = $config->host;
        $arrAttrItem = [];
        // создаем экземпляр класса
        $document = $this->initPHPQ();
        //имя

        $arrAttrItem['name'] = $document->find($config->forDetail['name'])->text();

        //Price
        $price = $document->find($config->forDetail['price']);
        $arrAttrItem['price'] = preg_replace("/[^0-9]/", '', $price->text());

        $priceAction = $document->find($config->forDetail['priceAction']);
        $arrAttrItem['priceAction'] = preg_replace("/[^0-9]/", '', $priceAction->text());


        $arrAttrItem['uri'] = $this->uri;
        $arrAttrItem['host'] = $this->host;

        //brand
        $arrAttrItem['brand'] = $document->find($config->forDetail['brand'])->text();

        //model
        $arrAttrItem['model'] = $document->find($config->forDetail['model'])->text();

        //preview
        //$arrAttrItem['preview'] = $document->find(".gp-brief")->text();

        //description
//        $description = $document->find(".tabs-body");
//        $description->find('ul')->remove('.uf-form');
//        $arrAttrItem['description'] = $description->find('ul')->htmlOuter();

        //главная фоточка

        if (key($config->forDetail['imgMain']) == 'href') {
            $arrAttrItem['imgMain'] = $document->find(current($config->forDetail['imgMain']))->attr('href');
        }
//        elseif (key($arrAttrDetailItem['imgMain']) == 'href') {
//
//        }

        //остальные фоточки
//        $shopImages = $document->find($arrAttrDetailItem['imgMain'])->find('img');
//        $imgArr = [];
//
//        foreach ($shopImages as $img) {
//            $img = pq($img);
//            $imgArr[] = ($img->attr('src') != "#") ? $img->attr('src') : '';
//        }

//        $arrAttrItem['imgArr'] = array_diff($imgArr, array(0, null));

        //аттрибуты
        $attributes = $document->find($config->forDetail['attr']['tableSelector'])->find($config->forDetail['attr']['rowSelector']);

        foreach ($attributes as $attr) {
            $attr = pq($attr);
            $attrText = $attr->find(":last")->text();

            if (empty($attrText)) {
                $arrAttrItem['attr'][$attr->find(":first")->text()] = trim($attr->find(":eq(2)")->html());
                continue;
            }

            $arrAttrItem['attr'][$attr->find(":first")->text()] = $attr->find(":last")->text();
        }


        //поиск файлов
//        $files = $document->find("#doc")->find("table");
//        $filesArr = [];
//
//        foreach ($files as $file) {
//            $file = pq($file);
//            //todo сделать регулярку до первых кавычек
//
//            preg_match("/.*?\<br\>/", $file->find('tr')->find(':first')->htmlOuter(), $matches);
//            $nameFile = $matches[1] ?? $matches[0] ?? '';
//            $filesArr[$nameFile] = $file->find('a')->attr('href');
//        }
//
//        $arrAttrItem['files'] = $filesArr;
//        echo "<pre>";
//        print_r($arrAttrItem);
//        die();

        return $arrAttrItem;

    }

    /**
     * Список товаров
     * @param $uri
     * @return array
     */
    public function getListUrl($listSelector, $itemSelector)
    {
        $listHref = [];
        $doc = $this->initPHPQ();
        $listUrl = $doc->find($listSelector)->find($itemSelector);

        foreach ($listUrl as $item) {
            echo "<pre>";
            print_r($item);
            die();
        }

        foreach ($listUrl as $url) {
            echo "<pre>";
            print_r($url);
            die();
            $url = pq($url);
            echo "<pre>";
            print_r('f');
            die();
            echo "<pre>";
            print_r($url->find('.text-muted small')->text());
            die();
            $url = $this->getOneUrl($url->find('a'));

            $listHref[] = $url;
        }
        echo "<pre>";
        print_r('fd');
        die();
        return $listHref;
    }

    /**
     * Возвращает один url из массива
     * @param $arrUrl
     * @return array|\phpQueryObject|string|null
     */
    public function getOneUrl($arrUrl)
    {
        $url = '';

        foreach ($arrUrl as $a) {
            $a = pq($a);
            if (strpos($a->attr('href'), '/') !== false) {
                $url = $a->attr('href');
            }
        }

        return $url;
    }


    public function getItemsInCategory($uriCategory)
    {
        $this->uri = $uriCategory;

        return $this->getItems($this->config, $uriCategory);
    }


//    public function getItems($config)
//    {
//
//        $parser = $this;
//
//        $parser->host = $config->host;
//        $parser->uri = $config->uri;
//        $parser->initPHPQ();
//        //все ссылки на товары в категории
//        $allItemsHref = $parser->getListUrl($config->forCategory['listItems'], $config->forCategory['itemBlock']);
//
//        $items = [];
//
//        foreach ($allItemsHref as $detailHref) {
//            $parser->uri = $detailHref;
//
//            $items[] = $parser->getDetailItem($config);
//        }
//        echo "<pre>";
//        print_r($items);
//        die();
//        return $items;
//    }
//

}