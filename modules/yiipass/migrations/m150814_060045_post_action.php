<?php

use yii\db\Schema;
use yii\db\Migration;

class m150814_060045_post_action extends Migration
{
    public function up()
    {
        $this->createTable('post_action', [
            'password-id' => Schema::TYPE_PK,
            'post-action-url' => Schema::TYPE_TEXT,
            'user-form-field-id' => Schema::TYPE_STRING,
            'password-form-field-id' => Schema::TYPE_STRING,
        ]);
    }

    public function down()
    {
        echo "m150814_060045_post_action cannot be reverted.\n";

        return false;
    }
    

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    /*`
    public function safeDown()
    {
    }
    */
}
