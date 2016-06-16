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

    public function wxlogin() {
    	if(Input('get.code')){
    		echo "code:".$_GET['code'].'<br/>';
    	}

    	if(Input('get.state')){
    		echo "state:".$_GET['state'].'<br/>';
    	}
    	return "wxlogin";
    }

    public function qqlogin() {
    	$uri = "";
    	if(Input('get.code')){
    		$uri = "https://graph.qq.com/oauth2.0/token";
    		$uri = $uri."?grant_type=authorization_code";
    		$uri = $uri."&client_id=101327498";
    		$uri = $uri."&client_secret=befaa63c1dc47b9bdb692cc93be2d3ec";
    		$uri = $uri."&code=".$_GET['code'];
    		$uri = $uri."&redirect_uri=".urlencode('http://www.itoplee.com/ihowqi/index.php/index/index/qqlogin');  

    		$data = file_get_contents($uri);
    		print_r($data);
    		echo "string<br>";
    	}
    	return $uri;
    }
}
