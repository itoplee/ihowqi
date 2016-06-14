<?php
namespace app\admin\event;
use think\Db;

class ProjectLink 
{
    public function getLinks($map, $start, $count) {
        return db("project_link")
            ->where($map)
            ->limit($start * $count, $count)
            ->select();
    }

    public function getLinksTotal($map) {
        return db("project_link")
            ->where($map)
            ->count();
    }

    // 获取导入批次
    public function getVersion($projectId)
    {       
        return db("project_link")
            ->field('DISTINCT(`version`)')
            ->where('project_id', $projectId)
            ->select();
    }

    // 获取一条可用链接
    public function getEnableLink($projectId) {
        return db("project_link")
            ->where('project_id', $projectId)
            ->where('used', 1)
            ->where('is_delete', 0)
            ->limit(0, 1)
            ->select();
    }

    // 获取可用链接数
    public function getEnableLinkCount($projectId) {
        return db("project_link")
            ->where('project_id', $projectId)
            ->where('used', 1)
            ->where('is_delete', 0)
            ->count();
    }

    // 获取最大导入批次
    public function getMaxVersion($projectId) {
        return db("project_link")->where('project_id', $projectId)->max('version');
    }

	public function insert($projectId, $link, $version)
    {    	
    	$data = [
        	'project_id' => $projectId,
        	'link' => $link,
        	'used' => 1,
        	'insert_time' => date("Y-m-d h:i:s"),
        	'update_time' => date("Y-m-d h:i:s"),
            'version' => $version,
            'is_delete' => 0
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

    public function enableLink($map, $val)
    {
        db("project_link")->where($map)->update(['is_delete' => $val]);
    }

    public function deleteProject($projectId) {
    	db("project_link")->where('project_id', $projectId)->delete();
    }
}