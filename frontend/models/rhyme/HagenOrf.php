<?php

namespace app\models\rhyme;

use Yii;

/**
 * This is the model class for table "hagen_orf".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $word
 * @property string|null $accent
 * @property string|null $word_with_accent
 */
class HagenOrf extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hagen_orf';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['word'], 'string', 'max' => 100],
            [['accent'], 'string', 'max' => 450],
            [['word_with_accent'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'word' => 'Word',
            'accent' => 'Accent',
            'word_with_accent' => 'Word With Accent',
        ];
    }
}
