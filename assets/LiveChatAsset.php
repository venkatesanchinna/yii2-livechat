<?php
namespace backend\modules\Livechat\assets;

use yii\web\AssetBundle;

/**
 * This declares the asset files required by CMS.
 *
 * @author Vinoth Pandiyan <vinoth.p@caritorsolutions.com>
 */
class LiveChatAsset extends AssetBundle
{
    // the alias to assets folder in file system
    public $sourcePath = '@webroot/modules/LiveChat/assets/source';
    public $css = ['css/juichat.css'];
    public $js = [
      'js/jquery.juichat.js',
    ];
    // that are the dependecies, for making Asset bundle work with Yii2 framework
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
