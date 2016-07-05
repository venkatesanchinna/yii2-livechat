<?php

use yii\db\Migration;
use common\models\User;

class m160705_060702_seed_user_table extends Migration
{
    public function up()
    {
            
        $this->addColumn('user', 'isonline', $this->integer()->defaultValue(1));
        
        $user = new User();
        $user->username = 'venkatesan';
        $user->email = 'venkatesangee@gmail.com';
        $user->password = 'l!vech@t';
        $user->setPassword('l!vech@t');
        $user->generateAuthKey();
        $user->save();
        
        $user = new User();
        $user->username = 'prabu';
        $user->email = 'venkatesan556@gmail.com';
        $user->password = 'l!vech@t';
        $user->setPassword('l!vech@t');
        $user->generateAuthKey();
        $user->save();
    }

    public function down()
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
