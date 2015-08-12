<?php

namespace app\modules\yiipass\controllers;

use Yii;
use yii\di\ServiceLocator;
use app\modules\yiipass\models\Password;
use app\modules\yiipass\models\PasswordSearch;

/**
 * PasswordController implements the CRUD actions for Password model.
 */
class ImportXmlController
{
    /**
     * Iterates over all password groups and their passwords and saves them
     * to the database.
     */
    public function get($file_path){
        /* @var $xml \app\modules\yiipass\services\SimpleKeePassXmlService */
        $xml = \Yii::$app->getModule('yiipass')->get('SimpleKeePassXmlService');

        $arr_password_groups = $xml->formXmlFileToArray($file_path);
        foreach($arr_password_groups as $group){
            foreach($group as $password_from_group){
                /* @var $password_from_group \app\models\Password */
                /* @var $password \app\models\Password */
                $password = new Password();
                $password->title = $password_from_group->title->__toString();
                $password->username = $password_from_group->username->__toString();
                $password->password = $password_from_group->password->__toString();
                $password->url = $password_from_group->url->__toString();
                $password->comment = $password_from_group->comment->__toString();
                $password->creation = $password_from_group->creation->__toString();
                $password->lastaccess = $password_from_group->lastaccess->__toString();
                $password->lastmod = $password_from_group->lastmod->__toString();
                $password->group = $password_from_group->group->__toString();
                $password->lastaccess = $password_from_group->lastaccess->__toString();
                $password->save();
            }
        }
    }
}