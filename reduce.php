<?php
    require_once "./config/config.inc.php";
    require_once ROOT_LIBPATH."http_code.lib.php";
    require_once ROOT_LIBPATH."util.lib.php";
    require_once ROOT_LIBPATH."xml.lib.php";
    require_once ROOT_LIBPATH."dbhelper.lib.php";
    
    global $logger;

    $params = $_GET;
    if (Util::check_reduce_input($params) == false) {
        $logger->Fatal("Input error : ".serialize($params));
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $account_reduced = Util::compute_account_reduced($params['type'], $params['qq'], $params['qb']);
    
    $tables['user_account'] = 'user_account_info';
    $add_datas['user_account']['account'] = $account_reduced;
    $set_datas['user_account']['update_time'] = time();
    $where_fields['user_account']['user_id'] = $params['userid'];
    $where_fields['user_account']['imei'] = $params['imei'];  
    
    
    $tables['qb'] = 'qb_pay_list';
    $insert_datas['qb']['transaction_id'] = '';
    $insert_datas['qb']['user_id'] = $params['userid'];
    $insert_datas['qb']['imei'] = $params['imei'];
    $insert_datas['qb']['qq'] = $params['qq'];
    $insert_datas['qb']['qb'] = $params['qb'];
    $insert_datas['qb']['paid'] = false;
    $insert_datas['qb']['create_time'] = time();
    $insert_datas['qb']['update_time'] = $insert_datas['qb']['create_time'];
    //$where_fields['qb']['qq'] = $params['qq'];
    //$add_datas['qb']['qb'] = $params['qb'];
    //$set_datas['qb']['update_time'] = time();
    
    $tables['user_identity'] = 'user_identity_info';
    $insert_datas['user_identity']['user_id'] = $params['userid'];
    $insert_datas['user_identity']['qq'] = $params['qq'];
    $insert_datas['user_identity']['create_time'] = time();
    $insert_datas['user_identity']['update_time'] = $insert_datas['user_identity']['create_time'];
    $set_datas['user_identity']['qq'] = $params['qq'];
    $set_datas['user_identity']['update_time'] = time();
    $where_fields['user_identity']['user_id'] = $params['userid'];
    
    $db_helper = new DBHelper();
    $result = $db_helper->reduce_data_in_user_account_info($tables, $where_fields, $add_datas, $set_datas, $insert_datas);
    
    
    if ($result === false) {
        $logger->Fatal("reduce_data_in_user_account_info failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    } 
    if ($result === null) {
        $logger->Fatal("reduce_data_in_user_account_info return empty.");
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $xml_data['account'] = $result[0]['account'];
    $xml_writer = new xml();
    $xml_writer->make_xml($xml_data);  
        
    http_response($HTTP_CODE['SUCCESS']);