<?php

use yii\db\Migration;

/**
 * Handles the creation for table `chat_table`.
 */
class m160705_064243_create_chat_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('lc_chat', [
            'id' => $this->primaryKey(),
            'sender' => $this->integer()->notNull(),
            'receiver' => $this->integer()->notNull(),
            'chat' => $this->text(),
            'time' => $this->dateTime(),
        ]);

        // creates index for column `sender`
        $this->createIndex(
            'idx-lc_chat-sender',
            'lc_chat',
            'sender'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-lc_chat-sender',
            'lc_chat',
            'sender',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `receiver`
        $this->createIndex(
            'idx-lc_chat-receiver',
            'lc_chat',
            'receiver'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-lc_chat-receiver',
            'lc_chat',
            'receiver',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('lc_chat');
    }
}
