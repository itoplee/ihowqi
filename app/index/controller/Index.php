<?php
namespace app\index\controller;
use think\Controller;

class Index 
{
    public function index()
    {    
        return "index:";
    }

   public function hello() {
   	echo "hello";
   }
}
