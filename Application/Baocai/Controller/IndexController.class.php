<?php
namespace Baocai\Controller;

use Think\Controller;

Vendor('kf5.Client');
Vendor('SensorsAnalytics.SensorsAnalytics');
Vendor('kf5.core.apiRequire');


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

        # 从 Sensors Analytics 配置页面中获取的数据接收的 URL
        $SA_SERVER_URL = 'http://yikebaocai.cloud.sensorsdata.cn:8006/sa?token=5719721cfbe1ddf7';

        # 初始化一个 Consumer，用于数据发送
        $consumer = new \BatchConsumer($SA_SERVER_URL);
        # 使用 Consumer 来构造 SensorsAnalytics 对象
        $sa = new \SensorsAnalytics($consumer);

        # 记录用户登录事件
        $distinct_id = 'ABCDEF123452324';

        $properties=['ShopName'=>'科兴旗舰店','Member'=>'2','Sex'=>'1'];
        $sa->track($distinct_id, 'UserLogin',$properties);
        $sa->track($distinct_id, 'ViewProduct');



        $sa->close();

        echo 'ok';


    }


}