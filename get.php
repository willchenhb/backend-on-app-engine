<?php
    require_once "./config/config.inc.php";
    require_once ROOT_LIBPATH."http_code.lib.php";
    require_once ROOT_LIBPATH."util.lib.php";
    require_once ROOT_LIBPATH."xml.lib.php";
    require_once ROOT_LIBPATH."dbhelper.lib.php";
    
    global $logger;
        
    $params = $_GET;
    if (Util::check_get_input($params) == false) {
        $logger->Fatal("Input error : ".serialize($params));
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $table = 'user_account_info';
    $where_fields['user_id'] = $params['userid'];
    $where_fields['imei'] = $params['imei']; 
    
    $db_helper = new DBHelper();
    $result = $db_helper->get_data_in_user_account_info($table, $where_fields); 
    
    if ($result === false) {
        $logger->Fatal("get_data_in_user_account_info failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    }
    if ($result === null) {
        $logger->Fatal("get_data_in_user_account_info return empty.");
        http_response($HTTP_CODE['INPUT ERROR']);
    }   
    
    $xml_data['account'] = $result[0];
    $xml_writer = new xml();
    $xml_writer->make_xml($xml_data);  
    
    http_response($HTTP_CODE['SUCCESS']);