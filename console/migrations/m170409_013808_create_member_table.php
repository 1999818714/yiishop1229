<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member`.
 */
class m170409_013808_create_member_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('member', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            //'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'tel'=>$this->char(11)->notNull()->comment('电话'),
            'last_login_time'=>$this->integer(),
            //211.123.110.119
            //ip2long('211.123.110.119')
            //long2ip()
            'last_login_ip'=>$this->integer(),
            'status' => $this->smallInteger()->defaultValue(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('member');
    }
}
