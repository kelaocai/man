<?php
namespace Demo\Controller;

use Think\Controller;

class IndexController extends Controller
{

    const MENU_KEY_NEWUSER = 'newuser';
    const MENU_KEY_PROGRESS = 'progress';


    public function index()
    {
        $this->assign('openid', I('get.openid'));
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

        $HOST_URL = 'http://baocai.vip.natapp.cn';

        $weObj = new \Org\Wx\Wechat($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();
        switch ($type) {
            case \Org\Wx\Wechat::MSGTYPE_TEXT:


//                $weObj->text("发送<a href='http://baocai.vip.natapp.cn/man/?m=demo&c=index&a=index'>aa</a>")->reply();
//                $this->sendmb($weObj, 'oVT3TjgfVHR9Gryv4gco-ZcIBv4k');
                exit;
                break;
            case \Org\Wx\Wechat::MSGTYPE_EVENT:
                $key = $weObj->getRevEvent()['key'];
                switch ($key) {
                    case MENU_KEY_NEWUSER:

                        $user_openid = $weObj->getRev()->getRevFrom();
                        $url = $HOST_URL . U('Demo/index/index', array('openid' => $user_openid));
                        session('wx_user.openid', $user_openid);
                        $weObj->text('Demo:' . "<a href='$url'>新用户注册</a>")->reply();
                        break;
                    case MENU_KEY_PROGRESS:
                        $url = $HOST_URL . U('Demo/index/jd');
                        $weObj->text('Demo:' . "<a href='$url'>检测报告进度</a>")->reply();
                        break;
                }
                break;
            case \Org\Wx\Wechat::MSGTYPE_IMAGE:
                break;
            case \Org\Wx\Wechat::MSGTYPE_LOCATION:
                break;
            default:
                $weObj->text("help info")->reply();
        }


    }

    public function menu()
    {

        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];

        $weObj = new \Org\Wx\Wechat($options);

        $btn_new = array('name' => '新用户注册', 'type' => 'click', 'key' => MENU_KEY_NEWUSER);
        $btn_jd = array('name' => '查看进度', 'type' => 'click', 'key' => MENU_KEY_PROGRESS);
        $sub_btn[0] = $btn_new;
        $sub_btn[1] = $btn_jd;

        $newmenu = array(
            "button" =>
                array(
                    array('name' => 'Demo', 'sub_button' => $sub_btn)
                )
        );
        $result = $weObj->createMenu($newmenu);
        echo "result:" . $result;
    }

    private function sendmb($weObj, $to_openid)
    {

//        $options = [
//            'token' => C('WX_TOKEN'), //填写你设定的key
//            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
//            'appid' => C('WX_APPID'), //填写高级调用功能的app id
//            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
//        ];
//
//        $weObj = new \Org\Wx\Wechat($options);

        $data = array('touser' => $to_openid);
        $data['template_id'] = 'FrZZwNbeXO8veuokFCzzcU9SZg2m1UbX3ohykOEdn_Q';
        $data['topcolor'] = '#FF0000';
        $data['url'] = 'http://baocai.vip.natapp.cn/man/?m=demo&c=index&a=jd';

        $paramter_data = array('msg' => array('value' => '样本签收', "color" => "#3cc51f"));
        $paramter_data['time'] = array('value' => date('Y-m-d', time()));
        $paramter_data['name'] = array('value' => '瑞享瘦', "color" => '#0338bf');

        $data['data'] = $paramter_data;

        return $weObj->sendTemplateMessage($data);


    }

    public function jd()
    {
//        echo U('Demo/index/jd');
        $this->display();
    }

    public function createUser()
    {
        $bj = M("user", "ss_", "DB_CONFIG_APP");
        session('wx_user.openid', I('post.openid'));
        session('wx_user.email', I('post.emai'));
        session('wx_user.name', I('post.real_name'));

        $data['email'] = I('post.email');
        $data['name'] = I('post.real_name');
        $data['openid'] = I('post.openid');
//        $data['nickname']=I('post.real_name');
        $data['createdate'] = date('y-m-d h:i:s', time());
        $bj->add($data);
        $url = 'http://' . $_SERVER['HTTP_HOST']  . U('Demo/index/order');
        $rs = array('message' => 'hello', 'url' => $url);
//        $data = json_decode($rs, true);
        $this->ajaxReturn($rs);
    }

    public function order()
    {
        $this->assign('orderno', 'RA' . time());
        $this->assign('wx_user', session('wx_user'));
        $this->display();
//        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Demo/index/order');
//        echo $url;
    }

    public function createOrder()
    {
        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];


        $weObj = new \Org\Wx\Wechat($options);


        $to_openid = session('wx_user.openid');
        $this->sendmb($weObj, $to_openid);

        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . U('Demo/index/jd');
        $rs = array('message' => 'hello', 'url' => $url);
        $this->ajaxReturn($rs);

//
    }

}