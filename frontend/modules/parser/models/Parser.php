<?php

namespace app\modules\parser\models;


use phpQuery;
use GuzzleHttp\Client;
use yii\base\Model; // подключаем Guzzle

class Parser extends Model
{

//    const SITE_URL = "http://massage-kresla.loc";

    const DIR_IMG_PRODUCT = "/web/images/products/";
    const DIR_FILES_PRODUCT = "/web/files/products/";

    public $uri;
    public $host;
    public $config;


    public function __construct($config)
    {
        $this->config = (object)$config;
    }

    public function init()
    {
        parent::init();

    }

    /**
     * Инициализация парсера
     * @param $uri
     * @return \phpQueryObject|\QueryTemplatesParse|\QueryTemplatesSource|\QueryTemplatesSourceQuery
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initPHPQ()
    {
        $client = new Client();
        // отправляем запрос к странице Яндекса
        $res = $client->request('GET', $this->host . $this->uri, ['verify' => true]);
        // получаем данные между открывающим и закрывающим тегами body
        echo "<pre>"; print_r($res);die();
        $body = $res->getBody();
        // подключаем phpQuery
        $document = \phpQuery::newDocumentHTML($body);

        return $document;
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

        foreach ($listUrl as $url) {
            $url = pq($url);

            $url = $this->getOneUrl($url->find('a'));
            $listHref[] = $url;
        }
       
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


    public function getItems($config)
    {

        $parser = $this;
      
        $parser->host = $config->host;
        $parser->initPHPQ();
        //все ссылки на товары в категории
        echo "<pre>"; print_r($parser);die();
        $allItemsHref = $parser->getListUrl($config->forCategory['listItems'], $config->forCategory['itemBlock']);
        $items = [];

        foreach ($allItemsHref as $detailHref) {
            $parser->uri = $detailHref;
           
            $items[] = $parser->getDetailItem($config);
        }

        return $items;
    }


}