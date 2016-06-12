<?php

namespace Home\Controller;

use Think\Controller;
use Think\Log\Driver\Sae;
use Think\Think;

include 'FeyinAPI.php';
Vendor('Kdt.lib.SimpleHttpClient');
Vendor('Kdt.lib.KdtApiClient');
Vendor('Kdt.lib.KdtApiOauthClient');


class IndexController extends Controller
{

    public function index()
    {
        $this->display();
    }

    public function wx()
    {

        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];

        dump($options);
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
                $pr_status = sendFreeMessage($freeMessage);
//                $pr_status = 1;
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
                $Geo = $weObj->getRevGeo();
                $Location_X = $Geo['x'];
                $Location_Y = $Geo['y'];
//                $weObj->text("zuobiao:".$Location_X.",".$Location_Y)->reply();
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

        //S('wx_$access_token',null);
        $access_token = $this->getWxtoken();
        $client = new \KdtApiOauthClient();
        $method = 'kdt.trades.sold.get';
        $params = [
            'page_size' => 50
        ];

        $rs = $client->get($access_token, $method, $params);
        foreach ($rs as $key => $val) {
            foreach ($val['trades'] as $key2 => $val2) {
                echo ($val2['title'] . '--' . $val2['buyer_nick']) . '<br>' . '<br>';
            }

        }

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

    public function callback()
    {
        Vendor('Kdt.lib.KdtApiOauthClient');
        $code = $_GET['code'];
        $state = $_GET['state'];
        if ($state == 'bc') {
            $redirect_uri = 'http://baocai.vip.natapp.cn/man/?m=home&c=index&a=callback';
            $params = [
                'client_id' => '62100d9ae0aa97dd6e',
                'client_secret' => '15ee3653cd2f28e255ba09f9d999389d',
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirect_uri
            ];
//            dump($params);
            $rs = \SimpleHttpClient::post('https://open.koudaitong.com/oauth/token', $params);
            $data = json_decode($rs, true);

            $access_token = $data['access_token'];
            S('wx_$access_token', $access_token, $data['expires_in']);
            $this->test();
        } else {
            echo true;
        }

    }

    private function getWxtoken()
    {
        $access_token = S('wx_$access_token');
        if ($access_token) {
            return $access_token;
        } else {
            $redirect_uri = 'http://baocai.vip.natapp.cn/man/?m=home&c=index&a=callback';
            redirect("https://open.koudaitong.com/oauth/authorize?client_id=62100d9ae0aa97dd6e&response_type=code&state=bc&redirect_uri=http://baocai.vip.natapp.cn/man/?m=home%26c=index%26a=callback", 2, '页面跳转中...');
        }
    }


}