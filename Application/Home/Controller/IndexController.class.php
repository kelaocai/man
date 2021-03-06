<?php

namespace Home\Controller;

use Think\Controller;
use Think\Log\Driver\Sae;
use Think\Think;

Vendor('Feyin.FeyinAPI');
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


        $HOST_URL = 'http://' . $_SERVER['HTTP_HOST'];

        $weObj = new \Org\Wx\Wechat($options);
        $weObj->valid();
        $type = $weObj->getRev()->getRevType();
        \Think\Log::record('微信消息代码:' . $type, 'INFO');
        switch ($type) {
            case \Org\Wx\Wechat::MSGTYPE_TEXT:
                $weObj->text('jajaj')->reply();
                break;
            case \Org\Wx\Wechat::MSGTYPE_EVENT:
                if (isset($weObj->getRevEvent()['key'])) {
                    $key = $weObj->getRevEvent()['key'];
                    \Think\Log::record('微信EventKey:' . $key, 'INFO');
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
                    \Think\Log::record('微信事件代码:' . $event, 'INFO');
                    switch ($event) {
                        case \Org\Wx\Wechat::EVENT_CARD_USER_GET:
                            $card = $weObj->getRev()->getRevCardGet();
                            $user_openid = $weObj->getRev()->getRevFrom();
                            \Think\Log::record('卡券领取:' . $user_openid . '，code:' . $card['UserCardCode'], 'WARN');
                            break;

                        case  \Org\Wx\Wechat::EVENT_CARD_PASS:
                            $cardid = $weObj->getRev()->getRevCardPass();
                            \Think\Log::record('卡券审核通过，cardid' . $cardid, 'WARN');
                            break;
                        case  \Org\Wx\Wechat::EVENT_CARD_NOTPASS:
                            $cardid = $weObj->getRev()->getRevCardPass();
                            \Think\Log::record('卡券未通过，cardid' . $cardid, 'WARN');
                            break;

                        case  \Org\Wx\Wechat::EVENT_MERCHANT_ORDER:
                            $order_id = $weObj->getRev()->getRevOrderId();
                            $order_info = $weObj->getOrderByID($order_id);
                            \Think\Log::record('order_info' . json_encode($order_info), 'WARN');

//                            $prd_name=explode('$',$order_info['product_sku']);
                            $prd_name=$order_info['product_name'];
                            $prd_price=$order_info['product_price'];
                            $prd_count=$order_info['product_count'];

                            $msgInfo = array (
                                'memberCode'=>MEMBER_CODE,
                                'charge'=>$order_info['product_price'],
                                'customerName'=>$order_info['buyer_nick'],
                                'customerPhone'=>$order_info['receiver_mobile'],
                                'customerAddress'=>$order_info['receiver_province'].$order_info['receiver_address'],
                                'customerMemo'=>'请快点送货',
                                'msgDetail'=>$prd_name.'@10'.'@'.$prd_count
                            );

                            $this->printXp($msgInfo);
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


    public function fy()
    {
//        testSendFormatedMessage();

        //飞印传递信息
        $xp_msg = I('post.xp_msg');
        $xp_shopid = I('post.xp_shopid');

        $device_no = '9982172973682014';
        $msgNo = time() + 1;

        switch ($xp_shopid) {
            case 1:
                $device_no = '4600042606700803';
                break;
            case 2:
                $device_no = '4600042606700825';
                break;
            default:
                $device_no = '9982172973682014';
        }


        $freeMessage = array(
            'memberCode' => MEMBER_CODE,
            'msgDetail' => $xp_msg,
            'deviceNo' => $device_no,
            'msgNo' => $msgNo,
        );

        $rs = '发送状态:' . sendFreeMessage($freeMessage) . '<br> 发送设备号:' . $device_no;

        $this->assign('rs', $rs);
        $this->display();

    }


    public function printXp($msg=['memberCode'=>MEMBER_CODE])
    {

        $msg['memberCode']=MEMBER_CODE;
        $msg['companyName']='米兜烘焙店-';
        $msg['deviceNo']='4600042606700825';
        $msg['msgNo']=time() + 1;

//        $freeMessage = array(
//            'memberCode' => MEMBER_CODE,
//            'msgDetail' =>$msg,
//            'deviceNo' => $device_no,
//            'msgNo' => $msgNo,
//        );

        $msgNo = time()+1;
        /*
         格式化的打印内容
        */
//        $msgInfo = array (
//            'memberCode'=>MEMBER_CODE,
//            'charge'=>'3000',
//            'customerName'=>'老财',
//            'customerPhone'=>'13321332245',
//            'customerAddress'=>'翰岭院书房',
//            'customerMemo'=>'请快点送货',
//            'msgDetail'=>'$购买商品:$小兔软糖@1000@1',
//            'deviceNo'=>'4600042606700825',
//            'msgNo'=>$msgNo,
//        );

        echo sendFormatedMessage($msg);

    }

    public function xp()
    {
        //打印小票
        $this->assign('post_url', U('home/index/fy'));
        $this->display();

    }

    public function test()
    {

//        $options = [
//            'token' => C('WX_TOKEN'), //填写你设定的key
//            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
//            'appid' => C('WX_APPID'), //填写高级调用功能的app id
//            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
//        ];
//
//        $weObj = new \Org\Wx\Wechat($options);
//        $auth = $weObj->checkAuth();
//        echo $auth;

        $a='$ss:$2rt';
        $b=explode('$',$a);
        echo $b[2];


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
            'page_size' => 50,
            'status' => 'WAIT_BUYER_CONFIRM_GOODS',
            'start_created' => '2016-07-01'
        ];


        $rs = $client->get($method, $params);
        foreach ($rs as $key => $val) {
            foreach ($val['trades'] as $key2 => $val2) {
                foreach ($val2['orders'] as $key3 => $val3) {
                    echo ($val3['sku_properties_name'] . '--' . $val2['payment'] . '--' . $val2['num']) . '<br>' . '<br>';
                }

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
        $this->assign('today', date("Y-m-d"));
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
            'ak' => 'NLBwYBHxe2AIx7YPmaAQenG5',
            'sn' => ''
        );

//        $rs = \SimpleHttpClient::get($url, $params);
//        $data = json_decode($rs, true);
//        dump($data);
        $this->assign('post_url', U('home/index/createShop'));
        $this->display();

    }

    public function getPoint()
    {
//        $url = "http://api.map.baidu.com/geodata/v3/geotable/list";
        $url = "http://api.map.baidu.com/geosearch/v3/local";
        $params = array('geotable_id' => 143034,
            'ak' => C('BAIDU_MAP_AK'),
            'sn' => '',
            'page_size' => 50
        );
        $rs = \SimpleHttpClient::get($url, $params);
        $data = json_decode($rs, true);
        $this->ajaxReturn($data);
    }

    public function createShop()
    {
//        echo 'shop';
        $latitude = $_POST['lat'];
        $longitude = $_POST['lng'];
        $shop_name = $_POST['shop_name'];
        $address = $_POST['shop_address'];

        $url = "http://api.map.baidu.com/geodata/v3/poi/create";
        $params = array(
            'geotable_id' => 143034,
            'coord_type' => 3,
            'ak' => 'NLBwYBHxe2AIx7YPmaAQenG5',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'shop_name' => $shop_name,
            'title' => $shop_name,
            'address' => $address
        );
        $rs = \SimpleHttpClient::post($url, $params);
        $data = json_decode($rs, true);
        redirect(U('home/index/map'));
    }

    public function ip()
    {
        \Think\Log::write('ip:' . I('get.ip', 0));
        $pr_status = sendMail('34206043@qq.com', 'RasPi上线了', "the ip is :" . I('get.ip', 0));
        echo "the ip is :" . I('get.ip', 0) . ",mail_status:" . $pr_status;
    }

    public function travel()
    {
        $this->display();
    }

    public function ddz_dk()
    {
        //点点折每日打款报表

        $report = M("taoke_report_settle", "ddz_", "DB_CONFIG_DDZ");


        //当日现金打款条数和金额
        $conditons = array("OUTCODE_TYPE" => 'B', "SETTLE_STATUS" => 'U');
        $today_toal_cash = $report->where($conditons)->count();
        $today_sum_cash = round($report->where($conditons)->sum('SETTLE_FEE'), 2);

        //当日集分宝打款条数和金额
        $conditons = array("OUTCODE_TYPE" => 'J', "SETTLE_STATUS" => 'U');
        $today_toal_jfb = $report->where($conditons)->count();
        $today_sum_jfb = round($report->where($conditons)->sum('SETTLE_JFB') / 100, 2);

        $data_today['today_toal_cash'] = $today_toal_cash;
        $data_today['today_sum_cash'] = $today_sum_cash;
        $data_today['today_toal_jfb'] = $today_toal_jfb;
        $data_today['today_sum_jfb'] = $today_sum_jfb;

        $this->assign('data_today', $data_today);
        $this->display();

    }

    public function pay()
    {
//        $api_key = 'sk_test_mLKSC00uDufPj9G48CyfvrnD';
        $api_key = 'sk_live_5WLKO0XXzLC0DSSi5SPejvHS';
        $app_id = 'app_4q9CqP1GGq1OPaPC';
        \Pingpp\Pingpp::setApiKey($api_key);
        $channel = 'wx_pub';
        $extra = array();
        switch ($channel) {
            case 'alipay_wap':
                $extra = array(
                    'success_url' => 'http://example.com/success',
                    'cancel_url' => 'http://example.com/cancel'
                );
                break;
            case 'alipay_pc_direct':
                $extra = array(
                    'success_url' => 'http://example.com/success',
                );
                break;
            case 'bfb_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result',
                    'bfb_login' => true
                );
                break;
            case 'upacp_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result'
                );
                break;
            case 'wx_pub':
                $extra = array(
                    'open_id' => 'ozolMuAnTRGa3UUqyJFEFiGEZOy0'
                );
                break;
            case 'wx_pub_qr':
                $extra = array(
                    'product_id' => '008291'
                );
                break;
            case 'yeepay_wap':
                $extra = array(
                    'product_category' => '1',
                    'identity_id' => 'your identity_id',
                    'identity_type' => 1,
                    'terminal_type' => 1,
                    'terminal_id' => 'your terminal_id',
                    'user_ua' => 'your user_ua',
                    'result_url' => 'http://example.com/result'
                );
                break;
            case 'jdpay_wap':
                $extra = array(
                    'success_url' => 'http://example.com/success',
                    'fail_url' => 'http://example.com/fail',
                    'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf'
                );
                break;
        }

        $charge = \Pingpp\Charge::create(array(
            'order_no' => substr(time(), 0, 30),
            'amount' => '1',//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
            'app' => array('id' => $app_id),
            'channel' => $channel,
            'currency' => 'cny',
            'client_ip' => '127.0.0.1',
            'subject' => 'Your Subject',
            'body' => 'Your Body',
            'extra' => $extra
        ));

        $this->assign('charge', $charge);
        $this->display();
    }

    public function red()
    {
        $api_key = 'sk_test_mLKSC00uDufPj9G48CyfvrnD';
//        $api_key = 'sk_live_5WLKO0XXzLC0DSSi5SPejvHS';
        $app_id = 'app_4q9CqP1GGq1OPaPC';
        \Pingpp\Pingpp::setApiKey($api_key);

        //微信红包
        $red = \Pingpp\RedEnvelope::create(
            array(
                'subject' => '包菜爱你',
                'body' => '坚持就是胜利,今天的努力是对未来的救赎',
                'amount' => 199,//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                'order_no' => substr(time(), 0, 30),
                'currency' => 'cny',
                'extra' => array(
                    'send_name' => '包菜仔'
                ),
                'recipient' => 'ozolMuAnTRGa3UUqyJFEFiGEZOy0',//发送红包给指定用户的 open_id
                'channel' => 'wx_pub',//此处 wx_pub 为公众平台的支付
                'app' => array('id' => $app_id),
                'description' => '恭喜发财'
            )
        );
        echo $red;
    }

    public function transfer()
    {
        //微信企业转账
//        $api_key = 'sk_test_mLKSC00uDufPj9G48CyfvrnD';
        $api_key = 'sk_live_5WLKO0XXzLC0DSSi5SPejvHS';
        $app_id = 'app_4q9CqP1GGq1OPaPC';
        \Pingpp\Pingpp::setApiKey($api_key);
        try {
            $tr = \Pingpp\Transfer::create(
                array(
                    'amount' => 100,
                    'order_no' => date('YmdHis') . (microtime(true) % 1) * 1000 . mt_rand(0, 9999),
                    'currency' => 'cny',
                    'channel' => 'wx_pub',
                    'app' => array('id' => $app_id),
                    'type' => 'b2c',
                    'recipient' => 'ozolMuAnTRGa3UUqyJFEFiGEZOy0',
                    'description' => 'testing',
                    'extra' => array('user_name' => 'laocai', 'force_check' => false)
                )
            );
            echo $tr;
        } catch (\Pingpp\Error\Base $e) {
            header('Status: ' . $e->getHttpStatus());
            echo($e->getHttpBody());
        }
    }

    public function webhooks()
    {
        //接受ping++ webhooks事件通知
        $raw_data = file_get_contents('php://input');
        $event = json_decode($raw_data, true);
        if ($event['type'] == 'charge.succeeded') {
            $charge = $event['data']['object'];
            // ..
            http_response_code(200); // PHP 5.4 or greater
        } elseif ($event['type'] == 'refund.succeeded') {
            $refund = $event['data']['object'];
            // ...
            http_response_code(200); // PHP 5.4 or greater
        } else {
            /**
             * 其它类型 ...
             * - summary.daily.available
             * - summary.weekly.available
             * - summary.monthly.available
             * - transfer.succeeded
             * - red_envelope.sent
             * - red_envelope.received
             * ...
             */
            http_response_code(200);

            // 异常时返回非 2xx 的返回码
            // http_response_code(400);
        }

    }

    public function wxjs()
    {

        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];

        $weObj = new \Org\Wx\Wechat($options);
        $auth = $weObj->checkAuth();
        $js_ticket = $weObj->getJsTicket();

        if (!$js_ticket) {
            echo "获取js_ticket失败！<br>";
            echo '错误码：' . $weObj->errCode;
            echo ' 错误原因：' . ErrCode::getErrText($weObj->errCode);
            exit;
        } else {
        }
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $js_sign = $weObj->getJsSign($url);
        $this->assign('js_sign', $js_sign);
        $this->display();

    }

    private function get_js_sign()
    {


        if (!$rs_js_sign = S('js_sign')) {

            $options = [
                'token' => C('WX_TOKEN'), //填写你设定的key
                'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
                'appid' => C('WX_APPID'), //填写高级调用功能的app id
                'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
            ];

            $weObj = new \Org\Wx\Wechat($options);
            $auth = $weObj->checkAuth();
            $js_ticket = $weObj->getJsTicket();

            if (!$js_ticket) {
                echo "获取js_ticket失败！<br>";
                echo '错误码：' . $weObj->errCode;
                echo ' 错误原因：' . ErrCode::getErrText($weObj->errCode);
                exit;
            } else {
//            echo '获取js_ticket成功'.$js_ticket;
            }
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $js_sign = $weObj->getJsSign($url);
            S('js_sign', $js_sign, 24 * 3600);
            $rs_js_sign = $js_sign;

        }

        return $rs_js_sign;;
    }

    public function find_baocai()
    {
        $Location_X = I('post.latitude');
        $Location_Y = I('post.longitude');
        $params = [
            'ak' => 'lB3MdI4HADGDT8trntoLxOWR',
            'geotable_id' => 143034,
            'location' => $Location_Y . ',' . $Location_X,
            'radius' => 1500
        ];
        $rs = \SimpleHttpClient::get('http://api.map.baidu.com/geosearch/v3/nearby', $params);
        $data = json_decode($rs, true);
        $add_list = '';
//        \Think\Log::write($rs . 'sss', 'WARN');
        foreach ($data['contents'] as $key => $val) {
            $add_list = $add_list . $val['shop_name'] . ":" . $val['address'] . "\n";
        }

        $this->ajaxReturn("发现【" . $data['total'] . "】颗包菜\n" . $add_list);
    }

    public function create_shop()
    {
        $latitude = $_POST['lat'];
        $longitude = $_POST['lng'];
        $shop_name = $_POST['shop_name'];
        $address = $_POST['shop_address'];

        $url = "http://api.map.baidu.com/geodata/v3/poi/create";
        $params = array(
            'geotable_id' => 143034,
            'coord_type' => 3,
            'ak' => 'NLBwYBHxe2AIx7YPmaAQenG5',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'shop_name' => $shop_name,
            'title' => $shop_name,
            'address' => $address
        );
        $rs = \SimpleHttpClient::post($url, $params);
        $data = json_decode($rs, true);
        $this->ajaxReturn($data);
    }

    public function wx_zh()
    {
        $this->assign('post_url', U('home/index/createShop'));
        $this->display();
    }


    public function wx_bj()
    {
        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];

        $weObj = new \Org\Wx\Wechat($options);
        $auth = $weObj->checkAuth();
        $js_ticket = $weObj->getJsTicket();

        if (!$js_ticket) {
            echo "获取js_ticket失败！<br>";
            echo '错误码：' . $weObj->errCode;
            echo ' 错误原因：' . ErrCode::getErrText($weObj->errCode);
            exit;
        } else {
//            echo '获取js_ticket成功'.$js_ticket;
        }
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $js_sign = $weObj->getJsSign($url);
        $this->assign('js_sign', $js_sign);
        $this->assign('js_url', $url);
        $this->display();
    }

    public function bj()
    {
        $bj = M("bj", "ss_", "DB_CONFIG_APP");
        $data['data'] = $_POST['lat'] . ',' . $_POST['lng'];
        $data['time'] = date('Y-m-d H:i:s', time());
        $bj->add($data);
//        echo time();
    }


    public function wx_yao()
    {
        $options = [
            'token' => C('WX_TOKEN'), //填写你设定的key
            'encodingaeskey' => C('WX_ENCODINGAESKEY'), //填写加密用的EncodingAESKey
            'appid' => C('WX_APPID'), //填写高级调用功能的app id
            'appsecret' => C('WX_APPSECRET') //填写高级调用功能的密钥
        ];

        $weObj = new \Org\Wx\Wechat($options);
        $auth = $weObj->checkAuth();
        $js_ticket = $weObj->getJsTicket();

        if (!$js_ticket) {
            echo "获取js_ticket失败！<br>";
            echo '错误码：' . $weObj->errCode;
            echo ' 错误原因：' . ErrCode::getErrText($weObj->errCode);
            exit;
        } else {
//            echo '获取js_ticket成功'.$js_ticket;
        }
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $js_sign = $weObj->getJsSign($url);
        $this->assign('js_sign', $js_sign);
        $this->assign('js_url', $url);
        $this->assign('js_ticket', $js_ticket);
        $this->display();
    }


}