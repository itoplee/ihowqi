<?php
namespace app\admin\controller;
use \think\Input;
use think\Loader;
class Get extends Http
{
    // 获取项目整体数据
    public function getProjectsChart() 
    {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = $this->getEvent("Project");
        $data['total'] = $Event->getFinishTotal([]);
        $data['unstart'] = $Event->getFinishTotal(['state' => 0]);
        $data['start'] = $Event->getFinishTotal(['state' => 1]);
        $data['stop'] = $Event->getFinishTotal(['state' => 2]);
        $data['compete'] = $Event->getFinishTotal(['state' => 3]);
        $data['audit'] = $Event->getFinishTotal(['state' => 4]);
        $data['finish'] = $Event->getFinishTotal(['state' => 5]);
        return new HttpResult($data);
    }

    // 获取项目列表
    public function getProjects()
    {    	
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $map['a.state'] = ['<', 5];
        if(Input('post.state')){
            $map['a.state'] = $_POST['state'] - 1;
        }
        if(Input('post.name')){
            $map['a.name'] = ['like', '%'.$_POST['name'].'%'];
        }
    	$Event = \think\Loader::controller('Project','event'); 
    	$data['list'] = $Event->getAll($_POST['page'], $_POST['rowCount'], $map);
    	$data['rowCount'] = $Event->getTotal($map);
        $count = count($data['list']);
        if($count > 0){
            $Event2 = \think\Loader::controller('AccountProject','event');
            $Event3 = \think\Loader::controller('ProjectCompanyExecute','event');
            for($i=0; $i<$count; $i++){
                $id = $data['list'][$i]['id'];
                $arr[$i] = $data['list'][$i];
                $arr[$i]['partInCount'] = $Event2->getPartInCount($id) + $Event3->getPartInCount($id);
                $arr[$i]['dropCount'] = $Event2->getStateCount($id, 1) + $Event3->getStateCount($id, 1);
                $arr[$i]['competeCount'] = $Event2->getStateCount($id, 2) + $Event3->getStateCount($id, 2);
                $arr[$i]['refuseCount'] = $Event2->getStateCount($id, 3) + $Event3->getStateCount($id, 3);
                $crr = $Event2->getUseTimeDiff($id);
                $drr = $Event3->getUseTimeDiff2($id);
                $frr = $this->getUseTimeDiff($crr, $drr);
                $arr[$i]['max'] = $frr['max'];
                $arr[$i]['min'] = $frr['min'];
                $arr[$i]['avg'] = $frr['avg'];
                $arr[$i]['loi'] = $frr['loi'];
            }
            $data['list'] = $arr;
        }
        return new HttpResult($data);
    }

    // 获取项目详情
    public function getProjectInfo()
    {    	
    	if($this->loginCheck()) {
            return $this->timeout();
        }

    	$Event = \think\Loader::controller('Project','event'); 
        return new HttpResult($Event->getById($_POST['id'])); 
    }



    // 获取项目外包详情
    public function getProjectCompany() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        return new HttpResult($this->getEvent('ProjectCompany')->getByProjectIdAndCompanyId($_POST['projectId'], $_POST['companyId']));
    }

    // 获取项目进度
    public function getProjectProcess() {
        $id = $_POST['id'];
        $project = $this->getEvent('Project')->getById($id)[0];
        $companys = $this->getEvent('ProjectCompany')->getByProjectId($id);
        $count = count($companys);
        $index = 0;
        $companyCount = 0;    
        if($count > 0){                    
            foreach($companys as $company) {
                $companyCount += $company['amount'];
                $arr[$index]['isOut'] = true;
                $arr[$index]['id'] = $company['id'];
                $arr[$index]['name'] = $this->getEvent('Company')->getById($company['company_id'])[0]['name'];
                $arr[$index]['amount'] = $company['amount'];
                $arr[$index]['unfinish'] = $this->getEvent('ProjectCompanyExecute')->getCompanyStateCount($id, $company['company_id'], 1);
                $arr[$index]['compete'] = $this->getEvent('ProjectCompanyExecute')->getCompanyStateCount($id, $company['company_id'], 2);
                $arr[$index]['refuse'] = $this->getEvent('ProjectCompanyExecute')->getCompanyStateCount($id, $company['company_id'], 3);
                $arr[$index]['full'] = $this->getEvent('ProjectCompanyExecute')->getCompanyStateCount($id, $company['company_id'], 4) +
                                        $this->getEvent('ProjectCompanyExecute')->getCompanyStateCount($id, $company['company_id'], 5);
                $arr[$index]['partIn'] = $arr[$index]['unfinish'] + $arr[$index]['compete'] + $arr[$index]['refuse'] + $arr[$index]['full'];
                $diffArr = $this->getUseTimeDiff($this->getEvent('ProjectCompanyExecute')->getUseTimeDiff($id, $company['company_id']), null);
                $arr[$index]['min'] = $diffArr['min'];
                $arr[$index]['max'] = $diffArr['max'];
                $arr[$index]['avg'] = $diffArr['avg'];                
                $arr[$index]['sum'] = $diffArr['sum'];
                $arr[$index]['total'] = $diffArr['total'];
                $index++;
            }
        }

        $arr[$index]['isOut'] = false;
        $arr[$index]['id'] = "000";
        $arr[$index]['name'] = '内部会员';
        $arr[$index]['amount'] = $project['amount'] - $companyCount;
        $arr[$index]['unfinish'] = $this->getEvent('AccountProject')->getStateCount($id, 1);
        $arr[$index]['compete'] = $this->getEvent('AccountProject')->getStateCount($id, 2);
        $arr[$index]['refuse'] = $this->getEvent('AccountProject')->getStateCount($id, 3);
        $arr[$index]['full'] = $this->getEvent('AccountProject')->getStateCount($id, 4) + 
                                $this->getEvent('AccountProject')->getStateCount($id, 5);
        $arr[$index]['partIn'] = $arr[$index]['unfinish'] + $arr[$index]['compete'] + $arr[$index]['refuse'] + $arr[$index]['full'];
        $diffArr = $this->getUseTimeDiff($this->getEvent('AccountProject')->getUseTimeDiff($id), null);
        $arr[$index]['min'] = $diffArr['min'];
        $arr[$index]['max'] = $diffArr['max'];
        $arr[$index]['avg'] = $diffArr['avg'];
        $arr[$index]['sum'] = $diffArr['sum'];
        $arr[$index]['total'] = $diffArr['total'];
        return new HttpResult([
            'project' => $project,
            'list' => $arr,
            'linkCount' => $this->getEvent("ProjectLink")->getEnableLinkCount($id)
        ]);
    }

    // 获取已归档项目
    public function getFinishedProjects() {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $map['state'] = 5;
        if(Input('post.name')){
            $map['name'] = ['like', '%'.$_POST['name'].'%'];
        }
        $Event = \think\Loader::controller('Project','event'); 
        $data['list'] = $Event->getFinishAll($_POST['page'], $_POST['rowCount'], $map);
        $data['rowCount'] = $Event->getFinishTotal($map);
        return new HttpResult($data);
    }

    // 获取项目类型
    public function getProjectTypes()
    {       
        if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('ProjectType','event'); 
        if(Input('post.name')){
            return new HttpResult($Event->search($_POST['name']));
        }
        return new HttpResult($Event->getAll()); 
    }

    // 获取项目类型积分
    public function getProjectTypeScore() {
         if($this->loginCheck()) {
            return $this->timeout();
        }

        $Event = \think\Loader::controller('ProjectTypeScore','event'); 
        return new HttpResult($Event->getTypeScores($_POST['typeId']));
    }

    // 获取外包公司
    public function getCompanys()
    {    	
    	if($this->loginCheck()) {
            return $this->timeout();
        }

    	$Event = \think\Loader::controller('Company','event'); 
        if(Input('post.name')){
            return new HttpResult($Event->search($_POST['name']));
        }
        return new HttpResult($Event->getAll()); 
    }

    // 获取uuid
    public function getUUID() 
    {
        if($this->loginCheck()) {
            return $this->timeout();
        }

        return new HttpResult(['uuid' => $this->uuid()['uuid']]); 
    }

    private function getUseTimeDiff($arr, $brr) 
    {
        $data = ['min' => 0, 'max' => 0, 'avg' => 0, 'sum' => 0, 'total' => 0, 'loi' => 0];
        $count = count($arr);
        $min = 0;
        $max = 0;
        $sum = 0;
        $lcount = 0;
        $lsum = 0;
        if($count > 0){
            for($i=0; $i<$count; $i++){
                $num = $arr[$i]['diff'];
                $sum += $num;
                if($num < $min){
                    $min = $num;
                }else if($num > $max){
                    $max = $num;
                }

                if($arr[$i]['state'] === 2){
                    $lcount = $lcount + 1;
                    $lsum = $lsum + $num;
                }
            }
            
        }

        if($brr){
            $count2 = count($brr);
            for($i=0; $i<$count2; $i++){
                $num = $brr[$i]['diff'];
                if($num < $min){
                    $min = $num;
                }else if($num > $max){
                    $max = $num;
                }

                if($brr[$i]['state'] === 2){
                    $lcount = $lcount + 1;
                    $lsum = $lsum + $num;
                }
            }
        }

        $data['min'] = $min;
        $data['max'] = $max;
        $data['avg'] = $count > 0 ? round($sum / $count, 2) : 0;
        $data['sum'] = $sum;
        $data['total'] = $count;
        $data['loi'] = 0;
        if($lcount > 0){
            $data['loi'] = round($lsum / $lcount, 2);
        }

        return $data;
    }
}