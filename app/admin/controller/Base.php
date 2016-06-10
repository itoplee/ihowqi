<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
    public function _initialize()
    {
    	$this->assign('css', CSS_PATH);
        $this->assign('img', IMG_PATH);
        $this->assign('js', JS_PATH);
    	$userId = session(LOGIN_KEY_SESSION);
    	if(!(is_int($userId) && $userId > 0)){
    		echo "string";
    		$this->redirect("login/index");
    	}
    }
}
