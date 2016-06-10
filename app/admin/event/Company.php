<?php
namespace app\admin\event;
use think\Db;

class Company 
{
	public function getAll()
    {    	
        return db("company")->select();
    }

    public function search($name)
    {       
        return db("company") ->where('name','like', '%'.$name.'%')->select();
    }

    public function getById($id)
    {       
        return db("company")->where('id', $id)->select();
    }

    public function insert($name)
    {
    	db("company")->insert(['name' => $name]);
    }

    public function update($id, $name, $compete, $refuse, $full)
    {
    	db("company")->update([
            'name' => $name, 
            'link_compete' => $compete,
            'link_refuse' => $refuse,
            'link_full' => $full,
            'id' => $id
        ]);
    }

    public function delete($id)
    {
    	db("company")->where('id', $id)->delete();
    }

    public function isExist($name) {
    	return (db("company")->where('name', $name)->count()) > 0;
    }
}