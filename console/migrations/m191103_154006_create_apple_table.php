<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%apple}}`.
 */
class m191103_154006_create_apple_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%apple}}', [
            'id' => $this->primaryKey(),
            'color' => $this->string(50)->notNull()->comment('Цвет'),
            'created_at' => $this->dateTime()->notNull()->comment('Дата создания'),
            'fell_at' => $this->dateTime()->null()->comment('Дата падения'),
            'status' => $this->tinyInteger(1)->notNull()->comment('Статус'),
            'eaten_percent' => $this->tinyInteger(3)->notNull()->defaultValue(0)->comment('Съедено %')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%apple}}');
    }
}
