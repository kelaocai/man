<?php

namespace Home\Controller;

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

        Vendor('Kdt.lib.SimpleHttpClient');

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
//            'appid' => 'wx8e9af23cb8d804e8', //填写高级调用功能的app id 测试
//            'appsecret' => '3208fc5407922bd7e3327f78febaa6eb' //填写高级调用功能的密钥 测试

            'appid' => 'wx68b800e79738a452', //填写高级调用功能的app id
            'appsecret' => '57fb691b606420fe6d9bec9ef5c69274' //填写高级调用功能的密钥
        );
        $weObj = new \Org\Wx\Wechat($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();
        switch ($type) {
            case \Org\Wx\Wechat::MSGTYPE_TEXT:
                $msgNo = time() + 1;
                $freeMessage = array(
                    'memberCode' => MEMBER_CODE,
                    'msgDetail' => '' . $weObj->getRevContent(),
                    'deviceNo' => DEVICE_NO,
                    'msgNo' => $msgNo,
                );
//                $pr_status = sendFreeMessage($freeMessage);
                $pr_status = 1;
                $usr_info = $weObj->getUserInfo($weObj->getRevFrom());
                //\Think\Log::write($usr_info['nickname'].'我的记12录'.$weObj->getRevFrom(),'WARN');

                //$pr_status=sendMail('34206043@qq.com',$usr_info['nickname'].'微信发来信息',$weObj->getRevContent().'<img src='.$usr_info['headimgurl'].' />.');

                $weObj->text("发送:" . $pr_status)->reply();


                exit;
                break;
            case \Org\Wx\Wechat::MSGTYPE_EVENT:
                break;
            case \Org\Wx\Wechat::MSGTYPE_IMAGE:
                break;
            case \Org\Wx\Wechat::MSGTYPE_LOCATION:
                $Location_X = $weObj->getRevGeo()['x'];
                $Location_Y = $weObj->getRevGeo()['y'];
                //$weObj->text("zuobiao:".$Location_X.",".$Location_Y)->reply();
                $params = [
                    'ak' => 'lB3MdI4HADGDT8trntoLxOWR',
                    'geotable_id' => 143034,
                    'location' => $Location_Y . ',' . $Location_X,
                    'radius' => 1500
                ];
                $rs = \SimpleHttpClient::get('http://api.map.baidu.com/geosearch/v3/nearby', $params);
                $data = json_decode($rs, true);
                $add_list = '';
                \Think\Log::write($rs . 'sss', 'WARN');
                foreach ($data['contents'] as $key => $val) {
                    $add_list = $add_list . $val['shop_name'] . ":" . $val['address'] . "\n";

                }

                $weObj->text("发现【" . $data['total'] . "】颗包菜\n" . $add_list)->reply();
                break;
            default:
                $weObj->text("help info")->reply();
        }


    }

    public function fy()
    {
        testSendFormatedMessage();
    }

    public function test()
    {

        if (sendMail('34206043@qq.com', "i d", 'hello'))
            echo 'success';
        //$this->success('发送成功！');
        else
            //$this->error('发送失败');
            echo "failure";
    }

    public function menu()
    {
        $options = array(
            'token' => '1qaz2wsx', //填写你设定的key
            'encodingaeskey' => 'bMY75kCr4C4tGDtKFNHjZ0I5ZKNDU42DQXAQzxGY5v0', //填写加密用的EncodingAESKey
            'appid' => 'wx8e9af23cb8d804e8', //填写高级调用功能的app id
            'appsecret' => '3208fc5407922bd7e3327f78febaa6eb' //填写高级调用功能的密钥
        );
        //获取菜单操作:
        $weObj = new \Org\Wx\Wechat($options);
        //$weObj->valid();
        $menu = $weObj->getMenu();
        dump($menu);
        //设置菜单
        $newmenu = array(
            "button" =>
                array(
                    array('type' => 'click', 'name' => '捣蛋', 'key' => 'MENU_KEY_NEWS'),
                    array('type' => 'view', 'name' => '我爱包菜', 'url' => 'http://www.baocai.us'),
                )
        );
        dump($newmenu);
        $result = $weObj->createMenu($newmenu);
    }

    /**
     *
     */
    public function kdt()
    {
        Vendor('Kdt.lib.KdtApiClient');
        $appId = '74a4bcc3b638a70415';
        $appSecret = '91b7dffe7314369b44f2a1cc79b39695';
        $client = new \KdtApiClient($appId, $appSecret);


        $method = 'kdt.trades.sold.get';
        $params = [
            'page_size' => 50
        ];


        $rs = $client->get($method, $params);
        foreach ($rs as $key => $val) {
            foreach ($val['trades'] as $key2 => $val2) {
                echo ($val2['title'] . '--' . $val2['buyer_nick']) . '<br>' . '<br>';
            }

        }


    }


}