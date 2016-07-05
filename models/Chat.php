<?php

namespace backend\modules\Livechat\models;
use common\models\User;
use Yii;

/**
 * This is the model class for table "t_lc_chat".
 *
 * @property integer $id
 * @property integer $sender
 * @property integer $receiver
 * @property string $chat
 * @property string $time
 *
 * @property User $sender0
 * @property User $receiver0
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lc_chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'receiver', 'chat', 'time'], 'required'],
            [['sender', 'receiver'], 'integer'],
            [['chat'], 'string'],
            [['time'], 'safe'],
            [['sender'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sender' => 'id']],
            [['receiver'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['receiver' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender' => 'Sender',
            'receiver' => 'Receiver',
            'chat' => 'Chat',
            'time' => 'Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'sender']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'receiver']);
    }
    public function getChatUsers($id)
    {
        if(!Yii::$app->user->isGuest) {
        $query = User::find()->orderby('username asc');
        $query->select(['user.id','username as name','email','user.id as hash']);
        $query->where("user.id != '".$id."' AND user.status='1' AND isonline=1");
        return $query->asArray()->all();
        }
        return array();
    }
}
