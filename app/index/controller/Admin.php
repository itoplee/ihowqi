<?php
namespace app\index\controller;
use think\Db;

class Admin
{
    public function index()
    {    	
        return 'admin--->'.__DIR__.Db::name('province')->select();
    }

    public function index2()
    {    	
        return Db::name('province')->select();
    }
}
