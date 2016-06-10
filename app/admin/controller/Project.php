<?php
namespace app\admin\controller;
use think\Controller;

class Project extends Controller
{
    // 问卷完成后的回调
    public function project($s, $p, $u) {
        $s = $this->setProjectState($p, $s);
        if(strlen((string)$u) > 11){
            $Event = \think\Loader::controller("ProjectCompanyExecute",'event');
            $Event2 = \think\Loader::controller("ProjectCompany",'event');
            $out = $Event->getUserRecord($p, $u);
            if($out){
                $record = $Event2->getByProjectIdAndCompanyId($out['project_id'], $out['company_id']);
                if($record){
                    if((int)$s === 2){
                        $count = $Event->getCompanyStateCount($out['project_id'], $out['company_id'], 2);
                        if((int)$count  >= (int)$record['amount']){
                            $s = 5;
                        }
                    }
                }
                $Event->updateUserRecore($out['id'], $s);                
                if($record){
                    $this->redirect($this->getLinkOut($record, $out['company_user_id'], $s));
                }                
            }
        }else{
            $Event = \think\Loader::controller("AccountProject",'event');
            $inner = $Event->getUserRecord($p, $u);            

            if((int)$s === 2) {
                $outCount = \think\Loader::controller("ProjectCompany",'event')->getOutAmount($p);
                $innerCount = \think\Loader::controller("AccountProject",'event')->getStateCount($p, 2);
                $arr = \think\Loader::controller("Project",'event')->getById($p);
                if(count($arr) > 0){
                    if((int)$innerCount >= ((int)$arr[0]['amount'] - (int)$outCount)){
                        $s = 5;
                    }
                }
            }
            
            if($inner){
                $Event->updateUserRecore($inner['id'], $s, $this->getScore($s));
                $this->assign('img', IMG_PATH);
                return $this->fetch($s);
            }
        }
    }

    // 第三方会员点击问卷的回调
    public function outlink($k, $u) {   
        $Event = \think\Loader::controller("ProjectCompanyExecute",'event');
        $Event2 = \think\Loader::controller("ProjectCompany",'event');
        $record = $Event2->getById($k);
        if($record){
            if($this->isProjectClose($record['project_id'])){
                $this->assign('img', IMG_PATH);
                return $this->fetch("close");
            }else{
                $exec = $Event->getUserRecordOut($record['project_id'], $record['company_id'], $u);
                if($exec){
                    if((int)$exec['state'] > 1){
                        $this->assign('img', IMG_PATH);
                        return $this->fetch("duplicate");
                    }else{
                        $link = $this->getLink($record['project_id']);
                        if($link){
                            $Event->updateStartTime($exec['id']);
                            if(strpos($link, "?")){
                                $link = $link."&".PARAM_KEY."=".$exec['uuid'];
                            }else{
                                $link = $link."?".PARAM_KEY."=".$exec['uuid'];
                            }
                            $this->redirect($link);
                        }else{
                            $this->assign('img', IMG_PATH);
                            return $this->fetch("maintain");
                        }    
                    }
                    
                }else{
                    $link = $this->getLink($record['project_id']);
                    if($link){
                        $uuid = str_replace('-','',\think\Db::query("SELECT UUID() as uuid")[0]);
                        $Event->insert([
                            'uuid' => $uuid['uuid'],
                            'company_user_id' => $u,
                            'project_id' => $record['project_id'],
                            'company_id' => $record['company_id'],
                            'start_time' => date("Y-m-d h:i:s"),
                            'finish_time' => date("Y-m-d h:i:s"),
                            'state' => 1
                        ]);
                        if(strpos($link, "?")){
                            $link = $link."&".PARAM_KEY."=".$uuid['uuid'];
                        }else{
                            $link = $link."?".PARAM_KEY."=".$uuid['uuid'];
                        }
                        $this->redirect($link);
                    }else{
                        $this->assign('img', IMG_PATH);
                        return $this->fetch("maintain");
                    }                
                }
            }
        }else{
            $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
            $this->assign('img', IMG_PATH);
            return $this->fetch("invalid");
        }
    }

    // 内部会员点击问卷的回调
    public function innerlink($p, $u) {  
        if(\think\Loader::controller("Account",'event')->hasUserId($u)) {
            if(\think\Loader::controller("Project",'event')->isExistId($p)) {
                if($this->isProjectClose($p)){
                    $this->assign('img', IMG_PATH);
                    return $this->fetch("close");
                }else{
                    $Event = \think\Loader::controller("AccountProject",'event');
                    $record = $Event->getUserRecord($p, $u);
                    if($record){
                        if((int)$record['state'] > 1){
                            $this->assign('img', IMG_PATH);
                            return $this->fetch("duplicate");
                        }else{
                            $link = $this->getLink($p);
                            if($link){
                                $Event->updateStartTime($record['id']);
                                if(strpos($link, "?")){
                                    $link = $link."&".PARAM_KEY."=".$u;
                                }else{
                                    $link = $link."?".PARAM_KEY."=".$u;
                                }
                                $this->redirect($link);
                            }else{
                                $this->assign('img', IMG_PATH);
                                return $this->fetch("maintain");
                            }     
                        }
                        
                    }else{
                        $link = $this->getLink($p);
                        if($link){
                            $Event->insert([
                                'user_id' => $u,
                                'project_id' => $p,
                                'start_time' => date("Y-m-d h:i:s"),
                                'finish_time' => date("Y-m-d h:i:s"),
                                'state' => 1,
                                'integral' => 0
                            ]);
                            if(strpos($link, "?")){
                                $link = $link."&".PARAM_KEY."=".$u;
                            }else{
                                $link = $link."?".PARAM_KEY."=".$u;
                            }
                            $this->redirect($link);
                        }else{
                            $this->assign('img', IMG_PATH);
                            return $this->fetch("maintain");
                        }     
                    }
                }
            }else{
                $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
                $this->assign('img', IMG_PATH);
                return $this->fetch("invalid");
            }            
        }else{
            $this->assign('msg', '非内部会员，不能参与调查。');
            $this->assign('img', IMG_PATH);
            return $this->fetch("invalid");
        }
        
    }

    // 更新项目状态
    private function setProjectState($projectId, $s) {
        if((int)$s === 2){
            $Event = \think\Loader::controller("Project",'event');
            $arr = $Event->getById($projectId);
            if(count($arr) > 0){
                if($arr[0]['state'] > 1){
                    $s = 5;
                }else{
                    $count1 = \think\Loader::controller("AccountProject",'event')->getStateCount($projectId, 2);
                    $count2 = \think\Loader::controller("ProjectCompanyExecute",'event')->getStateCount($projectId, 2);
                    if(($count1 + $count2) >= $arr[0]['amount']){
                        $Event->update(['id' => $projectId, 'state' => 3]);
                        $s = 5;
                    }
                }                
            }
        }
        return $s;
    }

    // 判断项目是否已结束
    private function isProjectClose($projectId) {
        $Event = \think\Loader::controller("Project",'event');
        $arr = $Event->getById($projectId);
        if(count($arr) > 0){
            return $arr[0]['state'] > 1;
        }

        return true;
    }

    // 获取问卷地址
    private function getLink($projectId) {
        $link = null;
        $Event = \think\Loader::controller("Project",'event');
        $arr = $Event->getById($projectId);
        if(count($arr) > 0){
            $project = $arr[0];
            if((int)$project['is_muilty_link'] > 0){
                $Event2 = \think\Loader::controller("ProjectLink",'event');
                $brr = $Event2->getEnableLink($projectId);
                if(count($brr) > 0){
                    $link = $brr[0]['link'];
                    $Event2->update($brr[0]['id']);
                }
            }else{
                $link = $project['link'];
            }
        }
        return trim($link);
    }

    // 获取第三方的回调地址
    private function getLinkOut($record, $uid, $s) {
        switch ((int)$s) {
            case 2:
                $link = $record['link_compete'].$uid;
                break; 
            case 3:
                $link = $record['link_refuse'].$uid;
                break; 
            case 4:
                $link = $record['link_full'].$uid;
                break;            
            default:
                $link = $record['link_full'].$uid;
                break;
        }
        return trim($link);
    }

    private function getScore($s) {
        $score = 10;
        if((int)$s === 2){
            // TODO
        }
        return $score;
    }
}
