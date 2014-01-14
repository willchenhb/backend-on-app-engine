<?php
    require_once "./config/config.inc.php";
    require_once ROOT_LIBPATH."http_code.lib.php";
    require_once ROOT_LIBPATH."util.lib.php";
    require_once ROOT_LIBPATH."dbhelper.lib.php";
    
    global $logger;
        
    $params = $_POST;
    if (Util::check_postpayedlist_input($params) == false) {
        $logger->Fatal("Input error : ".serialize($params));        
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $table = 'qb_pay_list';
    $set_data['paid'] = 1;
    $set_data['update_time'] = time();
    $in_field = 'transaction_id';
    $in_values = explode(',', $params['idlist']);
    
    $db_helper = new DBHelper();
    $result = $db_helper->update_unpaid_list_in_qb_pay_list($table, $set_data, $in_field, $in_values);    
    
    if ($result === false) {
        $logger->Fatal("update_unpaid_list_in_qb_pay_list failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    }
    
    http_response($HTTP_CODE['SUCCESS']);    
?>