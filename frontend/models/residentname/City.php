<?php

namespace app\models\residentname;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string|null $name
 * @property string|null $man
 * @property string|null $woman
 * @property string|null $townspeople
 * @property string|null $link
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id'], 'integer'],
            [['name', 'man', 'woman', 'townspeople', 'link'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country ID',
            'name' => 'Name',
            'man' => 'Man',
            'woman' => 'Woman',
            'townspeople' => 'Townspeople',
            'link' => 'Link',
        ];
    }
}
