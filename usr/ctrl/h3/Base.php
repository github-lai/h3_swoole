<?php
namespace Ctrl\H3;
use Lib;
use Model;

class Base extends Lib\CtrlBase{
	public $helper = null;

	function __construct(){
		$this->set("_root",Lib\Config::get("root"));
		$this->set("_cfgurl", Lib\Config::get("url"));
	}

	function IsLogin()
	{
		$auth = new Lib\Auth($this->request,$this->response);
		$html = "";
		if($auth->check("admin")){
			$arr = $auth->get("admin");
			$html  =  $arr["username"].' <a href="@admin/welcome" style="color:yellow;">进入用户中心</a> <a href="base/logout" style="color:yellow;">退出</a>';
		}else{
			$html  = '<a href="login" style="color:yellow;">登录</a>';
		}
		return $html;
	}

	//验证码
	function Vcode()
	{
		$an = new Lib\Captcha();
		$rsp = new Lib\Rsp($an->create("vcode"));
		$rsp->head['content-type']='image/png';
		return $rsp;
	}

	function Logout()
	{
		$auth = new Lib\Auth($this->request,$this->response);
		$auth->remove("admin");
		return $this->redirect(Lib\Config::get("url")."home/index");
	}

}

?>