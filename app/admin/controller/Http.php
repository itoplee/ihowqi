<?php
namespace app\admin\controller;

class Http
{

    protected  function loginCheck()
    {       
    	$userId = session(LOGIN_KEY_SESSION);
    	return !(is_int($userId) && $userId > 0);
    }

    protected function timeout() {
        $result = new HttpResult(null); 
        $result->code = 201;
        $result->error = "登录超时";
        return $result;
    } 

    protected function getEvent($name) {
        return \think\Loader::controller($name,'event');
    }

    protected function uuid() {
        return str_replace('-','',\think\Db::query("SELECT UUID() as uuid")[0]);
    }
}
