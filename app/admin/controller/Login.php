<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Login extends Controller
{
    public function index()
    { 
    	$this->assign('css', CSS_PATH);
        $this->assign('img', IMG_PATH);
        $this->assign('js', JS_PATH);
        $this->assign('account', session('account'));
        $this->assign('error', session('error'));
    	return $this->fetch();
    }

    public function login($account, $pwd){
        if(empty($account)){
            session('error', '账号不能为空');
            return $this->redirect('index');
        }
        if(empty($pwd)){
            session('account', $account);
            session('error', '密码不能为空');
            return $this->redirect('index');
        }
    	$Event = \think\Loader::controller('Account','event');    
    	if($Event->isExist($account)){
    		$user = $Event->getAccount($account, $pwd);
    		if(count($user) === 1){
    			if((int)$user[0]['identity'] === 1){
    				if((int)$user[0]['state'] === 1){
    					session(null);
			    		session(LOGIN_KEY_SESSION, (int)$user[0]['id']);
			    		return $this->redirect('index/index');
    				}else{
    					session('account', $account);
			    		session('error', '账号已冻结，请联系管理员');
			    		return $this->redirect('index');
    				}
    			}else{
    				session('account', $account);
		    		session('error', '没有管理权限，请联系管理员');
		    		return $this->redirect('index');
    			}
    		}else{
    			session('account', $account);
	    		session('error', '账号或密码错误');
	    		return $this->redirect('index');
    		}
    	}else{
    		session('account', $account);
    		session('error', '账号不存在');
    		return $this->redirect('index');
    	}
    }

    public function logout() {
    	session(null);
    	return $this->redirect("index");
    }
}
