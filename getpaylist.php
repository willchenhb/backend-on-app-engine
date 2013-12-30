<?php
    require_once "./config/config.inc.php";
    require_once ROOT_LIBPATH."http_code.lib.php";
    require_once ROOT_LIBPATH."util.lib.php";
    require_once ROOT_LIBPATH."xml.lib.php";
    require_once ROOT_LIBPATH."dbhelper.lib.php";
    
    global $logger;
             
    $params = $_GET;
    if (Util::check_getpaylist_input($params) == false) {
        $logger->Fatal("Input error : ".serialize($params));
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $table = 'qb_pay_list';  
    $between_params['field'] = 'transaction_id';
    $between_params['start'] = $params['startid'];
    $between_params['end'] = $params['endid'];
    $where_fields['paid'] = false;
    
    $db_helper = new DBHelper();
    $result = $db_helper->get_unpaid_list_in_qb_pay_list($table, $between_params, $where_fields); 
    
    if ($result === false) {
        $logger->Fatal("get_unpaid_list_in_qb_pay_list failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    }
    
    $xml_data = array();
    foreach ($result as $entry) {
        $xml_data[] = array('qq' => $entry['qq'], 'qb' => $entry['qb']);
    }
    $xml_writer = new xml();
    $xml_writer->make_xml($xml_data);  
    
    http_response($HTTP_CODE['SUCCESS']);
?>