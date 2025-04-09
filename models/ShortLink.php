<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "short_links".
 *
 * @property int $id
 * @property string $original_url
 * @property string $short_code
 * @property int $created_at
 * @property int|null $clicks_count
 *
 * @property LinkLogs[] $linkLogs
 */
class ShortLink extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'short_links';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clicks_count'], 'default', 'value' => 0],
            [['original_url', 'short_code', 'created_at'], 'required'],
            [['created_at', 'clicks_count'], 'integer'],
            [['original_url'], 'string', 'max' => 2000],
            [['short_code'], 'string', 'max' => 10],
            [['short_code'], 'unique'],

            [['original_url'], 'url'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'original_url' => 'Original Url',
            'short_code' => 'Short Code',
            'created_at' => 'Created At',
            'clicks_count' => 'Clicks Count',
        ];
    }

    /**
     * Gets query for [[LinkLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinkLogs()
    {
        return $this->hasMany(LinkLog::class, ['link_id' => 'id']);
    }

}
