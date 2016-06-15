<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {    
    	$this->assign('css', CSS_PATH);
    	$this->assign('js', JS_PATH);
        return $this->fetch();
    }

}
