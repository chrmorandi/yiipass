<?php

namespace app\modules\yiipass\models;

use Yii;

/**
 * This is the model class for table "password".
 *
 * @property integer $id
 * @property string $title
 * @property string $group
 * @property string $username
 * @property string $password
 * @property string $comment
 * @property string $url
 * @property string $creation
 * @property string $lastaccess
 * @property string $lastmod
 * @property string $expire
 */
class Password extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'password';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'group', 'password'], 'required'],
            [['comment'], 'string'],
            [['creation', 'lastaccess', 'lastmod', 'expire'], 'safe'],
            [['title', 'group', 'username', 'password', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'group' => 'Group',
            'username' => 'Username',
            'password' => 'Password',
            'comment' => 'Comment',
            'url' => 'Url',
            'creation' => 'Creation',
            'lastaccess' => 'Lastaccess',
            'lastmod' => 'Lastmod',
            'expire' => 'Expire',
        ];
    }
}
