<?php
    require_once "./config/config.inc.php";
    require_once ROOT_LIBPATH."http_code.lib.php";
    require_once ROOT_LIBPATH."util.lib.php";
    require_once ROOT_LIBPATH."xml.lib.php";
    require_once ROOT_LIBPATH."dbhelper.lib.php";
    
    global $logger;
    
    $params = $_GET;
    if (Util::check_getadid_input($params) == false) {
        $logger->Fatal("Input error : ".serialize($params));
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $ad_id = Util::compute_ad_id($params);
    $table = 'ad_list';
    $where_fields['ad_id'] = $ad_id;

    $db_helper = new DBHelper();
    $result = $db_helper->get_ad_id_in_ad_list($table, $where_fields);
  
    if ($result === false) {
        $logger->Fatal("get_ad_id_in_ad_list failed.");
        http_response($HTTP_CODE['QUERY FAILED']);
    }
    if ($result === null) {
        $logger->Fatal("get_ad_id_in_ad_list return empty.");
        http_response($HTTP_CODE['INPUT ERROR']);
    }
    
    $xml_data['adid'] = $result['ad_id'];
    $xml_data['url'] = $result['url'];
    $xml_data['length'] = $result['length'];
    $xml_data['adword'] = $result['ad_word'];
    $xml_writer = new xml();
    $xml_writer->make_xml($xml_data);
       
    
    http_response($HTTP_CODE['SUCCESS']);