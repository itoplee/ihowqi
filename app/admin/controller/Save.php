<?php
namespace app\admin\controller;

class Save extends Http
{
	 // 项目添加/修改
    public function addProject() {
    	$Event = \think\Loader::controller('Project','event'); 
    	$result = new HttpResult(null); 
        $projectId = $_POST['id'];
    	$data = [
    		'id' => $_POST['id'],
	        'name' => trim($_POST['name']),
	        'link' => trim($_POST['link']),
	        'start_time' => $_POST['start'],
	        'end_time' => $_POST['end'],
	        'update_time' => date("Y-m-d h:i:s"),
	        'type_id' => $_POST['typeId'],
	        'use_time' => $_POST['time'],
	        'link_compete' => trim($_POST['compete']),
	        'link_refuse' => trim($_POST['refuse']),
	        'link_full' => trim($_POST['full']),
            'amount' => $_POST['amount'],
            'is_muilty_link' => $_POST['isMuilty']
	    ];
        
    	if($_POST['pid'] > 0){
    		$Event->update($data);     
            if($this->getEvent("Project")->isStart($projectId) < 1) {
                $this->getEvent("ProjectLink")->deleteProject($projectId);     
            }
    	}else{
			if($Event->isExist($_POST['name'])){
                $result->setError(202, "项目名称已存在");
	    	}else{
	    		$data['state'] = 0;
	    		$data['insert_time'] = date("Y-m-d h:i:s");
	    		$data['user_id'] = session(LOGIN_KEY_SESSION);
	    		$Event->insert($data);
	    	}
    	}
        if($_POST["isMuilty"] > 0){
            if(Input('post.fileName')){
                $arr = $this->readFile(ROOT_PATH.'/public/upload/'.date("Y-m-d").'/'.$_POST['fileName']);
                $count = count($arr);
                if($count > 0) {
                    for($i=0; $i<$count; $i++) {
                        $this->getEvent("ProjectLink")->insert($projectId, trim($arr[$i]));
                    }
                }
            }
        }

    	return $result;    	
    }

    // 添加/更新项目外包信息
    public function addProjectCompany() {
    	$Event = \think\Loader::controller('ProjectCompany','event');
    	$result = new HttpResult(null); 
    	$data = [
            'project_id' => $_POST['projectId'],
            'company_id' => $_POST['companyId'],
            'link_compete' => trim($_POST['compete']),
            'link_refuse' => trim($_POST['refuse']),
            'link_full' => trim($_POST['full']),
            'amount' => $_POST['amount']
        ];
    	if($Event->isExist($_POST['projectId'], $_POST['companyId'])){
            $data['id'] = $_POST['id'];
            $Event->update($data);
    	}else{
            $data['id'] = $this->uuid()['uuid'];
            $data['state'] = 0;
    		$Event->insert($data);
            \think\Loader::controller('Project','event')->update2($_POST['projectId']);
    	}
    	return $result;
    }


    // 项目挂起/恢复
    public function projectHang() {
        $Event = \think\Loader::controller('Project','event');
        $Event->update([
            'id' => $_POST['id'], 
            'state' => $_POST['flag'],
            'update_time' => date("Y-m-d h:i:s")
        ]);
        $result = new HttpResult(null); 
        return $result;
    }

    // 添加项目类型
    public function addProjectType() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('ProjectType','event');
        $result = new HttpResult(null);
        $name = trim($_POST['name']);
        if($Event->isExist($name)){
            $result->setError(202, "项目类型已存在");
        }else{
            $Event->insert($name);
        }

        return $result;
    }

    // 更新项目类型
    public function updateProjectType() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $id = $_POST['id'];
        $Event = \think\Loader::controller('ProjectType','event');
        $Event->updateName($id, trim($_POST['name']));
        $result = new HttpResult($Event->getById($id));
        return $result;
    }

    // 添加项目类型积分
    public function addProjectTypeScore() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('ProjectTypeScore','event');
        $result = new HttpResult(null);
        $Event->insert($_POST['typeId'], $_POST['min'], $_POST['max'], $_POST['score']);

        return $result;
    }

    // 修改项目类型积分
    public function updateProjectTypeScore() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('ProjectTypeScore','event');
        $result = new HttpResult(null);
        $Event->update($_POST['id'], $_POST['typeId'], $_POST['min'], $_POST['max'], $_POST['score']);

        return $result;
    }

    // 添加外包公司
    public function addCompany() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('Company','event');
        $result = new HttpResult(null);
        $name = trim($_POST['name']);
        if($Event->isExist($name)){
            $result->setError(202, "公司名称已存在");
        }else{
            $Event->insert($name);
        }

        return $result;
    }

    // 更新外包公司信息
    public function updateCompany() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('Company','event');
        $Event->update($_POST['id'], trim($_POST['name']), trim($_POST['compete']), trim($_POST['refuse']), trim($_POST['full']));
        return new HttpResult(null);
    }

    private function readFile($file_name) {
        $fp=fopen($file_name,'r');
        $index = 0;
        while(!feof($fp)){
            $arr[$index++] = fgets($fp,4096);
        }
        fclose($fp);
        return $arr;
    }
}