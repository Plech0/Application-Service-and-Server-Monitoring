<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 *
 * @property integer $id
 * @property string $server_host
 * @property string $resource
 * @property integer $value
 * @property integer $timestamp
 *
 */
class History extends ActiveRecord
{
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['server_host', 'resource', 'value', 'timestamp'], 'required'],
            [['server_host', 'resource'], 'string'],
            [['value', 'timestamp'], 'integer'],
        ];
    }
}
