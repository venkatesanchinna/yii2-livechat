<?php

use yii\db\Migration;
use common\models\User;


class m160705_060702_seed_user_table extends Migration
{
    public function safeup()
    {
       
        $this->addColumn('user', 'first_name', $this->string(50)->notNull()->after('username'));
        $this->addColumn('user', 'last_name', $this->string(50)->notNull()->after('first_name'));
              
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
