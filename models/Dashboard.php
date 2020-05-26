<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 *
 * @property integer $id
 * @property string $name
 * @property string $server_host
 * @property string $aplication_host
 * @property string $elasticsearch_host
 * @property string $elasticsearch_hash
 * @property boolean $cpu
 * @property boolean $memory
 * @property boolean $os
 * @property boolean $browsers
 * @property boolean $unique_users
 *
 */
class Dashboard extends ActiveRecord
{
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'elasticsearch_host', 'server_host', 'aplication_host'], 'required'],
            [
                [
                    'name',
                    'elasticsearch_host',
                    'server_host',
                    'aplication_host',
                    'elasticsearch_hash',
                ],
                'string',
            ],
            [['cpu', 'memory', 'os', 'browsers', 'elasticsearch_hash', 'unique_users'], 'safe'],
        ];
    }
}
