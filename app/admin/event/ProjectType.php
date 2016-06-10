<?php
namespace app\admin\event;
use think\Db;

class ProjectType 
{
	public function getAll()
    {    	
        return db("project_type")->select();
    }

    public function getById($id)
    {       
        return db("project_type")->where('id', $id)->select();
    }

    public function search($name)
    {       
        return db("project_type")->where("name", "like", "%".$name."%")->select();
    }

    public function insert($name)
    {
    	db("project_type")->insert(['name' => $name]);
    }

    public function updateName($id, $name)
    {
    	db("project_type")->update(['name' => $name, 'id' => $id]);
    }

    public function delete($id)
    {
    	db("project_type")->delete($id);
    }

    public function isExist($name) {
    	return (db("project_type")->where('name', $name)->count()) > 0;
    }
}