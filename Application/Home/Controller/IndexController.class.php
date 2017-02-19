<?php
namespace Home\Controller;

use Org\Util\String;
use Think\Controller;

class IndexController extends Controller {
    private $appid = 'wx81a4a4b77ec98ff4';
    private $acess_token = 'gh_68f0a1ffc303';

    public function index() {
        $signature = $this->JSSDKSignature();
        $this->assign('signature', $signature);
        $this->assign('appid', $this->appid);
        $this->display();
    }
    public function clickPage() {
        $signature = $this->JSSDKSignature();
        $this->assign('signature', $signature);
        $this->assign('appid', $this->appid);
        $this->display();
    }
    public function more() {
        $signature = $this->JSSDKSignature();
        $this->assign('signature', $signature);
        $this->assign('appid', $this->appid);
        $this->display();
    }

    public function order() {
        $input = I('post.');
        $company = $input['company'];
        $select = $input['select'];
        $name = $input['name'];
        $phone = $input['phone'];
        $position = $input['position'];
        header('Access-Control-Allow-Origin: *');
        if (empty($company)
            || $phone == ''
            || strlen($phone) != 11
            || !is_numeric($phone)
            || $position == ''
            || $name == ''
            || !is_array($select)
            || count($select) > 3
            || count($select) == 0) {
            $this->ajaxReturn(
                array(
                    'status' => 400,
                    'info'   => '参数错误'
                )
            );
        }
        $table = M('records');
        $count = $table->where(array('company' => $company))->count();
        $data = array(
            'company' => $company,
            'select'  => json_encode($select),
            'phone'  => $phone,
            'name'  => $name,
            'position'  => $position,
            'datetime' => date('Y-m-d H:i:s', time())
        );
        if ($count != 0) {
            $result = $table->where(array('company' => $company))->save($data);
        } else {
            $result = $table->add($data);
        }

        if (!$result) {
            $this->ajaxReturn(
                array(
                    'status' => 500,
                    'info'   => '服务器开小差了'
                )
            );
        }
        $this->ajaxReturn(
            array(
                'status' => 200,
                'info'   => '提交成功'
            )
        );

    }

    public function orderList() {
        $current = I('post.current');
        $current = $current >= 0 ? $current: 0;
        $table = M('records');
        $count = $table->count();
        $current = $current == 0 ? $count: $count - $current;
        $data = array();
        if ($current != 0) {
            $data = $table->order('id desc')->limit($current)->field('company, select, datetime')->select();
            foreach ($data as &$v) {
                $v['select'] = json_decode($v['select']);
            }
        }
        header('Access-Control-Allow-Origin: *');
        $this->ajaxReturn(
            array(
                'status' => 200,
                'data'   => $data
            )
        );
    }

    public function JSSDKSignature(){
        $string = new String();
        $jsapi_ticket =  $this->getTicket();
        $data['jsapi_ticket'] = $jsapi_ticket['data'];
        $data['noncestr'] = $string->randString();
        $data['timestamp'] = time();
        $data['url'] = 'http://'.$_SERVER['HTTP_HOST'].__SELF__;//生成当前页面url
        $data['signature'] = sha1($this->ToUrlParams($data));
        return $data;
    }
    private function ToUrlParams($urlObj){
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if($k != "signature") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }


    /*curl通用函数*/
    private function curl_api($url, $data=''){
        // 初始化一个curl对象
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        // 运行curl，获取网页。
        $contents = json_decode(curl_exec($ch), true);
        // 关闭请求
        curl_close($ch);
        return $contents;
    }

    private function getTicket() {
        $time = time();
        $str = 'abcdefghijklnmopqrstwvuxyz1234567890ABCDEFGHIJKLNMOPQRSTWVUXYZ';
        $string='';
        for($i=0;$i<16;$i++){
            $num = mt_rand(0,61);
            $string .= $str[$num];
        }
        $secret =sha1(sha1($time).md5($string)."redrock");
        $t2 = array(
            'timestamp'=>$time,
            'string'=>$string,
            'secret'=>$secret,
            'token'=>$this->acess_token,
        );
        $url = "http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/Api/Api/apiJsTicket";
        return $this->curl_api($url, $t2);
    }
    /*
      wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: 'wx81a4a4b77ec98ff4', // 必填，公众号的唯一标识
        timestamp: "{$signature['timestamp']}", // 必填，生成签名的时间戳
        nonceStr: "{$signature['noncestr']}", // 必填，生成签名的随机串
        signature: "{$signature['signature']}",// 必填，签名，见附录1
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'hideAllNonBaseMenuItem'
        ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function(){
        wx.onMenuShareTimeline({
            title: '【@重邮缘】“11.11” 来了，听说你还单着呢！没关系，这里有......', // 分享标题
            link: "http://mp.weixin.qq.com/s?__biz=MjM5NDAzNDM2MQ==&mid=401028673&idx=1&sn=be047c29e501192df9432b224ff59044#rd",
            imgUrl: "http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg",
            success: function () {
                alert('分享成功!');// 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareAppMessage({
            title: '【@重邮缘】“11.11” 来了，听说你还单着呢！没关系，这里有......', // 分享标题
            desc: '【@重邮缘】“11.11” 来了，听说你还单着呢！没关系，这里有......', // 分享描述
            link: "http://mp.weixin.qq.com/s?__biz=MjM5NDAzNDM2MQ==&mid=401028673&idx=1&sn=be047c29e501192df9432b224ff59044#rd",
            imgUrl: 'http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                alert('分享成功!');// 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareQQ({
            title: '【@重邮缘】“11.11” 来了，听说你还单着呢！没关系，这里有......', // 分享标题
            desc: '【@重邮缘】“11.11” 来了，听说你还单着呢！没关系，这里有......', // 分享描述
            link: "http://mp.weixin.qq.com/s?__biz=MjM5NDAzNDM2MQ==&mid=401028673&idx=1&sn=be047c29e501192df9432b224ff59044#rd",
            imgUrl: 'http://hongyan.cqupt.edu.cn/cquptluck/Public/images/index/share.jpg', // 分享图标
            success: function () {
                alert('分享成功!');// 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
     */
}