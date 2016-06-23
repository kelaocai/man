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
//        $access_token = $this->getWxtoken();
//        $client = new \KdtApiOauthClient();
//        $method = 'kdt.trades.sold.get';
//        $params = [
//            'page_size' => 50
//        ];
//
//        $list = $client->get($access_token, $method, $params);
////        dump($list);
//        $this->assign('list', $list["response"]['trades']);
        

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
//                    $add_list = $add_list . $val['address'] . "\n";

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

//        S('wx_$access_token',null);
//        $access_token = $this->getWxtoken();
//        $client = new \KdtApiOauthClient();
//        $method = 'kdt.trades.sold.get';
//        $params = [
//            'page_size' => 50
//        ];
//
//        $rs = $client->get($access_token, $method, $params);
//        foreach ($rs as $key => $val) {
//            foreach ($val['trades'] as $key2 => $val2) {
//                echo ($val2['title'] . '--' . $val2['buyer_nick']) . '<br>' . '<br>';
//            }
//
//        }
        $num = 23.55;
//        echo U('home/index/plan/?serial_number=1');
        $handle = \printer_open();
        printer_write($handle, "Text to print");
        printer_close($handle);

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

    public function jsj()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        S('jsj_' . $data['entry']['serial_number'], $data, 24 * 3600 * 7);
        \Think\Log::record(file_get_contents("php://input"), 'INFO');
        $this->display();
    }

    public function plan_wait()
    {
        redirect(U('home/index/plan/?serial_number=' . $_GET['serial_number']), 3, '生成合同中');
    }

    public function plan()
    {

//        根据金数据传递的表单序号生成合同
        $data = S('jsj_' . $_GET['serial_number']);
        \Think\Log::record('cache:' . $data['form_name'], 'INFO');
        $this->assign('jsj_data', $data['entry']);
        //计算服务费
        $calc_fw = $data['entry']['field_18'] * $data['entry']['field_19'];
        $this->assign('calc_fw', $calc_fw . " (" . \Org\Util\Num2Cny::ParseNumber($calc_fw) . ")");
        //计算服务费
        $calc_total = $calc_fw + $data['entry']['field_10'] + $data['entry']['field_11'] + $data['entry']['field_12'] + $data['entry']['field_13'];
        $this->assign('calc_total', $calc_total . " (" . \Org\Util\Num2Cny::ParseNumber($calc_total) . ")");

        $this->display();
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

    public function map()
    {
//        $url = "http://api.map.baidu.com/geodata/v3/geotable/list";
        $url = "http://api.map.baidu.com/geodata/v3/poi/list";
        $params = array('geotable_id' => 143034,
            'ak'=>'NLBwYBHxe2AIx7YPmaAQenG5',
            'sn'=>''
        );

//        $rs = \SimpleHttpClient::get($url, $params);
//        $data = json_decode($rs, true);
//        dump($data);
        $this->assign('post_url',U('home/index/createShop'));
        $this->display();

    }
    
    public function getPoint(){
//        $url = "http://api.map.baidu.com/geodata/v3/geotable/list";
        $url = "http://api.map.baidu.com/geosearch/v3/local";
        $params = array('geotable_id' => 143034,
            'ak'=>C('BAIDU_MAP_AK'),
            'sn'=>'',
            'page_size'=>50
        );
        $rs = \SimpleHttpClient::get($url, $params);
        $data = json_decode($rs, true);
        $this->ajaxReturn($data);
    }

    public function createShop(){
//        echo 'shop';
        $latitude=$_POST['lat'];
        $longitude=$_POST['lng'];
        $shop_name=$_POST['shop_name'];
        $address=$_POST['shop_address'];

        $url="http://api.map.baidu.com/geodata/v3/poi/create";
        $params=array(
            'geotable_id' => 143034,
            'coord_type'=>3,
            'ak'=>'NLBwYBHxe2AIx7YPmaAQenG5',
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'shop_name'=>$shop_name,
            'title'=>$shop_name,
            'address'=>$address
        );
        $rs = \SimpleHttpClient::post($url, $params);
        $data = json_decode($rs, true);
        redirect(U('home/index/map'));
    }

    public function ip(){
        \Think\Log::write('ip:'.I('get.ip',0));
        $pr_status=sendMail('34206043@qq.com','RasPi上线了',"the ip is :".I('get.ip',0));
        echo "the ip is :".I('get.ip',0).",mail_status:".$pr_status;
    }


}