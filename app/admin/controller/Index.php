<?php
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {    	
        $this->assign("style", "index");
        $this->assign("ctrl", "index");
        $this->assign("base", LINK_BASE_KEY);
        return $this->fetch();
    }

    public function login() {
        return 'login';
    }
}
