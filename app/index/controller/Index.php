<?php
namespace app\index\controller;
use think\Controller;
use think\Input;

class Index extends Controller
{
    public function index()
    {    
    	$this->assign('css', CSS_PATH);
    	$this->assign('js', JS_PATH);
        return $this->fetch();
    }

    // QQ登录
    public function qqlogin() {
    	if(Input('get.code')){
            // 获取access_token
    		$uri = "https://graph.qq.com/oauth2.0/token";
    		$uri = $uri."?grant_type=authorization_code";
    		$uri = $uri."&client_id=101327498";
    		$uri = $uri."&client_secret=befaa63c1dc47b9bdb692cc93be2d3ec";
    		$uri = $uri."&code=".$_GET['code'];
    		$uri = $uri."&redirect_uri=".urlencode('http://www.itoplee.com/ihowqi/index.php/index/index/qqlogin');  

            // 获取openid
    		$data = file_get_contents($uri);
            $pos = strpos($data, "&expires_in");
            $token = substr($data, 0, $pos);
            $token = str_replace("access_token=", "", $token);
            $token = str_replace("&", "", $token);
            $data2 = file_get_contents("https://graph.qq.com/oauth2.0/me?access_token=".$token);
    	
            // 获取用户信息
            $data2 = str_replace("callback", "", $data2);
            $data2 = str_replace("(", "", $data2);
            $data2 = str_replace(")", "", $data2);
            $data2 = str_replace(";", "", $data2);
            $arr = json_decode($data2, true);    		
            $data3 = file_get_contents("https://graph.qq.com/user/get_user_info?access_token=".$token."&oauth_consumer_key=".$arr["client_id"]."&openid=".$arr["openid"]);
    	}

    	$this->redirect("index");
    }

    public function baiduLogin() {
    	if(Input('get.code')){
            // 获取access_token
    		$uri = "https://openapi.baidu.com/oauth/2.0/token?";
            $uri = $uri."grant_type=authorization_code";
            $uri = $uri."&code=".$_GET['code'];
            $uri = $uri."&client_id=e1gGLs0wZKDjqRysUHnzAGMt";
            $uri = $uri."&client_secret=QcpAWb0QprGpWwbHeNxzKubsk1il6fK9";
            $uri = $uri."&redirect_uri=".urlencode('http://www.itoplee.com/ihowqi/index.php/index/index/baiduLogin');
            $data = file_get_contents($uri);
            $arr = json_decode($data, true);          
            // 获取用户信息
            $data2 = file_get_contents("https://openapi.baidu.com/rest/2.0/passport/users/getInfo?access_token=".$arr['access_token']);
            print_r($data2);
    	}

    	return "<br>baidu";
    }
}
