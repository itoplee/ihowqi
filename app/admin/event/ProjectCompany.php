<?php
namespace app\admin\event;
use think\Db;

class ProjectCompany 
{
	public function getAll()
    {    	
        return db("project_company")->select();
    }

    public function getById($id)
    {    	
        return db("project_company")->where('id', $id)->find();
    }

    public function getByProjectId($projectId)
    {       
        return db("project_company")->where('project_id', $projectId)->select();
    }

    public function getByProjectIdAndCompanyId($projectId, $companyId)
    {       
        return db("project_company")
            ->where('project_id', $projectId)
            ->where('company_id', $companyId)
            ->find();
    }

    public function getOutAmount($projectId) 
    {
        $arr = db("project_company")
            ->field('SUM(amount) AS amount')
            ->where('project_id', $projectId)
            ->select();
        if(count($arr) > 0){
            return $arr[0]['amount'];
        }

        return 0;
    }

    public function insert($arr)
    {
    	db("project_company")->insert($arr);
    }

    public function update($arr)
    {
    	db("project_company")->update($arr);
    }

    public function delete($id)
    {
    	db("project_company")->delete($id);
    }

    public function isExist($projectId, $companyId) {
    	return (db("project_company")
    		    ->where('project_id', $projectId)
    		    ->where('company_id', $companyId)
    		    ->count()) > 0;
    }

    public function hasProject($projectId) {
        return (db("project_company")
                ->where('project_id', $projectId)
                ->count()) > 0;
    }
}