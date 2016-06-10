<?php
namespace app\admin\event;
use think\Db;

class Account 
{
	public function getAccount($account, $pwd){    	
        return db("account")
        		->where('account', $account)
        		->where('password', md5($pwd))
        	    ->select();
    }

    public function hasUserId($id){     
        return (db("account")->where('id', $id)->count()) > 0;
    }

    public function isExist($account) {
    	return (db("account")->where('account', $account)->count()) > 0;
    }
}