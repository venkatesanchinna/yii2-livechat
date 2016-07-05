<?php

namespace app\modules\chat\models;

use Yii;

/**
 * This is the model class for table "t_lc_states".
 *
 * @property integer $id
 * @property integer $sender
 * @property integer $receiver
 * @property string $state
 * @property string $time
 * @property string $option_status
 *
 * @property User $sender0
 * @property User $receiver0
 */
class LCStates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lc_states';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'receiver', 'state', 'time', 'option_status'], 'required'],
            [['sender', 'receiver'], 'integer'],
            [['time'], 'safe'],
            [['state'], 'string', 'max' => 100],
            [['option_status'], 'string', 'max' => 32],
            [['sender'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['sender' => 'id']],
            [['receiver'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['receiver' => 'id']],
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
            'state' => 'State',
            'time' => 'Time',
            'option_status' => 'Option Status',
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
}
