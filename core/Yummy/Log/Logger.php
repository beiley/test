<?php
/**
 * 日志输出类
 * Logger
 * beiley(beiley@163.com) 07.12.28
 * ex:
   $log = new Logger();
   $log->debug('huhu');
 */
class Yummy_Log_Logger {

	public $g_log_level = 'debug';
	public $g_log_file = "Logs/error.log";
	public $g_log_levels = array (
		'none' => 0,
		'error' => 1,
		'info' => 2,
		'debug' => 3
	);

    function __construct() {
		//$ext = explode(".", $logfile);
		$log = Yummy_Config::get("logger");
		$this->g_log_file = $log["path"];
		$this->g_log_level = strtolower($log["type"]);
	}

	function info($message,$class=__CLASS__) {

		$level = 'info';
		if ($this->g_log_levels[$this->g_log_level] >= $this->g_log_levels[$level]) {
			//$message=date("D M j G:i:s T Y").$message;
			$message = $class." : ".date("Y-m-d H:i:s",time() + 8*60*60) . "  INFO [" . $_SERVER['REQUEST_URI'] . "]   " . $message;
			$this->write_log($message);
		}
	}
	function error($message,$class=__CLASS__) {

		$level = 'error';
		if ($this->g_log_levels[$this->g_log_level] >= $this->g_log_levels[$level]) {
			$message = $class." : ".date("Y-m-d H:i:s",time() + 8*60*60) . "  ERROR [" . $_SERVER['REQUEST_URI'] . "]   " . $message;
			$this->write_log($message);
		}
	}
	function debug($message,$class=__CLASS__) {
		$level = 'debug';
		if ($this->g_log_levels[$this->g_log_level] >= $this->g_log_levels[$level]) {
			$message = $class." : ".date("Y-m-d H:i:s",time() + 8*60*60) . "  DEBUG [" . $_SERVER['REQUEST_URI'] . "]   " . $message;
			$this->write_log($message);
		}
	}
	function reset() {
		@ unlink($this->g_log_file);
	}

	function write_log($message) {
		$message = "current module:".MODULE_NAME."|".$message;
		if ((strcmp($this->g_log_file, "") == 0) || (strcmp($this->g_log_file, "logfile") == 0)) {
			echo $message;
		}else{
			if(is_writable($this->g_log_file)){
				error_log($message."\n",3,$this->g_log_file);
			}else {
				//mkdir
				if(!file_exists($this->g_log_file)){
					$handle = @fopen($this->g_log_file,'w');
				}
				if(!chmod($this->g_log_file,666)){
					$info = "{$this->g_log_file}:the file can't be writed!";
					//self::debug($info);
					throw new Yummy_Exception($info);
				}else{
					error_log($message."\n",3,$this->g_log_file);
				}
			}
		}
	}
	function __destruct() {
	}
}
?>