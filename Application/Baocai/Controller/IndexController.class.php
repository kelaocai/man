<?php
namespace Baocai\Controller;

use Think\Controller;

Vendor('Feyin.FeyinAPI');
Vendor('ELind.ELindAPI');
Vendor('kf5.Client');
Vendor('SensorsAnalytics.SensorsAnalytics');
Vendor('kf5.core.apiRequire');
Vendor('Kdt.lib.SimpleHttpClient');
Vendor('Kdt.lib.KdtApiClient');
Vendor('Kdt.lib.KdtApiOauthClient');


class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    public function tj()
    {
        //统计网点销售量
        $report = M("order", "bc_", "DB_CONFIG_BC");
        $conditons = array("shop_id" => '28', "status" => array('in', '1,2,3,4'));
        $conditons['pay_way'] = '1';
        $conditons['gmt_create'] = array('BETWEEN', '2016-04-01,2016-08-01');
        $today_toal_cnt = $report->where($conditons)->count();
        $today_sum_fee = round($report->where($conditons)->sum('pay_fee'), 2);
        echo $today_toal_cnt . ',' . $today_sum_fee;

    }

    public function kf()
    {
        $yourDomain = 'baocai';
        $email = 'kelaocai@163.com';
        $token = 'ac36ec15847c14496dc47f8ababb25';
        $password = '3edc4rfv';


        $test = new \Client($yourDomain, $email);
        $test->setAuth('password', $password);

        $json = array(
            'title' => '我来罗33',
            'comment' => array('content' => '明天的订单处理了吗?'), 'requester' => array('email' => '0003@qq.com', 'name' => '哈哈')
        );

        $data = $test->tickets()->create($json);
//        echo('aa');
        var_dump($data);
    }

    public function kfhook()
    {

        $yourDomain = 'baocai';
        $email = 'kelaocai@163.com';
        $token = 'ac36ec15847c14496dc47f8ababb25';
        $password = '3edc4rfv';


        $test = new \Client($yourDomain, $email);
        $test->setAuth('password', $password);

        $json = array(
            'title' => '收到hook' . time(),
            'comment' => array('content' => 'good:' . $_GET['msg']), 'requester' => array('email' => '0003@qq.com', 'name' => '哈哈')
        );

//        $data = $test->tickets()->create($json);

        echo 'hello';
    }

    public function word()
    {

        $word = new \Org\Util\Word();
        $word->start();

//        $content = $this->fetch('Home@index:wx_bj');
//        echo $content;
//以下内容会保存在WORD文件中，可以使用HTML标签
        ?>
        <h1>直接用php创建word文档</h1>
        作者：axgle
        <hr size=1>
        <p>如果你打开data.doc，看到了这里的介绍，则说明word文档创建成功了。
        <p>
            不论是在什么操作系统下，使用本方法都可以直接用PHP生成word文档。绝对不是吹牛！
            就算是没有安装word，也能够生成word文件。
            当然了，生成的word文件可以用word,wps或者其他软件打开。
        <p>
            <b>使用方法：</b>
            <br>
            首先用$word->start()表示要生成word文件了。
            然后你可以输出任何的HTML代码，不论是从文件读过来再写到这里，
            还是直接在这里输出HTML，都没有关系。

        <p>等你输出完毕后，用$word->save($path)方法，其中$path是你想
            生成的word文件的名称（可以给出完整的路径）.当你使用了$word->save()
            方法后，这后面的任何输出都和word文件没有关系了，也就是说word的生成
            工作就完成了。之后就和你平常使用php的方式一样拉。随便你输出什么东西，
            都直接在浏览器里输出，而不会写到word里面去。
        <p>这是本人想到的一个很有意思的方法，它的实现方法出人意料的简单，并且避免
            了对windows环境的依赖。
            <br>哈哈，很有意思吧？享受它吧！
        <hr size=1>
        <?php
//以上内容会保存在WORD文件中
        $word->save("data.doc");//保存word并且结束.
//以下内容正常输出在页面文件中
        header("Content-type:text/html;charset=utf-8");
        echo 'data.doc生成成功，请到目录下查看<br>';
    }

    public function sa()
    {
        $this->display();
    }


    public function sa_newuser()
    {
        # 从 Sensors Analytics 配置页面中获取的数据接收的 URL
        $SA_SERVER_URL = 'http://yikebaocai.cloud.sensorsdata.cn:8006/sa?token=5719721cfbe1ddf7';
        # 初始化一个 Consumer，用于数据发送
        $consumer = new \BatchConsumer($SA_SERVER_URL);
//        $consumer = new \DebugConsumer($SA_SERVER_URL);
        # 使用 Consumer 来构造 SensorsAnalytics 对象
        $sa = new \SensorsAnalytics($consumer);
        $os = array('IOS', 'Android', "Windows", 'Mac');
        $member = array('会员', '普通用户');
        $sex = array('男', '女');
        $shop = array('科兴旗舰店', '北邮店', "国际商会中心店", '壹海城店', 'CooPark店', '多丽工业区店');

        $cnt = I('post.cnt');

        # 模拟记录新用户注册事件
        for ($i = 0; $i < $cnt; $i++) {
            $distinct_id = $this->genRandStr();
            $login_properties = array('ShopName' => $shop[array_rand($shop, 1)], '$os' => $os[array_rand($os, 1)]);
            $properties = array(
                # 用户性别属性（Sex）为男性
                'Sex' => $sex[array_rand($sex, 1)],
                # 用户等级属性（Level）为 VIP
                'UserLevel' => $member[array_rand($member, 1)],
            );
            //echo ($distinct_id).'<br>';
//            dump($login_properties);


            # 设置用户属性
            $sa->profile_set($distinct_id, $properties);

            $sa->track($distinct_id, 'UserLogin', $login_properties);

            $sa->close();


        }
//        $sa->track(1234, 'OpenAPP');

        $rs = array('message' => '模拟成功[' . $cnt . ']', 'url' => '');
//        $this->ajaxReturn($rs);

    }

    public function test()
    {
//        $os = array('IOS', 'Android', "Windows", 'Mac');
//        echo array_rand($os, 1);
        $a="{\"app_id\":\"2c61af3095a3518214\",\"id\":\"E20160817224046099913866\",\"kdt_id\":2901,\"mode\":0,\"msg\":\"%7B%22trade%22:%7B%22num%22:1,%22goods_kind%22:1,%22num_iid%22:%22295087682%22,%22price%22:%220.01%22,%22pic_path%22:%22https://img.yzcdn.cn/upload_files/2016/03/01/FvRQstFfxp8sUIRH1t_ehls3b4_9.png%22,%22pic_thumb_path%22:%22https://img.yzcdn.cn/upload_files/2016/03/01/FvRQstFfxp8sUIRH1t_ehls3b4_9.png%3FimageView2/2/w/200/h/0/q/75/format/png%22,%22title%22:%22Demo-push%22,%22type%22:%22COD%22,%22discount_fee%22:%220.00%22,%22status%22:%22WAIT_SELLER_SEND_GOODS%22,%22status_str%22:%22%E5%BE%85%E5%8F%91%E8%B4%A7%22,%22refund_state%22:%22NO_REFUND%22,%22shipping_type%22:%22express%22,%22post_fee%22:%220.00%22,%22total_fee%22:%220.01%22,%22refunded_fee%22:%220.00%22,%22payment%22:%220.01%22,%22created%22:%222016-08-17%2022:40:46%22,%22update_time%22:%222016-08-17%2022:40:50%22,%22pay_time%22:%222016-08-17%2022:40:50%22,%22pay_type%22:%22CODPAY%22,%22consign_time%22:%22%22,%22sign_time%22:%22%22,%22buyer_area%22:%22%E5%B9%BF%E4%B8%9C%E7%9C%81%E6%B7%B1%E5%9C%B3%E5%B8%82%22,%22seller_flag%22:0,%22buyer_message%22:%22%22,%22orders%22:[%7B%22oid%22:17905600,%22outer_sku_id%22:%22%22,%22outer_item_id%22:%22%22,%22title%22:%22Demo-push%22,%22seller_nick%22:%22%E5%8C%85%E8%8F%9C%E7%BD%91%22,%22fenxiao_price%22:%220.00%22,%22fenxiao_payment%22:%220.00%22,%22price%22:%220.01%22,%22total_fee%22:%220.01%22,%22payment%22:%220.01%22,%22discount_fee%22:%220.00%22,%22sku_id%22:0,%22sku_unique_code%22:%22%22,%22sku_properties_name%22:%22%22,%22pic_path%22:%22https://img.yzcdn.cn/upload_files/2016/03/01/FvRQstFfxp8sUIRH1t_ehls3b4_9.png%22,%22pic_thumb_path%22:%22https://img.yzcdn.cn/upload_files/2016/03/01/FvRQstFfxp8sUIRH1t_ehls3b4_9.png%3FimageView2/2/w/200/h/0/q/75/format/png%22,%22item_type%22:0,%22buyer_messages%22:[],%22order_promotion_details%22:[],%22state_str%22:%22%E5%BE%85%E5%8F%91%E8%B4%A7%22,%22allow_send%22:1,%22is_send%22:0,%22item_refund_state%22:%22%22,%22is_virtual%22:0,%22num_iid%22:%22295087682%22,%22num%22:%221%22%7D],%22fetch_detail%22:null,%22coupon_details%22:[],%22promotion_details%22:[],%22adjust_fee%22:%7B%22change%22:%220.00%22,%22pay_change%22:%220.00%22,%22post_change%22:%220.00%22%7D,%22sub_trades%22:[],%22weixin_user_id%22:%2245524597%22,%22button_list%22:[%7B%22tool_icon%22:%22http://imgqn.koudaitong.com/upload_files/2015/08/28/FpFY_MeJXzLCA3lwIV6br6qUbClv.png%22,%22tool_title%22:%22%E5%8F%91%E8%B4%A7%22,%22tool_value%22:%22%22,%22tool_type%22:%22goto_native:trade_send_goods%22,%22tool_parameter%22:%22%7B%7D%22,%22new_sign%22:%220%22,%22create_time%22:%22%22%7D,%7B%22tool_icon%22:%22http://imgqn.koudaitong.com/upload_files/2015/08/28/Flv3HyyRB2DGyXsPwRqnZvA4pHla.png%22,%22tool_title%22:%22%E5%85%B3%E9%97%AD%22,%22tool_value%22:%22%22,%22tool_type%22:%22goto_native:trade_close%22,%22tool_parameter%22:%22%22,%22new_sign%22:%22%22,%22create_time%22:%22%22%7D,%7B%22tool_icon%22:%22http://imgqn.koudaitong.com/upload_files/2015/08/28/FpO1UIXyOEZO026tWIgUOm9uZnT2.png%22,%22tool_title%22:%22%E5%A4%87%E6%B3%A8%22,%22tool_value%22:%22%22,%22tool_type%22:%22goto_native:trade_memo%22,%22tool_parameter%22:%22%7B%7D%22,%22new_sign%22:%220%22,%22create_time%22:%22%22%7D],%22feedback_num%22:0,%22trade_memo%22:%22%22,%22fans_info%22:%7B%22fans_nickname%22:%22%E8%80%81%E8%B4%A2%22,%22fans_id%22:%2245524597%22,%22buyer_id%22:%22215015%22,%22fans_type%22:%221%22%7D,%22buy_way_str%22:%22%E8%B4%A7%E5%88%B0%E4%BB%98%E6%AC%BE%22,%22pf_buy_way_str%22:%22%E8%B4%A7%E5%88%B0%E4%BB%98%E6%AC%BE%22,%22send_num%22:0,%22user_id%22:%22215015%22,%22kind%22:1,%22relation_type%22:%22%22,%22relations%22:[],%22out_trade_no%22:[],%22group_no%22:%22%22,%22outer_user_id%22:0,%22shop_id%22:%220%22,%22points_price%22:0,%22buyer_nick%22:%22%E8%80%81%E8%B4%A2%22,%22tid%22:%22E20160817224046099913866%22,%22buyer_type%22:%221%22,%22buyer_id%22:%2245524597%22,%22receiver_city%22:%22%E6%B7%B1%E5%9C%B3%E5%B8%82%22,%22receiver_district%22:%22%E7%A6%8F%E7%94%B0%E5%8C%BA%22,%22receiver_name%22:%22%E5%90%B4%E5%9D%B7%E9%BA%9F%22,%22receiver_state%22:%22%E5%B9%BF%E4%B8%9C%E7%9C%81%22,%22receiver_address%22:%22%E7%BF%B0%E5%B2%AD%E9%99%A210_5b%22,%22receiver_zip%22:%22518033%22,%22receiver_mobile%22:%2218682100625%22,%22feedback%22:0,%22outer_tid%22:%22%22%7D%7D\",\"sendCount\":0,\"sign\":\"b33324530eeff9e857bd088a0155002a\",\"status\":\"WAIT_SELLER_SEND_GOODS\",\"test\":false,\"type\":\"TRADE\",\"version\":1471444850}
";
        
        
        $push = json_decode($a,true);
        $msg=json_decode(urldecode($push['msg']),true);

        dump($msg['trade']);

        $device_no='9982172973682014';
        $freeMessage = array(
            'memberCode' => MEMBER_CODE,
            'msgDetail' =>$msg['trade']['status'],
            'deviceNo' => $device_no,
            'msgNo' => time(),
        );

        $rs='发送状态:'.sendFreeMessage($freeMessage).'<br> 发送设备号:'.$device_no;


        $str = array('code' => 0, 'msg' => 'success');
        $this->ajaxReturn($str);


//        echo 'test';
    }

    private function genRandStr()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < 16; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    public function youzan_push()
    {
        $postStr = file_get_contents("php://input");

        $push = json_decode($postStr,true);
        $msg=json_decode(urldecode($push['msg']),true);

        \Think\Log::write($postStr, 'WARN');
        \Think\Log::write('status'.$msg['trade']['status'], 'WARN');
//        $str= {"code":0,"msg":"success"}

        $trade=$msg['trade'];

        if($msg['trade']['status']=='WAIT_SELLER_SEND_GOODS'){

            //推送小票信息
//            $device_no='9982172973682014';
            $device_no='4600182785048629';
            $freeMessage = array(
                'memberCode' => MEMBER_CODE,
                'msgDetail' =>'订单号'.$trade['num_iid'].$msg['trade']['status_str'],
                'deviceNo' => $device_no,
                'msgNo' =>time(),
            );
            sendFreeMessage($freeMessage);
        }


        $str = array('code' => 0, 'msg' => 'success');
        $this->ajaxReturn($str);
    }


    public function kdt()
    {
        $appId = '74a4bcc3b638a70415';
        $appSecret = '91b7dffe7314369b44f2a1cc79b39695';
        $client = new \KdtApiClient($appId, $appSecret);


        $method = 'kdt.shop.basic.get';
        $rs = $client->get($method, null);
        dump($rs);


    }
    public function kdt_user()
    {
        $appId = '2c61af3095a3518214';
        $appSecret = '0ee9bfdeced099f98ad0c714f365fc0f';

//        $appId = '74a4bcc3b638a70415';//yikebaocai
//        $appSecret = '91b7dffe7314369b44f2a1cc79b39695';//yikebaocai

        $client = new \KdtApiClient($appId, $appSecret);


        $method = 'kdt.users.weixin.follower.get';
        $params = [
            'weixin_openid' => 'ozolMuAnTRGa3UUqyJFEFiGEZOy0'

        ];


        $rs = $client->get($method, $params);
        echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
        dump($rs);


    }

    public function kdt_jf()
    {
        $appId = '2c61af3095a3518214';//baocaiwang
        $appSecret = '0ee9bfdeced099f98ad0c714f365fc0f';//boacaiwang
//        $appId = '74a4bcc3b638a70415';//yikebaocai
//        $appSecret = '91b7dffe7314369b44f2a1cc79b39695';//yikebaocai

        $client = new \KdtApiClient($appId, $appSecret);


        $method = 'kdt.crm.fans.points.get';
        $params = [
            'fans_id' => '1048863221'


        ];


        $rs = $client->get($method, $params);
        echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
        dump($rs);


    }

    public function kdt_auth(){
        $redirect_uri = 'http://baocai.vip.natapp.cn/man/?m=home&c=index&a=callback';
        redirect("https://open.koudaitong.com/oauth/authorize?client_id=62100d9ae0aa97dd6e&response_type=code&state=bc&redirect_uri=http://baocai.vip.natapp.cn/man/?m=home%26c=index%26a=callback", 2, '页面跳转中...');

    }

    public function elind(){
        $print = new \Yprint();
        $content = "测试测试";
        $apiKey = "85b279433a173b5a27cfd623fc4ab4c7295eb17f";
        $msign = 'sb86ib6hysee';
        $print->action_print(5533,'4004512863',$content,$apiKey,$msign);
    }




}