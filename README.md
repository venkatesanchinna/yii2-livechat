Yii2 Live Chat
===============

[![Latest Stable Version](https://poser.pugx.org/venkatesanchinna/yii2-livechat/v/stable)](https://packagist.org/packages/venkatesanchinna/yii2-livechat)
[![License](https://poser.pugx.org/venkatesanchinna/yii2-livechat/license)](https://packagist.org/packages/venkatesanchinna/yii2-livechat)
[![Total Downloads](https://poser.pugx.org/venkatesanchinna/yii2-livechat/downloads)](https://packagist.org/packages/venkatesanchinna/yii2-livechat)
[![Monthly Downloads](https://poser.pugx.org/venkatesanchinna/yii2-livechat/d/monthly)](https://packagist.org/packages/venkatesanchinna/yii2-livechat)
[![Daily Downloads](https://poser.pugx.org/venkatesanchinna/yii2-livechat/d/daily)](https://packagist.org/packages/venkatesanchinna/yii2-livechat)

Online user chat for Yii


Features
=========

+ Easy text chat with system user
+ Integrated with [Yii2-livechat](https://github.com/venkatesanchinna/yii2-livechat) - flexible user online chat module
+ Sample url : http://host/pathtoproject/backend/web/index.php?r=chat/livechat/view


Installation
============


The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist venkatesanchinna/yii2-livechat "*"
```

or run

```
composer require venkatesanchinna/yii2-livechat
```

or add

```
"venkatesanchinna/yii2-livechat": "*"
```

to the require section of your `composer.json` file.


Usage
=====

1. Let 's add into modules config in your main config file

    ````
        'modules' => [
            'chat' => [
                'class' => 'venkatesanchinna\yii2livechat\Module'
            ]
        ]

    ````

    Next, update the database schema 

    ````
    $ php yii migrate/up --migrationPath=@vendor/venkatesanchinna/yii2-livechat/migrations

    ````

    Ok. That's done. Avaiable route now:

    + /chat/livechat/view

2. To register chat module configuration avaible:

    ````php

    use venkatesanchinna\yii2livechat\assets\LivechatAsset;
    LivechatAsset::register($this);

    ````

3. To load the chat view in your website

    ```
    Add in view

    <div class="book_appointment ui-lc-viewers plus-minus">
        <div class="book_head " style="bottom: 0px; opacity: 1; width: 322px;">
            <span class="header_content book_app txtcap"></span> <span class=" fa fa-plus plus "></span>
            <div class="clear"></div>
        </div>
        <div class="book_form " >
            <div class="ui-container" style="bottom: -280px; opacity: 1;"></div>
        </div>
    </div>

    ```
4. To Call the chat

    ```php

        $this->registerJs(' $().juichat({\'display_viewers\': true});'); 
    ```

5. Demo Users

    ```

    Username    : user1 & user2
    Password    : user1 & user2

    ```
6. Note

    ```

    + This will work after login to the site so check the condition where loading the chat ui and script

    ```

License
=======

MIT


