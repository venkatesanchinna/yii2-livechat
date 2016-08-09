Yii2 Live Chat
===============

Simple Online chat

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist venkaetsangee/yii2-livechat
```

or add

```
"venkaetsangee/yii2-livechat": "*"
```

to the require section of your `composer.json` file.

Usage
------------

1. The chat extension can use any database storage supported by yii2. Migrate the chat tables.

    php yii migrate


2. To start chat need to config the module:
    - Add following code in `backend\config\main.php`:
    
        'modules' => [
            'chat' => [
                'class' => 'app\modules\chat\Module',
            ],

        ],
        
3. To register chat on page just call:

    ```php
            <?php
                use yii\helpers\Html;
                use app\modules\chat\assets\LivechatAsset;
                LivechatAsset::register($this);
                $this->title='Live Chat';
            ?>

            <div class="book_appointment ui-lc-viewers plus-minus">
            <div class="book_head " style="bottom: 0px; opacity: 1; width: 322px;">
            <span class="header_content book_app txtcap"></span> <span class=" fa fa-plus plus "></span>
            <div class="clear"></div>
            </div>
            <div class="book_form " >

            <div class="ui-container" style="bottom: -280px; opacity: 1;"></div>

            </div>
            </div>
            <?php 
                $this->registerJs(' $().juichat({\'display_viewers\': true});'); 
            ?>
   ```
   

License
----

MIT

