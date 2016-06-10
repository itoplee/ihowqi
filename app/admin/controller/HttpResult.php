<?php
namespace app\admin\controller;

class HttpResult
{
	function __construct($data) {
		$this->data = $data;
	}
	public $code = 200;
	public $data = null;
	public $error = "";

	public function setError($code, $error) {
		$this->code = $code;
		$this->error = $error;
	}
}