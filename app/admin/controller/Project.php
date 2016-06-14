<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;

class Project extends Controller
{
    // 问卷完成后的回调
    public function project($s, $p, $u) {
        $P = Loader::Model('Project');
        // 判断项目是否合法
        if($P->where('id', $p)->count() < 1) {
            $this->assign('img', IMG_PATH);
            $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
            return $this->fetch("invalid");
        }

        if(strlen((string)$u) > 11) {
            $PME = Loader::Model("ProjectCompanyExecute")->where(['project_id' => $p, 'uuid' => $u])->find();
            if($PME) {
                // 记录数据
                $link = $this->getOutLink($s, $p, $u, $PME, $P->where('id', $p)->find());
                $this->redirect($link);
            }else{
                // 非法用户
                $this->assign('img', IMG_PATH);
                $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
                return $this->fetch("invalid");
            }
        }else{
            $this->assign('img', IMG_PATH);
            // 判断用户是否合法
            if(Loader::Model('Account')->where('id', $u)->count() < 1) {
                $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
                return $this->fetch("invalid");
            }
            // 记录数据
            $view = $this->getInnerView($s, $p, $u, $P->where('id', $p)->find());
            return $this->fetch($view);
        }
    }

    // 第三方会员点击问卷的回调
    public function outlink($k, $u) {   
        // 判断项目是否合法
        $pm = Loader::Model('ProjectCompany')->where('id', $k)->find();
        if($pm && (Loader::Model('Project')->where('id', $pm->project_id)->count() > 0)) {
            $uuid = str_replace('-','',\think\Db::query("SELECT UUID() as uuid")[0]);
            
            $link = $this->getLink(Loader::Model('Project')->where('id', $pm->project_id)->find(), $uuid['uuid']);
            if($link){
                $pme = Loader::Model('ProjectCompanyExecute')->where([
                    'project_id' => $pm->project_id,
                    'company_id' => $pm->company_id,
                    'company_user_id' => $u
                ])->find();
                if($pme) {
                    // 判断用户是否已点击过
                    if($pme->state > 1){
                        $this->assign('img', IMG_PATH);
                        return $this->fetch("duplicate");
                    }else{
                        $pme->where('id', $pme->id)->update(['start_time' => date("Y-m-d h:i:s")]);
                    }
                }else{
                    // 记录数据
                    Loader::controller("ProjectCompanyExecute",'event')->insert([
                        'uuid' => $uuid['uuid'],
                        'company_user_id' => $u,
                        'project_id' => $pm->project_id,
                        'company_id' => $pm->company_id,
                        'start_time' => date("Y-m-d h:i:s"),
                        'finish_time' => date("Y-m-d h:i:s"),
                        'state' => 1
                    ]);
                }

                $this->redirect($link);
            }else{
                $this->assign('img', IMG_PATH);
                return $this->fetch("maintain");
            } 
        }else{
            $this->assign('img', IMG_PATH);
            $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
            return $this->fetch("invalid");
        }
    }

    // 内部会员点击问卷的回调
    public function innerlink($p, $u) { 
        $this->assign('img', IMG_PATH);
        $P = Loader::Model('Project');
        // 判断项目是否合法
        if($P->where('id', $p)->count() < 1) {
            $this->assign('msg', '请勿修改问卷链接，如有问题请及时联系管理员。');
            return $this->fetch("invalid");
        }
        // 判断用户是否合法  
        if(Loader::Model('Account')->where('id', $u)->count() < 1) {
            $this->assign('msg', '非内部会员，不能参与调查。');
            return $this->fetch("invalid");
        }      
        // 判断用户是否已点击过
        $ap = Loader::Model("AccountProject")->where(['project_id' => $p, 'user_id' => $u])->find();
        // 判断项目是否在执行
        $project = $P->where('id', $p)->find();
        if($project->state > 1) {
            return $this->fetch("close");
        }

        $link = $this->getLink($project, $u);
        if($link){
            // 记录数据
            if($ap){
                if($ap->state > 1){
                    return $this->fetch("duplicate");
                }else{
                    Loader::Model("AccountProject")->where('id', $ap->id)->update(['start_time' => date("Y-m-d h:i:s")]);
                }
            }else{
                Loader::controller("AccountProject",'event')->insert([
                    'user_id' => $u,
                    'project_id' => $p,
                    'start_time' => date("Y-m-d h:i:s"),
                    'finish_time' => date("Y-m-d h:i:s"),
                    'state' => 1,
                    'integral' => 0
                ]);
            }

            $this->redirect($link);
        }else{
            return $this->fetch("maintain");
        } 
    }

    // 判断项目调查数量是否完成，完成后更新项目状态
    private function isCompete($p, $s, $project) {
        if((int)$s === 2){
            $query = ['project_id' => $p, 'state' => 2];
            $innerCount = Loader::Model('AccountProject')->where($query)->count();
            $outCount = Loader::Model('ProjectCompanyExecute')->where($query)->count();
            if($project->amount === ($innerCount + $outCount)){
                $project->where('id', $p)->update(['state' => 3]);
                $s = 5;
            }
        }
        
        return $s;;
    }

    // 获取内部会员问卷完成视图（含状态更新）
    private function getInnerView($s, $p, $u, $project) 
    {
        $ap = Loader::Model('AccountProject');
        if($project->state < 2) {
            $s = $this->isCompete($p, $s, $project);
            if((int)$s === 2) {
                // 判断内部调查数量是否已完成
                $innerCount = $ap->where(['project_id' => $p, 'state' => 2])->count();
                $outAmount = Loader::Model('ProjectCompany')->where('project_id', $p)->sum('amount');
                if($innerCount >= ($project->amount - $outAmount)){
                    $s = 5;
                }
            }
        }else{
            $s = 5;
        }
        // 更新状态
        $record = $ap->where(['project_id' => $p, 'user_id' => $u])->find();
        $record->where(['id' => $record->id])->update([
                'state' => $s,
                'finish_time' => date("Y-m-d h:i:s"),
                'integral' => $this->getScore($p, $s)
            ]);       
        return $s;
    }

    // 获取外部会员问卷完成跳转链接（含状态更新）
    private function getOutLink($s, $p, $u, $pme, $project)
    {
        $s = $this->isCompete($p, $s, $project);
        $PM = Loader::Model("ProjectCompany")->where(['project_id' => $p, 'company_id' => $pme->company_id])->find();
        if($project->state < 2) {
            if((int)$s === 2) {
                // 判断外包数量是否已完成
                $count = Loader::Model("ProjectCompanyExecute")->where([
                    'project_id' => $p, 
                    'company_id' => $pme->company_id,
                    'state' => 2
                ])->count();
                if($count >= $PM->amount){
                    $s = 5;
                }
            }
        }else{
            $s = 5;
        }

        // 更新状态
        $pme->where(['id' => $pme->id])->update([
                'state' => $s,
                'finish_time' => date("Y-m-d h:i:s")
            ]);  

        if((int)$s === 2) {
            return $PM->link_compete.$pme->company_user_id;
        }else if((int)$s === 2){
            return $PM->link_refuse.$pme->company_user_id;
        }else{
            return $PM->link_full.$pme->company_user_id;
        }
    }

    // 获取问卷地址
    private function getLink($project, $u) {
        $link = null;
        if((int)$project['is_muilty_link'] > 0){
            $Event2 = \think\Loader::controller("ProjectLink",'event');
            $brr = $Event2->getEnableLink($project->id);
            if(count($brr) > 0){
                $link = $brr[0]['link'];
                $Event2->update($brr[0]['id']);
            }
        }else{
            $link = $project['link'];
        }

        if($link) {
            $link = trim($link);
            if(strpos($link, "?")){
                $link = $link."&".PARAM_KEY."=".$u;
            }else{
                $link = $link."?".PARAM_KEY."=".$u;
            }
        }
        
        return $link;
    }

    private function getScore($p, $s) {
        $score = 10;
        if((int)$s === 2){
            // TODO
        }
        return $score;
    }
}