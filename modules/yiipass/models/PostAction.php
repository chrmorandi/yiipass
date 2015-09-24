<?php

namespace app\modules\yiipass\models;

use Yii;

/**
 * This is the model class for table "post_action".
 *
 * @property integer $password_id
 * @property string $post_action_url
 * @property string $user_form_field_id
 * @property string $password_form_field_id
 *
 * @property Password $password
 */
class PostAction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_action';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_action_url'], 'string'],
            [['user_form_field_id', 'password_form_field_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password_id' => 'Password ID',
            'post_action_url' => 'Post Action Url',
            'user_form_field_id' => 'User Form Field ID',
            'password_form_field_id' => 'Password Form Field ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPassword()
    {
        return $this->hasOne(Password::className(), ['id' => 'password_id']);
    }
}
