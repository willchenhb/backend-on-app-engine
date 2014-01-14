<?php
    require_once ROOT_LIBPATH."/mysql.lib.php";
    
    class DBHelper{
        private $db;
        private $logger;
        
        public function __construct() {
            global $logger;
            $this->logger = $logger;
            
            $this->db = new DB();
            $ret = $this->db->connect();
            if ($ret === false) {
                $this->logger->Fatal("Connecting to database failed.");
                http_response($HTTP_CODE['CONNECT FAILED']);
            }
        }
        
        public function add_data_in_user_account_info($table, $where_fields, $add_data, $set_data) {
            $update_querys[] = $this->db->get_update_add_sql($table, $where_fields, $add_data, $set_data);
            $select_query = $this->db->get_select_sql($table, $where_fields);
            
            $result = $this->db->query_with_transaction($update_querys, $select_query);
            if ($result === false) {
                $this->logger->Fatal("Transaction query failed : ".serialize($update_querys)."; select : ".$select_query);
            }
            if ($result === null) {
                $this->logger->Fatal("Transaction return empty : ".serialize($where_fields));
            }
           
            return $result;
        }
        
        public function insert_data_in_user_account_info($table, $insert_data) {
            $insert_query = $this->db->get_insert_sql($table, $insert_data);  
            
            $result = $this->db->execute($insert_query);
            if ($result === false) {
                $this->logger->Fatal("Query failed : ".$insert_query);
            }            
            
            return $result;
        }
        
        public function get_last_insert_autoincrease_id() {
            $id = $this->db->query_last_insert_id();
            if ($id === false) {
                $this->logger->Fatal("Query failed : select last insert id()");
            }
            
            return $id;
        }
        
        public function reduce_data_in_user_account_info($tables, $where_fields, $add_datas, $set_datas, $insert_datas) {
            $insert_update_querys[] = $this->db->get_update_add_sql($tables['user_account'], $where_fields['user_account'], $add_datas['user_account'], $set_datas['user_account']);
            $select_query = $this->db->get_select_sql($tables['user_account'], $where_fields['user_account']);
            $insert_update_querys[] = $this->db->get_insert_sql($tables['qb'], $insert_datas['qb']);
            $insert_update_querys[] = $this->db->get_insert_sql($tables['user_identity'], $insert_datas['user_identity']);            
            $candidate_update_query = $this->db->get_update_sql($tables['user_identity'], $where_fields['user_identity'], $set_datas['user_identity']);
            //get_update_add_sql($tables['qb'], $where_fields['qb'], $add_datas['qb'], $set_datas['qb']);

            $result = $this->db->query_with_transaction($insert_update_querys, $select_query, $candidate_update_query);
            
            if ($result === false) {
                $this->logger->Fatal("Transaction query failed : ".serialize($insert_update_querys)."; candidate : ".$candidate_update_query."; select : ".$select_query);
            }
            if ($result === null) {
                $this->logger->Fatal("Transaction return empty : ".serialize($where_fields['user_account']));
            }
            
            return $result;
        }
        
        public function get_ad_id_in_ad_list($table, $where_fields) {
            $select_query = $this->db->get_select_sql($table, $where_fields);
            
            $result = $this->db->query_row($select_query);
            if ($result === false) {
                $this->logger->Fatal("Query failed : ".$select_query);
            }
            if ($result === null) {
                $this->logger->Fatal("Query return empty : ".serialize($where_fields));
            }
            
            return $result;
        }
        
        public function get_data_in_user_account_info($table, $where_fields) {
            $select_query = $this->db->get_select_sql($table, $where_fields);
            
            $result = $this->db->query_one_field($select_query, 'account');     
            if ($result === false) {
                $this->logger->Fatal("Query failed : ".$select_query);
            }
            if ($result === null) {
                $this->logger->Fatal("Query return empty : ".serialize($where_fields));
            }      
            
            return $result;
        }
        
        public function get_unpaid_list_in_qb_pay_list($table, $between_params, $where_fields) {
            $select_query = $this->db->get_select_between_sql($table, $between_params, $where_fields);
            
            $result = $this->db->query($select_query);        
            if ($result === false) {
                $this->logger->Fatal("Query failed : ".$select_query);
            }
            
            return $result;    
        }
        
        public function update_unpaid_list_in_qb_pay_list($table, $set_data, $in_field, $in_values) {
            $update_query = $this->db->get_update_in_between_sql($table, $set_data, $in_field, $in_values);
            $result = $this->db->execute($update_query);     
            if ($result === false) {
                $this->logger->Fatal("Query failed : ".$update_query);
            }       
            
            return $result;
        }

    };