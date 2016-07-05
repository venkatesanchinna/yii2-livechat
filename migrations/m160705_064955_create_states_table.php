<?php

use yii\db\Migration;

/**
 * Handles the creation for table `status_table`.
 */
class m160705_064955_create_states_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('lc_states', [
            'id' => $this->primaryKey(),
            'sender' => $this->integer()->notNull(),
            'receiver' => $this->integer()->notNull(),
            'state' => $this->string(100),
            'time' => $this->dateTime(),
            'option_status' => $this->string(32),
        ]);

        // creates index for column `sender`
        $this->createIndex(
            'idx-lc_states-sender',
            'lc_states',
            'sender'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-lc_states-sender',
            'lc_states',
            'sender',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `receiver`
        $this->createIndex(
            'idx-lc_states-receiver',
            'lc_states',
            'receiver'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-lc_states-receiver',
            'lc_states',
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
        $this->dropTable('lc_states');
    }
}
