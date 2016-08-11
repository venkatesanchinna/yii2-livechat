<?php

use yii\db\Migration;
use common\models\User;


class m160705_060702_seed_user_table extends Migration
{
    public function safeup()
    {
       
        $table_to_check = Yii::$app->db->schema->getTableSchema('user');
        //Create the table if not exist
        if ( ! is_object($table_to_check)) {

            $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

            $columns['id'] =$this->primaryKey();
            $columns['username'] =$this->string()->notNull()->unique();
            $columns['first_name'] =$this->string(50)->notNull();
            $columns['last_name'] =$this->string(30)->notNull();
            $columns['auth_key'] =$this->string(32)->notNull();
            $columns['password_hash'] =$this->string()->notNull();
            $columns['password_reset_token'] =$this->string()->unique();
            $columns['email'] =$this->string()->notNull()->unique();

            $columns['status'] =$this->smallInteger()->notNull()->defaultValue(10);
            $columns['created_at'] =$this->integer()->notNull();
            $columns['updated_at'] =$this->integer()->notNull();
            $this->createTable('{{%user}}', $columns, $tableOptions);
        }

        //Add the column in user
        if ( ! isset( $table_to_check->columns['first_name'] )) {
            $this->addColumn('user', 'first_name', $this->string(50)->notNull()->after('username'));
        }
        if ( ! isset( $table_to_check->columns['last_name'] )) {
            $this->addColumn('user', 'last_name', $this->string(30)->notNull()->after('first_name'));
        }
       
        //Insert the user for testing   
        $user = new User();
        $user->username = 'user1';
        $user->first_name = 'userone';
        $user->last_name = 'online';
        $user->email = 'venkatesangee@gmail.com';
        $user->setPassword('user1');
        $user->generateAuthKey();
        $user->save();
        
        $user = new User();
        $user->username = 'user2';
        $user->first_name = 'usertwo';
        $user->last_name = 'online';
        $user->email = 'venkatesan556@gmail.com';
        $user->setPassword('user2');
        $user->generateAuthKey();
        $user->save();
    }

    public function safedown()
    {
       
       // echo "m160705_060702_seed_user_table cannot be reverted.\n";

        //return false;
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
