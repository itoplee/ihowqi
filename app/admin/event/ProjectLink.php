<?php
namespace app\admin\event;
use think\Db;

class ProjectLink 
{
    public function getEnableLink($projectId) {
        return db("project_link")
            ->where('project_id', $projectId)
            ->where('used', 1)
            ->limit(0, 1)
            ->select();
    }

    public function getEnableLinkCount($projectId) {
        return db("project_link")
            ->where('project_id', $projectId)
            ->where('used', 1)
            ->count();
    }

	public function insert($projectId, $link)
    {    	
    	$data = [
        	'project_id' => $projectId,
        	'link' => $link,
        	'used' => 1,
        	'insert_time' => date("Y-m-d h:i:s"),
        	'update_time' => date("Y-m-d h:i:s")
        ];
        db("project_link")->insert($data);
    }

    public function update($id)
    {
        db("project_link")->update([
            'used' => 0,
            'update_time' => date("Y-m-d h:i:s"),
            'id' => $id
        ]);
    }

    public function deleteProject($projectId) {
    	db("project_link")->where('project_id', $projectId)->delete();
    }
}