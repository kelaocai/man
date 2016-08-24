<?php
return array(
    //'配置项'=>'配置值'


    'DB_CONFIG_APP' => array(

        'DB_TYPE' => 'mysql',// 数据库类型
        'DB_HOST' => 'corneronline.mysql.rds.aliyuncs.com',// 服务器地址
        'DB_NAME' => 'apponline',// 数据库名
        'DB_USER' => 'app',// 用户名
        'DB_PWD' => '1KebaoCai',// 密码
        'DB_PORT' => 3306,// 端口
        'DB_PREFIX' => 'ss_',// 数据库表前缀
        'DB_CHARSET' => 'utf8',// 数据库字符集

    ),
    //包菜
    'DB_CONFIG_BC' => array(

        'DB_TYPE' => 'mysql',// 数据库类型
        'DB_HOST' => 'tsbonline.mysql.rds.aliyuncs.com',// 服务器地址
        'DB_NAME' => 'bc_crm',// 数据库名
        'DB_USER' => 'tsbdb',// 用户名
        'DB_PWD' => '3edc4rfv',// 密码
        'DB_PORT' => 3306,// 端口
        'DB_PREFIX' => 'bc_',// 数据库表前缀
        'DB_CHARSET' => 'utf8',// 数据库字符集
    ),

    //点点折
    'DB_CONFIG_DDZ' => array(

        'DB_TYPE' => 'mysql',// 数据库类型
        'DB_HOST' => 'corneronline.mysql.rds.aliyuncs.com',// 服务器地址
        'DB_NAME' => 'corneronline',// 数据库名
        'DB_USER' => 'app',// 用户名
        'DB_PWD' => '1KebaoCai',// 密码
        'DB_PORT' => 3306,// 端口
        'DB_PREFIX' => 'ddz_',// 数据库表前缀
        'DB_DEBUG' => TRUE,
        'DB_CHARSET' => 'utf8',// 数据库字符集
    ),

    //微信配置
    'WX_TOKEN' => '1qaz2wsx',
//    'WX_ENCODINGAESKEY' => 'bMY75kCr4C4tGDtKFNHjZ0I5ZKNDU42DQXAQzxGY5v0',
//    'WX_APPID'=>'wx68b800e79738a452',//baocaizai生产
//    'WX_APPSECRET'=>'57fb691b606420fe6d9bec9ef5c69274',//baocaizai生产
//    'WX_APPID'=>'wx4e4b57848ce1ced4',//yikebaocai生产
//    'WX_APPSECRET'=>'c94fd3da4d006277fe09d90d9d438202',//yikebaocai生产
//    'WX_APPID' => 'wx8e9af23cb8d804e8',//测试
//    'WX_APPSECRET' => '3208fc5407922bd7e3327f78febaa6eb',//测试

    'WX_APPID' => 'wxb595d641246eb016',//包菜网生产
    'WX_APPSECRET' => '52fe7f6d89fc0bc2796a86f6a8a00c19',//包菜网生产
    'WX_ENCODINGAESKEY' => 'H6BEjGmD3iW7QtTXKLVvqCwIO3urpJLW6yduXST9fjt',//包菜网生产


);