<?php

namespace Ctrl\H3;
use Lib;
use Model;

class Home extends Base
{

	function Index()
	{
		$this->set("say","hello word!!");
		return $this->view();
	}

	function About()
	{
		return $this->view("About");
	}

	function Users()
	{
		$user = new Model\User;

		$pagesize = 10;
		$cur = isset($_GET["page"]) && is_numeric($_GET["page"]) ? $_GET["page"] : 1;

		$start = ($cur-1) * $pagesize;
		$arr = $user->getlist($start,$pagesize);

		$total = $arr["foundrows"];
		$list = $arr["data"];

		$pager = new Lib\Pager($cur,$total,$pagesize);

		$this->set("list",$list);
		$this->set("pager", $pager);
		
		return $this->view("Users");
	}

	function Remove($iid)
	{
		$user = new Model\User;
		$user->delBy($iid);

		return $this->action("home/users");
	}

	function Modify($iid)
	{
		$pass = $this->request->post["pass"];
		$user = new Model\User;
		
		$bool = $user->save($iid,$pass);
		
		return $bool === true ?"1":"0";
	}

	function AddUser()
	{
		$user = new Model\User;
		$iid = $user->add("admin","admin");
		return $iid;
	}

}  

?>