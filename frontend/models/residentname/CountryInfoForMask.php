<?php

namespace app\models\residentname;

use Yii;

/**
 * This is the model class for table "country_info_for_mask".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string|null $part_of_the_world
 * @property string|null $capital
 * @property string|null $language
 * @property string|null $country_name
 * @property string|null $character_code_2
 * @property string|null $character_code_3
 * @property string|null $iso_code
 * @property string|null $full_name
 * @property string|null $title_in_english
 * @property string|null $location
 */
class CountryInfoForMask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country_info_for_mask';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id'], 'integer'],
            [['part_of_the_world', 'capital', 'language', 'country_name', 'character_code_2', 'character_code_3', 'iso_code', 'full_name', 'title_in_english', 'location'], 'string', 'max' => 100],
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
            'part_of_the_world' => 'Part Of The World',
            'capital' => 'Capital',
            'language' => 'Language',
            'country_name' => 'Country Name',
            'character_code_2' => '2 Character Code',
            'character_code_3' => '3 Character Code',
            'iso_code' => 'Iso Code',
            'full_name' => 'Full Name',
            'title_in_english' => 'Title In English',
            'location' => 'Location',
        ];
    }
}
