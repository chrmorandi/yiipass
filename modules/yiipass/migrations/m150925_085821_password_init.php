<?php

use yii\db\Schema;
use yii\db\Migration;

class m150925_085821_password_init extends Migration
{
    public function up()
    {
        $this->createTable('password', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING,
            'group' => Schema::TYPE_STRING,
            'username' => Schema::TYPE_STRING,
            'password' => Schema::TYPE_STRING,
            'comment' => Schema::TYPE_TEXT,
            'url' => Schema::TYPE_TEXT,
            'creation' => Schema::TYPE_TIMESTAMP,
            'lastaccess' => Schema::TYPE_TIMESTAMP,
            'lastmod' => Schema::TYPE_TIMESTAMP,
            'expire' => Schema::TYPE_TIMESTAMP,
        ]);
    }

    public function down()
    {
        echo "m150925_085821_password cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
