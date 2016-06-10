<?php
namespace app\admin\controller;

class Delete extends Http
{
	 // 项目删除
    public function deleteProject() {
    	$projectId = $_POST['id'];
    	$result = new HttpResult(null); 
    	$Event = \think\Loader::controller('ProjectCompany','event');
    	if($Event->hasProject($projectId)){
    		$result->code = 202;
    		$result->error = "项目已外包，不允许删除";
    	}else{
            $this->getEvent("ProjectLink")->deleteProject($projectId); 
            $this->getEvent("Project")->delete($projectId);
    	}
    	return $result;
    }

    // 删除项目类型
    public function deleteProjectType() {
        $id = $_POST['id'];
        $result = new HttpResult(null); 
        $Event = \think\Loader::controller('ProjectType','event');
        $Event->delete($id);
        return $result;
    }

    // 删除项目类型积分
    public function deleteProjectTypeScore() {
        $id = $_POST['id'];
        $result = new HttpResult(null); 
        $Event = \think\Loader::controller('ProjectTypeScore','event');
        $Event->delete($id);
        return $result;
    }

    // 删除外包公司
    public function deleteCompany() {
        $id = $_POST['id'];
        $result = new HttpResult(null); 
        $Event = \think\Loader::controller('Company','event');
        $Event->delete($id);
        return $result;
    }
}