<?php

use yii\db\Schema;
use yii\db\Migration;

class m150704_132605_create_passwords_table extends Migration
{
    /*
    public function up()
    {

    }
    */
    public function down()
    {
        echo "m150704_132605_create_passwords_table cannot be reverted.\n";

        return false;
    }

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable('password', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'username' => Schema::TYPE_STRING,
            'password' => Schema::TYPE_STRING . ' NOT NULL',
            'description' => Schema::TYPE_TEXT
        ]);
    }

    /*
    public function safeDown()
    {
    }
    */
}
