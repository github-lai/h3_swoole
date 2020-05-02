<?php

namespace Ctrl\Admin;

class Logout extends Base
{

	function __construct(){
		//parent::__construct();//如果不显式调用父类的构造函数，默认是不会自动调用的
	}

	function Index()
	{
		session_start();
		session_destroy();
		return $this->redirect("login");
	}

}  

?>