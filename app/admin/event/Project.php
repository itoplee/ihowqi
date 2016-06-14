<?php
namespace app\admin\event;
use think\Db;

class Project 
{
	public function getAll($start, $count, $map)
    {    	
        return db("project")
            ->field('a.id,a.name,use_time,start_time,end_time,link,amount,state,is_muilty_link,w.name as type_name')
            ->alias('a')
            ->join('project_type w','type_id = w.id')
            ->where($map)
            ->order('insert_time DESC')
            ->limit($start * $count, $count)
            ->select();
    }

    public function getFinishAll($start, $count, $map)
    {    
        return db("project")
            ->field('a.id,a.name,use_time,start_time,end_time,link,amount,state,is_muilty_link,w.name as type_name')
            ->alias('a')
            ->join('project_type w','type_id = w.id')
            ->where($map)
            ->limit($start * $count, $count)
            ->select();
    }

    public function getTotal($map) 
    {
        return db("project")->alias('a')->where($map)->count();
    }

    public function getFinishTotal($map) {
        return db("project")->where($map)->count();
    }

    public function getById($id)
    {       
        return db("project")->where("id", $id)->select();
    }

    public function insert($arr)
    {
    	db("project")->insert($arr);
    }

    public function update($arr)
    {
    	db("project")->update($arr);
    }

    // 项目开始执行
    public function update2($id)
    {
        db("project")->where('id', $id)->where('state', 0)->setField('state', 1);
    }

    public function delete($id)
    {
    	db("project")->delete($id);
    }

    public function isStart($id) {
        return (db("project")->where('id', $id)->where('state', '>', 0)->count()) > 0;
    }

    public function isExist($name) {
    	return (db("project")->where('name', $name)->count()) > 0;
    }

    public function isExistId($id) {
        return (db("project")->where('id', $id)->count()) > 0;
    }
}