<?php
/**
 * Created by PhpStorm.
 * User: 2000
 * Date: 12.08.2020
 * Time: 11:40
 */

namespace app\modules\parser\models;


use yii\base\Model;

class ParserCsv extends Model
{
// указатель на файл
    protected $handle;

    // заголовок csv файла
    protected $header;

    /**
     * Открываем файл на чтение
     *
     * @param $file
     * @param bool $header
     * @return $this
     * @throws \Exception
     */
    public function open($file, $header = true, $delimetr)
    {
        $this->handle = fopen($file, 'r');
        if (!$this->handle) {
            throw new Exception('Невозможно прочитать файл ' . $file);
        }

        if ($header) {
            $this->header = fgetcsv($this->handle, 10000, $delimetr);
        }
        return $this;
    }

    /**
     * Разбирает файл, передавая данные из файла
     * в коллбэк функцию
     */
    public function parse($delimetr, callable $callable)
    {

        if (!$this->handle) {
            throw new Exception('Файл не открыт!');
        }

        $i = 1;

        while (($data = fgetcsv($this->handle, 10000, $delimetr)) !== false) {
            if ($this->header) {

                if (count($this->header) != count($data)) {
                    echo "<pre>";
                    print_r($data);
                    die();
                }

                $data = array_combine($this->header, $data);
            }
            // вызываем коллбэк, первый аргумент данные, второй ссылка на объект
            $callable($data, $this, $i);
            $i++;
        }
    }


    public function __destruct()
    {
        $this->close();
    }


    public function close()
    {
        if ($this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }


    public function value($row, $field)
    {
        return $row[$field];
    }

}