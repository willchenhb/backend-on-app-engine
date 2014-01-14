<?php
    class DB{
        private $link;
        private $logger;

        function __construct() {
            $this->link = null;
            global $logger;
            $this->logger = $logger;
        }
        
        function __destruct() {
            $this->disconnect();
        }
        
        /*数据库连接函数，参数在常量中指定*/
        function connect()
        {  
            //$this->logger->Debug("Connecting to database.");
            $this->link = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
            if ($this->link === false) {
                return false;
            }
            
            $ret = mysql_query('use '.DB_NAME, $this->link);
            if ($ret === false) {
                return false;
            }
            
            $ret = mysql_query('set names utf8');
            if ($ret === false) {
                return false;
            }
            
            //$this->logger->Debug("Connecting to database successfully.");
            return true;
        }
        
        /*组合成查询语句，where从句支持多个域$fields匹配*/
        function get_select_sql($table, $fields)
        {
            $fieldArray = array();
            foreach ($fields as $key => $value) {
                $fieldArray[] = $key."='".$value."'";
            }
            
            return 'select * from '.$table.' where '.implode('&&', $fieldArray);
        }
        
        /*组合成查询语句，where从句支持一个域匹配*/
        function get_select_one_sql($table, $field, $value)
        {
            return 'select * from '.$table.' where '.$field.'='."'".$value."'";
        }
        
        function get_select_between_sql($table, $params, $fields = array())
        {
            $sql = 'select * from '.$table.' where '.$params['field']." between '".$params['start']."' and '".$params['end']."'";
            
            $field_array = array();
            foreach ($fields as $key => $value) {
                $field_array[] = $key."='".$value."'";
            }
            if (count($field_array) != 0) {
                $sql .= ' && '.implode('&&', $field_array);
            }
            return $sql;
        }
        
        /*组合成数目查询语句，where从句支持一个域匹配*/
        function get_count_one_sql($table, $field, $value)
        {
            return 'select count(*) as count from '.$table.' where '.$field.'='."'".$value."'";
        }
        
        /*组合成全字段插入语句，$data为需要插入的数据*/
        function get_insert_sql($table, $data)
        {
            $keys = array();
            $values = array();
            foreach ($data as $key => $value) {
                $keys[] = $key;
                $values[] = "'".$value."'";
            }
            
            return "insert into ".$table." (".implode(',', $keys).")"." values(".implode(',',$values).");";
        }
        
        /*组合成更新语句，按$fields查找指定的域、按$data更新指定的域值为$data[$key]]*/
        function get_update_sql($table, $fields, $data)
        {
            $dataArray = array();
            foreach ($data as $key => $value) {
                $dataArray[] = $key."='".$value."'";
            }
            foreach ($fields as $key => $value) {
                $fieldArray[] = $key."='".$value."'";
            }
            
            return "update ".$table. " set ".implode(',', $dataArray)." where ".implode('&&', $fieldArray).";";
            //return "update ".$table. " set ".$fieldU."="."'".$valueU."' "."where ".$fieldS."="."'".$valueS."';"; 
        }
        
        /*
        *   组合成更新语句，按$fields查找指定的域,
        *   按$add_data更新指定的域值为$key + $data[$key]]，
        *   按$set_data更新指定的域值为$data[$key]
        */
        function get_update_add_sql($table, $fields, $add_data, $set_data = array())
        {
            $add_data_array = array();
            foreach ($add_data as $key => $value) {
                $add_data_array[] = $key."=".$key."+'".$value."'";
            }
            
            $set_data_array = array();
            foreach ($set_data as $key => $value) {
                $set_data_array[] = $key."='".$value."'";
            }
            
            foreach ($fields as $key => $value) {
                $field_array[] = $key."='".$value."'";
            }
            
            $set_sql = "set ".implode(',', $add_data_array);
            $set_sql .= (count($set_data_array) != 0) ? (','.implode(',', $set_data_array)) : '';
            
            return "update ".$table. " ".$set_sql." where ".implode('&&', $field_array).";";
            //return "update ".$table. " set ".$fieldU."="."'".$valueU."' "."where ".$fieldS."="."'".$valueS."';";           
        }
        
        function get_update_in_between_sql($table, $set_data, $in_field, $in_values, $between_params = array(), $fields = array())
        {
            $set_data_array = array();
            foreach ($set_data as $key => $value) {
                $set_data_array[] = $key."='".$value."'";
            }
            
            foreach ($in_values as $key => $value) {
                $in_values[$key] = "'".$value."'";
            }
            
            $where_sql = "";
            $has_where = 0;
            if (count($between_params) != 0) {
                $where_sql .= $between_params['field']." between '".$between_params['start']."' and '".$between_params['end']."'";
                $has_where = 1;
            }
            
            if (count($in_values) != 0) {
                if ($has_where == 1) {
                    $where_sql .= " && ";
                }
                $where_sql .= $in_field." in (".implode(',', $in_values).")";        
                $has_where = 1;        
            }

            $field_array = array();
            foreach ($fields as $key => $value) {
                $field_array[] = $key."='".$value."'";
            }
            if (count($field_array) != 0) {
                if ($has_where == 1) {
                    $where_sql .= " && ";
                }
                $where_sql .= implode('&&', $field_array);
            }
            
            if ($where_sql != "") {
                $where_sql = "where ".$where_sql;
            }
            
            $sql = 'update '.$table.' set '.implode(',', $set_data_array).' '.$where_sql;
            
            return $sql;            
        }        
        
        /*组合成删除语句，where从句支持多个域$fields匹配*/
        function get_delete_sql($table, $fields)
        {
            $fieldArray = array();
            foreach ($fields as $key => $value) {
                $fieldArray[] = $key."='".$value."'";
            }
            
            return 'delete from '.$table.' where '.implode('&&', $fieldArray);
        }
        
        /*组合成删除语句，where子句支持一个域匹配*/
        function get_delete_one_sql($table, $field, $value)
        {
            return "delete from ".$table." where ".$field."='".$value."';";
        }
        
        /*数据库查询函数，将查询结果存放在二维数组中*/
        function query($sql)
        {         
            //$this->logger->Debug("Query database : ".$sql);
            $result = mysql_query($sql, $this->link);
            
            if ($result === false) {
                return false;
            }
            
            if (mysql_num_rows($result) <= 0) {
                //$this->logger->Debug("No data return : ".$sql);
                return null;
            }
            
            $data = array();
            while ($item = mysql_fetch_array($result)) {
                $data[] = $item;
            }
            
            //$this->logger->Debug("Query database successfully : ".$sql);
            //$this->logger->Debug("Query result : ".serialize($data));
            return $data;
        }

        /*数据库查询函数，查询结果为空或只有一条记录*/
        function query_row($sql)
        {          
            //$this->logger->Debug("Query database : ".$sql);
            $result = mysql_query($sql, $this->link);
            
            if ($result === false) {
                return false;
            }
            
            if (mysql_num_rows($result) <= 0) {
                //$this->logger->Debug("No data return : ".$sql);
                return null;   
            }
            
            //$this->logger->Debug("Query database successfully : ".$sql);
            $row = mysql_fetch_array($result);
            //$this->logger->Debug("Query result : ".serialize($row));
            return $row;
        }
        
        /*数据库查询函数，结果为某个域的一列记录*/
        function query_one_field($sql, $fieldName)
        {
            $result = $this->query($sql);
            $ret = array();
            
            if ($result === false || $result === null) {
                return $result;
            }
            
            if (!isset($result[0][$fieldName])) {
                //$this->logger->Debug("No field found : ".$fieldName);
                return null;
            }
            foreach ($result as $entry) {
                $ret[] = $entry[$fieldName];
            }
            return $ret;      
        }
        
        function query_last_insert_id()
        {
            $sql = "select LAST_INSERT_ID();";
            $result = $this->query_row($sql);
            
            if ($result === false) {
                return false;
            }
            
            return $result[0];
        }
          
        function query_with_transaction($insert_update_querys, $select_query, $candidate_update_query = "") {
            //$this->logger->Debug("Transaction begin.");
            mysql_query("BEGIN");
            $ret = true;
            foreach ($insert_update_querys as $key => $value) {
                //$this->logger->Debug("Query database : ".$value);
                $result = mysql_query($value, $this->link);
                if ($result === false) {
                    if (strpos($value, 'insert into') !== false && !empty($candidate_update_query)) {
                        //$this->logger->Debug("Query failed and query the candidate sql : ".$candidate_update_query);
                        //$this->logger->Debug("Query database : ".$candidate_update_query);
                        $result = mysql_query($candidate_update_query, $this->link);
                        if ($result !== false) {
                            //$this->logger->Debug("Query database successfully : ".$candidate_update_query);  
                            continue;
                        }
                    }
                    $ret = false;
                    break;
                }
                //$this->logger->Debug("Query database successfully : ".$value);
            }
            if ($ret === false) {
                //$this->logger->Debug("Transaction rollback : query failed.");
                mysql_query("ROLLBACK");
                return false;
            } 
            
            $result = $this->query($select_query);
            if ($result === false || $result === null) {
                //$this->logger->Debug("Transaction rollback : query failed.");
                mysql_query("ROLLBACK");
                return $result;
            } else if (!isset($result[0]['account']) || $result[0]['account'] < 0) {
                mysql_query("ROLLBACK");
                return null;                
            } else {
                //$this->logger->Debug("Transaction commit.");
                mysql_query("COMMIT");
                return $result;
            }
        }
        
        /*SQL语句执行函数，用于insert和update等不需要返回结果的语句*/
        function execute($sql)
        {          
            //$this->logger->Debug("Query database : ".$sql);
            $result = mysql_query($sql, $this->link);
            
            if ($result === false) {
                return false;
            }
            //$this->logger->Debug("Query database successfully : ".$sql);  
            return true;
        }
        
        /*与数据库断开连接*/
        function disconnect()
        {
            //$this->logger->Debug("Disconnected to database."); 
            if ($this->link != null) {
                return mysql_close($this->link);    
            }
        }       
    };
?>