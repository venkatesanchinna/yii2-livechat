<?php
namespace app\modules\chat\assets;

use yii\web\AssetBundle;

/**
 * This declares the asset files required by CMS.
 *
 * @author Vinoth Pandiyan <vinoth.p@caritorsolutions.com>
 */
class LivechatAsset extends AssetBundle
{
    // the alias to assets folder in file system
    public $sourcePath = '/opt/lampp/htdocs/yii2-livechatlocal/backend/modules/chat/assets/source';
    public $css = ['css/juichat.css','css/jquery.cssemoticons.css'];
    public $js = [
      'js/jquery.juichat.js',
      'js/jquery.cssemoticons.min.js',
    ];
    // that are the dependecies, for making Asset bundle work with Yii2 framework
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
