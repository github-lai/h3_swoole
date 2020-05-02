<?php
namespace Model;
use Lib;

class User extends Lib\DbBase{
	function __construct(){
		$this->setName('user'); 
	}

	function delBy($id)
	{
		if($this->where("iid in('".$id."')")->remove()){
			return "ok";
		}else{
			return "error";
		}
	}

	function getlist($start,$ps)
	{
		return $this->where("iid>0")->desc("iid")->limit($start,$ps)->page();
	}

	function getuser($username,$userpass)
	{
		return $this->where("username='".$username."' and userpass='".$userpass."'")->select();
	}

    function save($iid,$pass)
	{
		$arr = array("userpass"=>$pass);

		return $this->where("`iid`=$iid")->kv($arr)->update();
	}


	function add($username,$userpass)
	{
		$arr = array(
		"username"=>$username,
		"userpass"=>$userpass);

		$iid = $this->kv($arr)->add();
		return $iid;
	}


} 

?>