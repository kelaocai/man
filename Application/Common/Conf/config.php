<?php
return array(
	//'配置项'=>'配置值'


    'DB_CONFIG_APP'=>array(

        'DB_TYPE'=>'mysql',// 数据库类型
        'DB_HOST'=>'corneronline.mysql.rds.aliyuncs.com',// 服务器地址
        'DB_NAME'=>'apponline',// 数据库名
        'DB_USER'=>'app',// 用户名
        'DB_PWD'=>'1KebaoCai',// 密码
        'DB_PORT'=>3306,// 端口
        'DB_PREFIX'=>'ss_',// 数据库表前缀
        'DB_CHARSET'=>'utf8',// 数据库字符集

    ),
    //包菜
    'DB_CONFIG_BC'=>array(

        'DB_TYPE'=>'mysql',// 数据库类型
        'DB_HOST'=>'tsbonline.mysql.rds.aliyuncs.com',// 服务器地址
        'DB_NAME'=>'bc_crm',// 数据库名
        'DB_USER'=>'tsbdb',// 用户名
        'DB_PWD'=>'3edc4rfv',// 密码
        'DB_PORT'=>3306,// 端口
        'DB_PREFIX'=>'bc_',// 数据库表前缀
        'DB_CHARSET'=>'utf8',// 数据库字符集
    ),

    //点点折
    'DB_CONFIG_DDZ'=>array(

        'DB_TYPE'=>'mysql',// 数据库类型
        'DB_HOST'=>'corneronline.mysql.rds.aliyuncs.com',// 服务器地址
        'DB_NAME'=>'corneronline',// 数据库名
        'DB_USER'=>'app',// 用户名
        'DB_PWD'=>'1KebaoCai',// 密码
        'DB_PORT'=>3306,// 端口
        'DB_PREFIX'=>'ddz_',// 数据库表前缀
        'DB_DEBUG'  =>  TRUE,
        'DB_CHARSET'=>'utf8',// 数据库字符集
    )


    );