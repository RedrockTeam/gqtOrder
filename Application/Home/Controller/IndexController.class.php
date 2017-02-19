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
        header('Access-Control-Allow-Origin: *');
        if (empty($company)) {
            $this->ajaxReturn(
                array(
                    'status' => 400,
                    'info'   => '单位名称不能为空'
                )
            );
        }

        if ($phone == ''
            || strlen($phone) != 11
            || !is_numeric($phone)) {
            $this->ajaxReturn(
                array(
                    'status' => 400,
                    'info'   => '手机号码有误'
                )
            );
        }
        if ($name == '') {
            $this->ajaxReturn(
                array(
                    'status' => 400,
                    'info'   => '姓名有误'
                )
            );
        }
        if (!is_array($select)
            || count($select) > 3
            || count($select) == 0) {
            $this->ajaxReturn(
                array(
                    'status' => 400,
                    'info'   => '点单数量有误'
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
            'position'  => '',
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

}