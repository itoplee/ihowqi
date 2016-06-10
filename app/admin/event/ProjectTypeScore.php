<?php
namespace app\admin\event;
use think\Db;

class ProjectTypeScore
{
	public function getTypeScores($typeId)
    {    	
        return db("project_type_score")->where('type_id', $typeId)->select();
    }

    public function insert($typeId, $min, $max, $score)
    {
    	db("project_type_score")->insert([
    		'type_id' => $typeId, 
    		'min_time' => $min, 
    		'max_time' => $max, 
    		'score' => $score
    	]);
    }

    public function update($id, $typeId, $min, $max, $score)
    {
    	db("project_type_score")->update([
            'type_id' => $typeId, 
            'min_time' => $min, 
            'max_time' => $max, 
            'score' => $score, 
            'id' => $id
        ]);;
    }

    public function delete($id)
    {
    	db("project_type_score")->delete($id);
    }
}