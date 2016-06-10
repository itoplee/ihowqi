<?php
namespace app\admin\controller;
use think\Input;

class Upload
{
	public function upload(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = Input::file('file');
	    // 移动到服务器的上传目录 /public/upload/
	    $info = $file->move(ROOT_PATH.'/public/upload/');
	    $arr = $this->readFile(ROOT_PATH.'/public/upload/'.date("Y-m-d").'/'.$info->getFilename());
	    if($info){
			return '{"code": 200, "name":"'.$info->getFilename().'", "len":'.count($arr).'}';
	    }else{
	        // 上传失败获取错误信息
	        return '{"code": 201, "error":"'.$file->getError().'"}'; 
	    }
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