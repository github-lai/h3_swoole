<?php

namespace Auth;
use Lib;

class AdminAuth implements Lib\IBase\IAuth
{
	public $request = null;
	public $response = null;

	function __construct($request,$response){
		$this->request = $request;
		$this->response = $response;
	}

	function valid($act)
	{
		$auth = new Lib\Auth($this->request,$this->response);
		return $auth->check("admin");
	}
	
	function allow()
	{
		return;
	}

	function deny()
	{
		$path = Lib\Config::get("url")."login";
		$this->response->redirect($path);
		$this->response->end();
	}


}

?>