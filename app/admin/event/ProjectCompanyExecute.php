<?php
namespace app\admin\event;
use think\Db;

class ProjectCompanyExecute 
{
    // 获取项目指定状态的记录数
    public function getStateCount($projectId, $state)
    {       
        return db("project_company_execute")
            ->where('project_id', $projectId)
            ->where('state', $state)
            ->count();
    }

    // 获取项目指定状态的记录数
    public function getCompanyStateCount($projectId, $companyId, $state)
    {       
        return db("project_company_execute")
            ->where('project_id', $projectId)
            ->where('company_id', $companyId)
            ->where('state', $state)
            ->count();
    }

    // 获取外部用户是否参与过项目
    public function getUserRecordOut($projectId, $companyId, $userId) 
    {
        return db("project_company_execute")
            ->where('project_id', $projectId)
            ->where('company_id', $companyId)
            ->where('company_user_id', $userId)
            ->find();
    }

    // 获取用户是否参与过项目
    // $uuid 临时用户id
    public function getUserRecord($projectId, $uuid) 
    {
        return db("project_company_execute")
            ->where('project_id', $projectId)
            ->where('uuid', $uuid)
            ->find();
    }

    // 获取用户完成问卷的时间差
    public function getUseTimeDiff($projectId, $companyId)
    {       
        return db("project_company_execute")
            ->field('id, TIMESTAMPDIFF(MINUTE, start_time, finish_time) AS diff')
            ->where('project_id', $projectId)
            ->where('company_id', $companyId)
            ->where('state', '>', 1)
            ->select();
    }

    // 用户完成问卷后更新数据
    public function updateUserRecore($id, $state)
    {
        db("project_company_execute")->update([
            'state' => $state,
            'finish_time' => date("Y-m-d h:i:s"),
            'id' => $id
        ]);
    }

    // 用户完成问卷后更新数据
    public function updateStartTime($id)
    {
        db("project_company_execute")->update([
            'start_time' => date("Y-m-d h:i:s"),
            'id' => $id
        ]);
    }

    // 外部用户点击问卷链接时，保持数据
    public function insert($arr)
    {
    	db("project_company_execute")->insert($arr);   	
    }
}