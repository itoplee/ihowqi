<?php
namespace app\admin\event;
use think\Db;

class AccountProject 
{
	// 获取项目指定状态的记录数
	public function getStateCount($projectId, $state)
    {    	
        return db("account_project")
        	->where('project_id', $projectId)
        	->where('state', $state)
        	->count();
    }

    // 获取用户是否参与过项目
    public function getUserRecord($projectId, $userId) 
    {
    	return db("account_project")
        	->where('project_id', $projectId)
        	->where('user_id', $userId)
        	->find();
    }

    // 获取用户完成问卷的时间差
    public function getUseTimeDiff($projectId)
    {       
        return db("account_project")
            ->field('id, TIMESTAMPDIFF(MINUTE, start_time, finish_time) AS diff')
            ->where('project_id', $projectId)
            ->where('state', '>', 1)
            ->select();
    }

    // 用户完成问卷后更新数据
    public function updateUserRecore($id, $state, $score)
    {
    	db("account_project")->update([
    		'state' => $state,
    		'integral' => $score,
    		'finish_time' => date("Y-m-d h:i:s"),
    		'id' => $id
    	]);
    }

    // 用户完成问卷后更新数据
    public function updateStartTime($id)
    {
        db("account_project")->update([
            'start_time' => date("Y-m-d h:i:s"),
            'id' => $id
        ]);
    }

    // 用户点击问卷链接时，记录用户数据
    public function insert($arr)
    {
        db("account_project")->insert($arr);    
    }
}