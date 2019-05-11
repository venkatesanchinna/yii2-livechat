<?php

namespace venkatesanchinna\yii2livechat\controllers;

use common\models\User;
use venkatesanchinna\yii2livechat\models\Chat;
use venkatesanchinna\yii2livechat\models\LCStates;
use Yii;
use yii\web\Controller;

/**
 * BookingController implements the CRUD actions for Booking model.
 */
class LivechatController extends Controller
{

    public $idleLimit = '300';
    public $flushTime = false;
    public $defaultuser = 'Unknown';
    public $defaultemail = 'Unknown';
    public $cookiename = 'juichat_user';
    public $hashcookie = 'juichat_hash';
    public $hash = '';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
    }
    /**
     * [beforeAction description]
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $this->SetUserHash();
        return parent::beforeAction($action);
    }
    /**
     * [actionView description]
     * @return [type] [description]
     */
    public function actionView()
    {

        return $this->render('index');
    }

    /**
     * Lists all Booking models.
     * @return mixed
     */
    public function actionIndex()
    {

        if (Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            return $this->redirect(Yii::$app->request->baseUrl);
        }

        if (isset($_REQUEST['mode'])) {
            switch ($_REQUEST['mode']) {
                case 'GetChats':
                    echo json_encode(array('viewers' => $this->GetViewers(), 'chats' => $this->GetChats()));
                    break;

                case 'GetViewers':
                    echo json_encode($this->GetViewers());
                    break;

                case 'SendChat':
                    $this->SendChat($_REQUEST['hash'], $_REQUEST['chat']);
                    break;

                case 'StartChat':
                    $this->StartChat($_REQUEST['hash'], isset($_REQUEST['positions']));
                    break;

                case 'CloseChat':
                    $this->CloseChat($_REQUEST['hash'], $_REQUEST['option'], isset($_REQUEST['positions']));
                    break;

                case 'MinimizeChat':
                    $this->MinimizeChat($_REQUEST['hash'], $_REQUEST['option']);
                    break;

                case 'SetUserName':
                    $this->SetUserName($_REQUEST['name'], $_REQUEST['email']);
                    break;

                case 'SetNewVisit':
                    echo json_encode($this->SetNewVisit());
                    break;
            }
        }

    }
    /**
     * [SetNewVisit description]
     */
    protected function SetNewVisit()
    {
        $session = Yii::$app->session;
        $session['juichat'] = ['viewed_rows' => array()];
        $user = Yii::$app->user->identity;
        return array('hash' => @$user->id, 'email' => @$user->email, 'user' => @$user->first_name . ' ' . @$user->last_name, 'gravitar' => $this->format_photo($user->id), 'viewers' => $this->GetViewers(), 'chat' => $this->GetChats());

    }
    /**
     * [SetUserHash description]
     */
    protected function SetUserHash()
    {
        if (!Yii::$app->user->isGuest) {
            $this->hash = Yii::$app->user->identity->id;
        }

    }
    /**
     * [SortByTime description]
     * @param [type] $a [description]
     * @param [type] $b [description]
     */
    private static function SortByTime($a, $b)
    {
        if ($a[0] == $b[0]) {
            return 0;
        }

        return ($a[0] < $b[0]) ? -1 : 1;
    }
    /**
     * [Sanitize description]
     * @param [type] $_string [description]
     */
    protected function Sanitize($_string)
    {
        // TO-DO: could use more!
        $_string = str_replace('<3', '&lt;3', $_string);
        $_string = str_replace(':<', ':&lt;', $_string);
        return strip_tags($_string, '<br><i>');
    }
    /**
     * [GetHash description]
     */
    protected function GetHash()
    {
        return $this->hash;
    }
    /**
     * [GetGravitarHash description]
     * @param [type] $_email [description]
     */
    protected function GetGravitarHash($_email)
    {
        return md5(strtolower(trim($_email)));
    }
    /**
     * [GetStyledChat description]
     * @param [type] $h [description]
     */
    protected function GetStyledChat($h)
    {

        $user = User::find()->where(['email' => mysql_real_escape_string($h[2])])->one();
        return json_encode(array('gravitar' => $this->format_photo($user->id), 'email' => $h[2], 'time' => date('g:i a', strtotime($h[0])), 'chat' => $h[1], 'type' => $h[3]));
    }
    /**
     * [GetChats description]
     */
    protected function GetChats()
    {

        $result = array();
        $session = Yii::$app->session;
        $connection = Yii::$app->db;
        $command = $connection->createCommand("SELECT DISTINCT sender FROM lc_chat WHERE receiver='" . mysql_real_escape_string($this->GetHash()) . "'");
        $senders = $command->queryAll();
        // get all unique calls where im the receiver
        foreach ($senders as $row) {
            $chat = array();
            $_lastmsg = '';

            //get all calls related to this where im the receiver
            $command1 = $connection->createCommand("SELECT * FROM lc_chat WHERE sender='" . mysql_real_escape_string($row['sender']) . "' AND receiver='" . mysql_real_escape_string($this->GetHash()) . "'");
            $senders1 = $command1->queryAll();
            foreach ($senders1 as $row1) {
                if (!isset($setvalue[$this->GetHash()])) {
                    $setvalue[$this->GetHash()] = array();
                }
                $ruser = User::find()->where(['id' => mysql_real_escape_string($row['sender'])])->one();
                if (!@in_array($row1['id'], $session['juichat']['viewed_rows'][$row['sender']])) {
                    if ($row1['chat']) {
                        $chat[] = array($row1['time'], $this->Sanitize($row1['chat']), $this->GetUserEmail($row['sender']), 'r');
                    }

                    $setvalue = $session['juichat']['viewed_rows'];
                    if (!isset($setvalue[$row['sender']])) {
                        $setvalue[$row['sender']] = array();
                    }
                    array_push($setvalue[$row['sender']], $row1['id']);
                    $session['juichat'] = ['viewed_rows' => $setvalue];

                    //$vieved[$row['sender']][]=$row1['id'];
                    //$session['juichat'] = ['viewed_rows' => $vieved];

                }
                $_lastmsg = strtotime($row1['time']);
            }

            // get all calls related to this where im the sender
            $command2 = $connection->createCommand("SELECT * FROM lc_chat WHERE sender='" . mysql_real_escape_string($this->GetHash()) . "' AND receiver='" . mysql_real_escape_string($row['sender']) . "'");
            $senders2 = $command2->queryAll();
            foreach ($senders2 as $row2) {
                $ruser = User::find()->where(['id' => mysql_real_escape_string($row['sender'])])->one();
                if (!@in_array($row2['id'], $session['juichat']['viewed_rows'][$this->GetHash()]) && $row2['chat']) {
                    $chat[] = array($row2['time'], $this->Sanitize($row2['chat']), $this->GetUserEmail($this->GetHash()), 'me');
                    $setvalue1 = $session['juichat']['viewed_rows'];
                    if (!isset($setvalue1[$this->GetHash()])) {
                        $setvalue1[$this->GetHash()] = array();
                    }
                    array_push($setvalue1[$this->GetHash()], $row2['id']);
                    $session['juichat'] = ['viewed_rows' => $setvalue1];
                    //$vieved1[$this->GetHash()][]=$row2['id'];
                    //$session['juichat'] = ['viewed_rows' => $vieved1];
                }
            }

            // sort the chat records by time
            usort($chat, array('venkatesanchinna\yii2livechat\controllers\LivechatController', 'SortByTime'));

            // display the chat records as html
            $output = array();
            foreach ($chat as $c => $h) {
                $output[] = $this->GetStyledChat($h);
            }
            $minimized = $this->GetState($row['sender'], 'minimized');
            $closed = $this->GetState($row['sender'], 'closed');
            if ($_lastmsg >= strtotime($closed[1])) {
                $closed = false;
            } else {
                $closed = $closed[0];
            }

            $result[] = array('user' => $this->GetUserName($row['sender']), 'ruser' => $this->GetUserName($row['sender']), 'hash' => $row['sender'], 'receiver' => @mysql_real_escape_string($this->GetHash()), 'chat' => $output, 'minimized' => $minimized[0], 'closed' => $closed, 'sort' => $this->GetPosition($row['sender']), 'lastmsg' => (strtotime(date('Y-m-d H:i:s')) - $_lastmsg), 'online' => $this->CheckOnline($row['sender']));
        }

        // get all unique calls where im the sender and no reply has been sent from the receiver
        $receiver1 = $connection->createCommand("SELECT DISTINCT receiver, chat FROM lc_chat WHERE sender='" . mysql_real_escape_string($this->GetHash()) . "'");
        $receivers1 = $receiver1->queryAll();
        foreach ($receivers1 as $row) {
            $_lastmsg = '';

            $receiver2 = $connection->createCommand("SELECT * FROM lc_chat WHERE sender='" . mysql_real_escape_string($row['receiver']) . "' AND receiver='" . mysql_real_escape_string($this->GetHash()) . "'");
            $receivers2 = $receiver2->queryAll();

            if (count($receivers2) == 0) {
                // i sent a message that got no reply!

                // duplicated code from above!
                $chat = array();
                $receiver3 = $connection->createCommand("SELECT * FROM lc_chat WHERE sender='" . mysql_real_escape_string($this->GetHash()) . "' AND receiver='" . mysql_real_escape_string($row['receiver']) . "'");
                $receivers3 = $receiver3->queryAll();
                foreach ($receivers3 as $row3) {
                    if (!@in_array($row3['id'], $session['juichat']['viewed_rows'][$this->GetHash()])) {
                        $type = 'me';
                        if ($row3['sender'] != mysql_real_escape_string($this->GetHash())) {
                            $type = 'r';
                        }

                        if ($row3['chat']) {
                            $chat[] = array($row3['time'], $this->Sanitize($row3['chat']), $this->GetUserEmail($this->GetHash()), $type);
                        }

                        $setvalue2 = $session['juichat']['viewed_rows'];
                        if (!isset($setvalue2[$this->GetHash()])) {
                            $setvalue2[$this->GetHash()] = array();
                        }
                        array_push($setvalue2[$this->GetHash()], $row3['id']);
                        $session['juichat'] = ['viewed_rows' => $setvalue2];
                        //$vieved2[$this->GetHash()][]=$row3['id'];
                        //$session['juichat'] = ['viewed_rows' => $vieved2];
                    }
                    $_lastmsg = strtotime($row3['time']);
                }
                // sort the chat records by time
                usort($chat, array('venkatesanchinna\yii2livechat\controllers\LivechatController', 'SortByTime'));

                // display the chat records as html
                $output = array();
                foreach ($chat as $c => $h) {
                    $output[] = $this->GetStyledChat($h);
                }

                $minimized = $this->GetState($row['receiver'], 'minimized');
                $closed = $this->GetState($row['receiver'], 'closed');
                if ($_lastmsg >= strtotime($closed[1])) {
                    $closed = false;
                } else {
                    $closed = $closed[0];
                }

                $result[] = array('user' => $this->GetUserName($row['receiver']), 'ruser' => $this->GetUserName($row['receiver']), 'hash' => $row['receiver'], 'receiver' => $row['receiver'], 'chat' => $output, 'minimized' => $minimized[0], 'closed' => $closed, 'sort' => $this->GetPosition($row['receiver']), 'online' => $this->CheckOnline($row['receiver']));
            }
        }

        usort($result, array('venkatesanchinna\yii2livechat\controllers\LivechatController', 'SortByPosition'));

        return $result;
    }
    /**
     * [SortByPosition description]
     * @param [type] $a [description]
     * @param [type] $b [description]
     */
    protected function SortByPosition($a, $b)
    {
        return $a['sort'] > $b['sort'];
    }
    /**
     * [GetPosition description]
     * @param [type] $_hash [description]
     */
    protected function GetPosition($_hash)
    {
        $session = Yii::$app->session;
        return isset($session['juichat']['positions'][$_hash]) ? $session['juichat']['positions'][$_hash] : false;
    }
    /**
     * [SetPositions description]
     * @param [type] $_positions [description]
     */
    protected function SetPositions($_positions)
    {
        $session = Yii::$app->session;
        if (!is_array($_positions)) {
            return false;
        }

        foreach ($_positions as $_hash => $_position) {
            $session['juichat'] = ['positions' => [$this->Sanitize($_position[0]) => $rthis->Sanitize($_position[1])]];
        }
    }
    /**
     * [StartChat description]
     * @param [type] $_hash      [description]
     * @param [type] $_positions [description]
     */
    protected function StartChat($_hash, $_positions)
    {
        $this->CloseChat($_hash, false, $_positions);
    }
    /**
     * [SendChat description]
     * @param [type] $_hash [description]
     * @param [type] $_chat [description]
     */
    protected function SendChat($_hash, $_chat)
    {
        $session = Yii::$app->session;
        if (preg_match('/\/me/s', $_chat)) {
            $_chat = '<i>' . str_replace('/me', $this->GetUserName($this->GetHash()), $_chat) . '</i>';
        }

        $save['sender'] = mysql_real_escape_string($this->GetHash());
        $save['receiver'] = mysql_real_escape_string($this->Sanitize($_hash));
        $save['chat'] = mysql_real_escape_string($this->Sanitize($_chat));
        $save['time'] = date('Y-m-d H:i:s');

        $lcchat = new Chat();
        $lcchat->load($save);
        $lcchat->attributes = $save;
        $lcchat->save();
        $id = Yii::$app->db->getLastInsertID();
        $setvalue = $session['juichat']['viewed_rows'];
        if (!isset($setvalue[$this->GetHash()])) {
            $setvalue[$this->GetHash()] = array();
        }
        array_push($setvalue[$this->GetHash()], $id);
        $session['juichat'] = ['viewed_rows' => $setvalue];
    }
    /**
     * [SetState description]
     * @param [type] $_hash          [description]
     * @param [type] $_state         [description]
     * @param [type] $_option_status [description]
     */
    protected function SetState($_hash, $_state, $_option_status)
    {
        $_states = array('closed', 'minimized');
        if (!in_array($_state, $_states)) {
            return false;
        }

        $save['receiver'] = $cond['receiver'] = mysql_real_escape_string($this->Sanitize($_hash));
        $save['sender'] = $cond['sender'] = mysql_real_escape_string($this->GetHash());
        $save['state'] = $cond['state'] = mysql_real_escape_string($_state);
        $save['option_status'] = mysql_real_escape_string($this->Sanitize($_option_status));
        $save['time'] = date('Y-m-d H:i:s');
        $result = LCStates::find()->where($cond)->one();

        if (!$result) {
            $result = new LCStates();
        }

        $result->load($save);
        $result->attributes = $save;
        $result->save();
    }
    /**
     * [GetState description]
     * @param [type] $_hash  [description]
     * @param [type] $_state [description]
     */
    protected function GetState($_hash, $_state)
    {
        $_states = array('closed', 'minimized');
        if (!in_array($_state, $_states)) {
            return false;
        }

        $cond['receiver'] = mysql_real_escape_string($this->Sanitize($_hash));
        $cond['sender'] = mysql_real_escape_string($this->GetHash());
        $cond['state'] = mysql_real_escape_string($_state);
        $result = LCStates::find()->where($cond)->asArray()->one();
        return array($result['option_status'], $result['time']);
    }
    /**
     * [CloseChat description]
     * @param [type] $_hash          [description]
     * @param [type] $_option_status [description]
     * @param [type] $_positions     [description]
     */
    protected function CloseChat($_hash, $_option_status, $_positions)
    {
        $session = Yii::$app->session;
        if (isset($session['juichat']['positions'][$this->Sanitize($_hash)])) {
            unset($session['juichat']['positions'][$this->Sanitize($_hash)]);
        }

        $this->SetState($_hash, 'closed', $_option_status);
        $this->MinimizeChat($_hash, false);
        $this->SetPositions($_positions);

    }
    /**
     * { function_description }
     *
     * @param      <type>  $_hash           The hash
     * @param      <type>  $_option_status  The option status
     */

    protected function MinimizeChat($_hash, $_option_status)
    {
        $this->SetState($_hash, 'minimized', $_option_status);
    }
    /**
     * [GetUserName description]
     * @param [type] $_hash [description]
     */
    protected function GetUserName($_hash)
    {
        $user = User::findOne(mysql_real_escape_string($this->Sanitize($_hash)));
        return !$user['first_name'] ? $this->defaultuser : $user['first_name'] . ' ' . $user['last_name'];
    }
    /**
     * [GetUserEmail description]
     * @param [type] $_hash [description]
     */
    protected function GetUserEmail($_hash)
    {
        $user = User::findOne(mysql_real_escape_string($this->Sanitize($_hash)));
        return !$user['email'] ? $this->defaultemail : $user['email'];
    }

    /**
     * [GetViewers description]
     */
    protected function GetViewers()
    {
        $result = array();

        $users = Chat::getChatUsers(mysql_real_escape_string($this->GetHash()));

        $viewers = array();
        foreach ($users as $user) {
            if ($user['name']) {
                $usern['id'] = $user['id'];
                $usern['user'] = $user['name'];
                $usern['email'] = $user['email'];
                $usern['hash'] = $user['hash'];
                $usern['gravitar'] = $this->format_photo($user['id']);
                $viewers[] = $usern;
            }
        }

        return $viewers;
    }
    /**
     * [format_photo description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    protected function format_photo($id)
    {

        //$imurl="http://www.gravatar.com/avatar/".$this->GetGravitarHash($id)."?d=mm";
       return $this->getphoto('', '128');

    }
    /**
     * [GetViewer description]
     * @param [type] $_hash [description]
     */
    protected function GetViewer($_hash)
    {

        $user = User::findOne(mysql_real_escape_string($this->Sanitize($_hash)));
        return $user ? true : false;

    }
    /**
     * [CheckOnline description]
     * @param [type] $_hash [description]
     */
    protected function CheckOnline($_hash)
    {

        $user = User::find()->where(['id' => mysql_real_escape_string($this->Sanitize($_hash))])->one();
        return $user ? true : false;

    }
    /**
     * [getphoto description]
     * @param  [type]  $photo       [description]
     * @param  string  $size        [description]
     * @param  boolean $customimage [description]
     * @return [type]               [description]
     */
    protected function getphoto($photo, $size = '64', $customimage = false)
    {
        $image = $photo;
        $default = @$customimage ? $customimage : 'user_' . $size . '_' . $size . '.png';
        //$iurl='/yii2-livechatlocal/backend/modules/chat/assets/source/default/'.$default;
        $iurl = str_replace('/web', '', Yii::$app->request->baseUrl) . '/vendor/venkatesanchinna/yii2-livechat/assets/source/default/' . $default;
        $iurl = str_replace('backend/', '', $iurl);
        $iurl = str_replace('frontend/', '', $iurl);

        if (isset($image) && !empty($image)) {
            $iurls = Yii::getAlias('@backend') . $image->path_original . $image->name;
            if (file_exists($iurls)) {

                $iurl = str_replace('/opt/lampp/htdocs/', '', $iurls);
                $iurl = str_replace('/home/heler/public_html/beta/', '', $iurl);
                $iurl = str_replace('/home/heler/public_html/', '', $iurl);
                $iurl = str_replace('/backend', '', $iurl);
                $iurl = str_replace('/web', '/admin', $iurl);
                $iurl = str_replace('backend/', '', $iurl);

            }
        }

        return Yii::$app->request->hostInfo . '/' . $iurl;

    }
}