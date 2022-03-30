<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "activity".
 *
 * @property int $id
 * @property string $dt_create
 * @property string $sessid
 * @property int $activity_id
 */
class Activity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dt_create', 'sessid', 'activity_id', 'url'], 'required'],
            [['sessid', 'activity_id'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dt_create' => 'Dt Create',
            'sessid' => 'Sessid',
            'activity_id' => 'Activity ID',
            'url' => 'Url',
        ];
    }
}
