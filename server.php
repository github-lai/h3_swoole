<?php

class Server {
	private $_serv = null;

	public function __construct()
    {
        $this->_serv = new \Swoole\Http\Server("0.0.0.0", 9906);
        $this->_serv->set(array(
		'log_file'=>'/home/wwwroot/default/log/swoole.log',
		'worker_num' => 2, 
		'daemonize' => true, 
		'max_wait_time' => 60,
		'reload_async' => true,
		'document_root' => __DIR__ . '/',//配置静态文件根目录
		'enable_static_handler' => true,//开启静态文件请求处理功能，这样当请求的是一个静态文件时，swoole自动会在上面配置的目录中查找并返回
		));

        $this->_serv->on('Start', array($this, 'onStart'));
        $this->_serv->on('Request', array($this, 'onRequest'));
        $this->_serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->_serv->on('Connect', array($this, 'onConnect'));
        $this->_serv->on('Receive', array($this, 'onReceive'));
        $this->_serv->on('Close', array($this, 'onClose'));
        $this->_serv->start();
    }

	function onRequest($request, $response) {
		//浏览器请求时会发起下面的额外请求，返回404即可
		if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
			$response->end();
			return;
		}else{
			//rsp = Lib\Rsp
			$rsp = Lib\Router::dispatch($request, $response);//$request跟着走才能获取$_GET和$_POST数据
			//Lib\Helper::info($request->server['path_info'].'\r\n'.var_export($rsp,true));
			$response->status($rsp->status, 200);
			foreach($rsp->head as $key=>$val){
				$response->header($key, $val, true);
			}
			$content = $rsp->content;
			$response->end($content);
		}
	}

	function onReceive(\Swoole\Http\Server $serv, $fd, $from_id, $data ) {
		if($data == 'reload'){
			$serv->reload();//当接收到客户端消息时更新代码，下次再执行work代码时变（onWorkerStart），本次执行还是不变
		}
		if($data == 'shutdown'){
			$serv->shutdown();
		}
        echo "Get Message From Client {$fd}:{$data}\n";
    }

	// worker 中的 ManagerStart | WorkerStart 事件是并发执行的, 不一定按顺序来
	// ManagerStart 可能在 WorkerStart 之后执行

	// 每个 Worker 进程启动或重启时都会执行
	function onWorkerStart(\Swoole\Http\Server $server, int $workerId) {
		require 'vendor/autoload.php';
		echo 'WorkerStart: ' . PHP_EOL . PHP_EOL;
		echo 'WorkerID: ' . $workerId . PHP_EOL . PHP_EOL;
	}

	// 服务器启动时执行一次
	function onStart(\Swoole\Http\Server $server) {
		swoole_set_process_name("live_master");//设置名字是为了方便sh脚本自动重启master进程
		echo PHP_EOL . PHP_EOL . 'Starting server now...' . PHP_EOL . PHP_EOL;
	}

	// 服务器启动时执行一次
	function onManagerStart(\Swoole\Http\Server $server) {
		echo 'ManagerStart: ' . PHP_EOL . PHP_EOL;
	}

	// 每次连接时(相当于每个浏览器第一次打开页面时)执行一次, reload 时连接不会断开, 也就不会再次触发该事件
	function onConnect(\Swoole\Http\Server $server, int $fd, int $reactorId) {
		echo 'Connect: ' . PHP_EOL . PHP_EOL;
		echo '    Worker ID: '. $server->worker_id . PHP_EOL . PHP_EOL;
		echo '    fd: ' . $fd . ' , reactorId: ' . $reactorId . PHP_EOL . PHP_EOL;
	}

	// 每个浏览器连接关闭时执行一次, reload 时连接不会断开, 也就不会触发该事件
	function onClose(\Swoole\Http\Server $server, int $fd, int $reactorId) {
		echo 'Close: ' . PHP_EOL . PHP_EOL;
		echo '    fd: '. $fd .' , reactorId: ' . $reactorId . PHP_EOL . PHP_EOL;
	}

	// 每个 Worker 进程退出或重启时执行一次
	function onWorkerStop(\Swoole\Http\Server $server, int $workerId) {
		echo 'WorkerStop' . PHP_EOL . PHP_EOL;
		echo '    Worker ID:' . $workerId . PHP_EOL . PHP_EOL;
	}

	// 服务器关闭时执行一次
	function onShutdown(\Swoole\Http\Server $server) {
		echo 'Shutdown: ' . PHP_EOL . PHP_EOL;
	}

}

new Server();

?>