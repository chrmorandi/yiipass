<?php

use app\modules\yiipass\controllers\PostActionLoginController;


class PostActionLoginTest extends \PHPUnit_Framework_TestCase {

    public function setUp(){
        require_once(__DIR__ . '/yii_boot_phpunit.inc.php');
    }

    public function testGrabLogin(){

        $post_action_login = new PostActionLoginController();

        $post_action_login->actionGrabLogin();

        $debug = true;
    }

}
