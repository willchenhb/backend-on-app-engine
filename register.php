<?php
    require_once "./config/config.inc.php";
    require_once ROOT_LIBPATH."http_code.lib.php";
    require_once ROOT_LIBPATH."util.lib.php";
    require_once ROOT_LIBPATH."xml.lib.php";
    require_once ROOT_LIBPATH."dbhelper.lib.php";

    global $logger;
    
    $params = $_GET;
    if (Util::check_register_input($params) === false) {
        $logger->Fatal("Input error : ".serialize($params));
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $table = 'user_account_info';
    $insert_data['user_id'] = '';
    $insert_data['imei'] = $params['imei'];
    $insert_data['create_time'] = time();
    $insert_data['update_time'] = $insert_data['create_time'];
    
    $db_helper = new DBHelper();
    $result = $db_helper->insert_data_in_user_account_info($table, $insert_data);    
    if ($result === false) {
        $logger->Fatal("insert_data_in_user_account_info failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    }
    
    $userid = $db_helper->get_last_insert_autoincrease_id();
    if ($userid === false) {
        $logger->Fatal("get_last_insert_autoincrease_id failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    }
    
	$xml_data['userid'] = $userid;
    $xml_writer = new xml();
	$xml_writer->make_xml($xml_data);    

	http_response($HTTP_CODE['SUCCESS']);