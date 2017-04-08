<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170406_030114_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'parent_id'=>$this->integer()->comment('上级分类id'),
            'name'=>$this->string(20)->notNull()->comment('名称'),
            'url'=>$this->string(50)->notNull()->comment('路由（权限）'),
            'description'=>$this->text()->comment('描述'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
