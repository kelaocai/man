<?php
namespace Demo\Controller;

use Think\Controller;

Vendor('kf5.Client');
Vendor('kf5.core.apiRequire');
Vendor('PhpWord.PHPWord');

class IndexController extends Controller
{

    const MENU_KEY_NEWUSER = 'newuser';
    const MENU_KEY_PROGRESS = 'progress';
    const MENU_KEY_SCAN = 'scan';


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

        $HOST_URL = 'http://' . $_SERVER['HTTP_HOST'];

        $weObj = new \Org\Wx\Wechat($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();
        switch ($type) {
            case \Org\Wx\Wechat::MSGTYPE_TEXT:
                exit;
                break;
            case \Org\Wx\Wechat::MSGTYPE_EVENT:
                if ($key = $weObj->getRevEvent()['key']) {
                    switch ($key) {
                        case MENU_KEY_NEWUSER:

                            $user_openid = $weObj->getRev()->getRevFrom();
                            $url = $HOST_URL . U('Demo/index/index', array('openid' => $user_openid));
                            session('wx_user.openid', $user_openid);
                            $weObj->text("<a href='$url'>新用户注册,点击开始</a>")->reply();
                            break;
                        case MENU_KEY_PROGRESS:

                            //取用户信息
                            $user_openid = $weObj->getRev()->getRevFrom();
                            $user = M("user", "ss_", "DB_CONFIG_APP");
                            $map = array('openid' => $user_openid);
                            $data_user = $user->where($map)->order('id desc')->find();
                            //取最新的订单记录
                            $orders = M("order", "ss_", "DB_CONFIG_APP");
                            $map = array('userid' => $data_user['id']);
                            $order = $orders->where($map)->order('createdate desc')->find();

                            $url = $HOST_URL . U('Demo/index/jd', 'kfid=' . $order['kfid']);
                            $progress_status = $order['status'] * 20;
                            $weObj->text("<a href='$url'>检测报告进度[$progress_status%],点击查看</a>")->reply();
                            break;

                        case MENU_KEY_SCAN:
                            $weObj->text($weObj->getRevScanInfo()['ScanResult'])->reply();
                            break;


                    }
                } else {
                    $event = $weObj->getRevEvent()['event'];
                    switch ($event) {
                        case EVENT_MENU_SCAN_WAITMSG:

                            break;
                    }
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
        $btn_scan = array('name' => '扫码条码', 'type' => 'scancode_waitmsg', 'key' => MENU_KEY_SCAN);
        $btn_bd = array('name' => '条码绑定', 'type' => 'view', 'url' => $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Demo/index/scan'));

        $sub_btn[0] = $btn_new;
        $sub_btn[1] = $btn_jd;
        $sub_btn[2] = $btn_scan;
        $sub_btn[3] = $btn_bd;

        $newmenu = array(
            "button" =>
                array(
                    array('name' => 'Demo', 'sub_button' => $sub_btn)
                )
        );
        $result = $weObj->createMenu($newmenu);
        echo "result:" . $result;
    }

    private function sendmb($weObj, $to_openid, $msg, $url)
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
        $data['url'] = $url;

        $paramter_data = array('msg' => array('value' => $msg, "color" => "#3cc51f"));
        $paramter_data['time'] = array('value' => date('Y-m-d', time()));
        $paramter_data['name'] = array('value' => '瑞享瘦', "color" => '#0338bf');

        $data['data'] = $paramter_data;

        return $weObj->sendTemplateMessage($data);


    }

    public function jd()
    {

        //取订单信息
        $orders = M("order", "ss_", "DB_CONFIG_APP");
        $kf_id = I('get.kfid');
        $map = array('kfid' => $kf_id);
        $order = $orders->where($map)->order('createdate desc')->find();
        $this->assign('order_progress', $order['status'] * 20);
        switch ($order['status']) {
            case 1:
                $this->assign('order_status', '采样器寄出');
                break;
            case 2:
                $this->assign('order_status', '样品收到');
                break;
            case 3:
                $this->assign('order_status', '送检中');
                break;
            case 4:
                $this->assign('order_status', '检测结果分析');
                break;
            case 5:
                $this->assign('order_status', '检测报告完成');
                break;
            default:
                $this->assign('order_status', '暂无进度');
        }


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
        $data['createdate'] = date('Y-m-d h:i:s', time());
        $bj->add($data);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Demo/index/order');
        $rs = array('message' => '', 'url' => $url);
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


        $kf_domain = C('KF_DOMAIN');
        $kf_email = C('KF_EMAIL');
        $kf_token = C('KF_TOKEN');
        $kf_password = C('KF_PASSWORD');

        $kf = new \Client($kf_domain, $kf_email);
        $kf->setAuth('password', $kf_password);

        //取用户信息
        $openid = I('post.openid');
        $orderno = I('post.orderno');
        $user = M("user", "ss_", "DB_CONFIG_APP");
        $map = array('openid' => $openid);
        $data_user = $user->where($map)->order('id desc')->find();


        $kf_order = array(
            'title' => '订单[' . $orderno . ']-瑞享瘦-' . $data_user['name'],
            'comment' => array('content' => '订单[' . $orderno . ']'), 'requester' => array('email' => $data_user['email'], 'name' => $data_user['name'])
        );

        $kf_data = $kf->tickets()->create($kf_order);

        $kf_id = $kf_data->ticket->id;

        $order = M("order", "ss_", "DB_CONFIG_APP");
        $new_order = array('orderno' => $orderno, 'userid' => $data_user['id'], 'itemid' => '1', 'createdate' => date('Y-m-d h:i:s', time()), 'status' => '0', 'kfid' => $kf_id);
        $order->add($new_order);

        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Demo/index/jd', 'kfid=' . $kf_id);
        $rs = array('message' => $kf_id, 'url' => $url);
        $this->ajaxReturn($rs);


//
    }

    public function hook()
    {

        $msg = I('get.msg');
        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];

        $HOST_URL = 'http://' . $_SERVER['HTTP_HOST'];

        $data = explode('|', $msg);

        //取订单信息
        $orders = M("order", "ss_", "DB_CONFIG_APP");
        $kf_id = $data[1];
        $map = array('kfid' => $kf_id);
        $order = $orders->where($map)->order('createdate desc')->find();
        //更新订单信息
        switch ($data[0]) {
            case '采样器寄出':
                $order['status'] = 1;
                break;
            case '样品收到':
                $order['status'] = 2;
                break;
            case '送检中':
                $order['status'] = 3;
                break;
            case '检测结果分析':
                $order['status'] = 4;
                break;
            case '检测报告完成':
                $order['status'] = 5;
                break;
        }
        $orders->where('kfid=' . $data[1])->save($order);


        $users = M("user", "ss_", "DB_CONFIG_APP");
        $map = array('id' => $order['userid']);
        $user = $users->where($map)->order('id desc')->find();


        $weObj = new \Org\Wx\Wechat($options);
        $url = $HOST_URL . U('Demo/index/jd', 'kfid=' . $kf_id);
        $this->sendmb($weObj, $user['openid'], $data[0], $url);

    }


    public function scan()
    {
        $options = [
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        ];

        $weObj = new \Org\Wx\Wechat($options);

        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $js_sign = $weObj->getJsSign($url);
        $this->assign('js_sign', $js_sign);
        $this->display();
    }

    public function test()
    {
//        $date = "04|30|1973";
//        $haha=explode('|',$date);
//        dump($haha);

        echo date('Y-m-d h:i:s', time());
    }

    public function word()
    {
// New Word Document
        $PHPWord = new \PHPWord();

// New portrait section
        $section = $PHPWord->createSection();

// Add text elements
        $section->addText('Hello World! 中文');
        $section->addTextBreak(2);

        $section->addText('I am inline styled.', array('name' => 'Verdana', 'color' => '006699'));
        $section->addTextBreak(2);

        $PHPWord->addFontStyle('rStyle', array('bold' => true, 'italic' => true, 'size' => 16));
        $PHPWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));
        $section->addText('I am styled by two style definitions.', 'rStyle', 'pStyle');
        $section->addText('I have only a paragraph style definition.', null, 'pStyle');


// Save File
        $objWriter = \PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
        $objWriter->save('Text.docx');

    }


}