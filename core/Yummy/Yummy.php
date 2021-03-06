<?php
//require(SYSTEM_DIR.'/Yummy/Configs.inc.php');//加载配置
class Yummy_Yummy {
    public $method;
    public $params;
    function run(){
        $this->Dispatcher();
		//$className = DEFAULT_MODULE."_Action_".$this->action;
        $map = $this->map();
        //print_r($map);
        $controller = new $map;
		if(!method_exists($controller,$this->fun)){
			exit('the function is not exist:'.$this->fun);
		}else{
			$controller->setParams($this->params);
			$author = true;
			if(method_exists($controller,"authority")){
				$author = $controller->authority();
			}
			if($author){
				$controller->{$this->method}();
			}else{
				//exit("authority not pass");
				Yummy_Object::error("authority not be passed!");
				exit;
			}
		}
    }
    /**
     * 拦截
     */
    function Dispatcher(){
        $this->action = ucfirst(isset($_GET['c']) ? $_GET['c'] : DEFAULT_ACTION); 
        $this->fun = isset($_GET['ac']) ? $_GET['ac'] : DEFAULT_FUNCTION;
        //Yummy_Object::debug('current function is :'.$this->fun.'| action is:'.$this->action,__CLASS__);
    }
    function map(){
        $uri = $_SERVER['REQUEST_URI'];
        $uri = str_replace(strrchr($uri,'?'),"",$uri);//? in url
        $uri= (string)(trim($uri,'/'));
        $tokens =explode('/',$uri);
        //array_shift($tokens);
        urldecode(array_shift($tokens));
        //print_r($tokens);
        if(empty($tokens)) {
            $moduleNamespace='';
        } else{
            $alias = urldecode(array_shift($tokens));
            $moduleNamespace = $alias;
        }
        
        if(empty($moduleNamespace)){
            $moduleNamespace = DEFAULT_MODULE;
        }

        $actionName = urldecode(array_shift($tokens));
        if(empty($actionName)){
            $actionName = DEFAULT_ACTION;
        }
        if(empty($actionName)){
            throw new Exception('Cannot mapping request to any valid action!');
        }
        
        if(empty($tokens)){
            $method = DEFAULT_FUNCTION;
        }else{
            $method = urldecode(array_shift($tokens));
        }

        $params = array();
        $nameOk = true;

        //check id format
        if(count($tokens)%2!=0 ){
            $paramName = 'id';
            $nameOk = false;
        }
        for($i=0;$i<count($tokens);$i++){
            if($nameOk){
                $paramName = urldecode($tokens[$i]);
                $nameOk = false;
            }else{
                $paramValue = urldecode($tokens[$i]);
                if(!empty($paramName)){
                    $params[$paramName] = $paramValue;
                }
                $nameOk = true;
            }
        }

        $this->params = $params;
        foreach ($_GET as $key=>$value){
        	$this->params[$key] = $value;
        }
        foreach ($_POST as $key=>$value){
        	$this->params[$key] = $value;
        }
        $moduleNamespace = ucfirst($moduleNamespace);
        $actionName = ucfirst($actionName);
        $this->method = Yummy_Util_Inflector::methodlize($method);
        $result = "{$moduleNamespace}_Action_{$actionName}";
        return $result;
    }
}
?>
