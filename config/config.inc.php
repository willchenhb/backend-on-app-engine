<?php 
    /*配置数据库名（可从管理中心查看到）*/
    define('DB_NAME','yYtFsorayeqrjIKUmGIQ');
    
	/*配置数据库连接需要的参数*/
	$host = 'sqld.duapp.com';
	$port = 4050;
	$ak = 'gxLMGxsKv6q3WRAKxBZwuidD';
	$sk = 'PEWOvh2h1aoHEVIYjRhsZ2iVZIhHf8TL';
	define('DB_HOST', $host . ':' . $port);
    define('DB_USERNAME', $ak);
    define('DB_PASSWORD', $sk);
    
    /*配置函数库路径*/
    define('ROOT_PATH', '.');
    define('ROOT_LIBPATH', ROOT_PATH.'/lib/');
    
    /*配置Log*/
    require_once(ROOT_LIBPATH."BaeLog.class.php");    
    $secret = array("user"=>$ak, "passwd"=>$sk);
    global $logger;
    $logger = BaeLog::getInstance($secret);
    $logger->setLogLevel(16);
    
    /*配置PHP时区*/
    date_default_timezone_set('Asia/Shanghai');

	/*设置超时间隔*/
	set_time_limit(5);