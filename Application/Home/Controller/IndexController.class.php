<?php

namespace Home\Controller;

//import('Vendor.wechat.Wechat');
//import('ORG.Wx.Wechat');

use Think\Controller;

include 'FeyinAPI.php';


class IndexController extends Controller
{

    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本222 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');
    }


    public function wx()
    {
        //echo('aaa');

//define your token
//        define("TOKEN", "1qaz2wsx");
//        $wechatObj = new wechatCallbackapiTest();
//        $wechatObj->valid();

//
////        $Data     = M('user');// 实例化Data数据模型
////        $result     = $Data->count();
////        $this->assign('result',$result);
////        $this->display();
//
        $options = array(
            'token' => '1qaz2wsx', //填写你设定的key
            'encodingaeskey' => 'bMY75kCr4C4tGDtKFNHjZ0I5ZKNDU42DQXAQzxGY5v0', //填写加密用的EncodingAESKey
            'appid' => 'wx68b800e79738a452', //填写高级调用功能的app id
            'appsecret' => '57fb691b606420fe6d9bec9ef5c69274' //填写高级调用功能的密钥
        );
        $weObj = new \Org\Wx\Wechat($options);
        $weObj->valid();


    }


}