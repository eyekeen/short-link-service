<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "link_logs".
 *
 * @property int $id
 * @property int $link_id
 * @property string $ip_address
 * @property int $accessed_at
 *
 * @property ShortLinks $link
 */
class LinkLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'link_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['link_id', 'ip_address', 'accessed_at'], 'required'],
            [['link_id', 'accessed_at'], 'integer'],
            [['ip_address'], 'string', 'max' => 45],
            [['link_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShortLink::class, 'targetAttribute' => ['link_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'link_id' => 'Link ID',
            'ip_address' => 'Ip Address',
            'accessed_at' => 'Accessed At',
        ];
    }

    /**
     * Gets query for [[Link]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLink()
    {
        return $this->hasOne(ShortLink::class, ['id' => 'link_id']);
    }

}
