<?php
class Logger{
	var $fp;
	function __construct($connections){
		if(defined('DEBUGMODE'))
			foreach($connections as $c)
				@$this->fp[] = fsockopen($c['host'], $c['port'], $errno, $errstr, 0.1);
	}
	public function log($message){
		if(defined('DEBUGMODE')){
			foreach($this->fp as $c){
				if ($c) {
					$bt = debug_backtrace();
					$caller = array_shift($bt);
					$file = explode("/",$caller[file]);
					$filename = $file[count($file)-2]."/".$file[count($file)-1];
					$message="$filename:$caller[line]|".getIpAddress()."\n".$message."\n\n";			
					@fwrite($c,$message );
					@fflush($c);
				}
			}
		}
	}
	public function close(){
		if(defined('DEBUGMODE'))
			foreach($this->fp as $c)
				@fclose($c);
	}
	
}
