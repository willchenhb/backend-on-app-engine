<?php
    class Util{
        private static $logger;
        
        public static function initialize() {
            global $logger;
            self::$logger = $logger;
        } 
        
        public static function check_imei($imei) {
		
          /*if (preg_match("/\d{15,15}/", $imei)) {
                return true;
            } else{
                return false;
	    }*/
            return true; // some of device can not get the imei, will return another id, so abandon the input check
        }
        
        public static function check_integer($num) {
            if (preg_match("/\d+/", $num)) {
                return true;
            } else{
                return false;
            }
        }
        
        public static function check_double($num) {
            if (preg_match("/^(-?\d+)(\.\d+)?$/", $num)) {
                return true;
            } else{
                return false;
            }
        }
        
        public static function check_register_input($params) {
            //self::$logger->Debug("Check register.php input.");
            
            if (!isset($params['imei']) || !Util::check_imei($params['imei'])) {
                return false;
            }
            
            return true;
        }
        
        public static function check_get_input($params) {
            //self::$logger->Debug("Check get.php input.");
            
            if (!isset($params['userid']) || !isset($params['imei'])) {
                return false;
            }
            if (!Util::check_imei($params['imei']) || !Util::check_integer($params['userid'])) {
                return false;
            }

            return true;
        }
        
        public static function check_add_input($params) {
            //self::$logger->Debug("Check add.php input.");
            
            if (!isset($params['userid']) || !isset($params['imei']) || !isset($params['traffic']) || !isset($params['duration'])) {
                return false;
            }
            if (Util::check_imei($params['imei']) == false) {
                return false;
            }
            if (!Util::check_integer($params['userid']) || !Util::check_double($params['traffic']) || !Util::check_double($params['duration'])) {
                return false;
            }
            return true;
        }
        
        public static function compute_account_added($traffic, $duration) {
            //self::$logger->Debug("Compute account added.");
            
            return 1.1;
        }
        
        public static function check_reduce_input($params) {
            //self::$logger->Debug("Check reduce.php input.");
            
            if (!isset($params['userid']) || !isset($params['imei']) || !isset($params['type']) || !isset($params['qq']) || !isset($params['qb'])) {
                return false;
            }
            if (Util::check_imei($params['imei']) == false) {
                return false;
            }
            if (!Util::check_integer($params['userid']) || !Util::check_integer($params['qq']) || !Util::check_double($params['qb'])) {
                return false;
            }
            return true;            
        }
        
        public static function compute_account_reduced($type, $qq, $qb) {
            //self::$logger->Debug("Compute account reduced."); 
            
            return -1.1;
        }
        
        public static function check_getadid_input($params) {
            //self::$logger->Debug("Check getadid.php input.");
            
            if (!isset($params['userid']) || !Util::check_integer($params['userid'])) {
                return false;
            }
            
            return true;            
        }
        
        public static function compute_ad_id($params) {
            //self::$logger->Debug("Compute ad id."); 
            $ad_id = 1;
            
            return $ad_id;
        }
        
        public static function check_getpaylist_input($params) {
            //self::$logger->Debug("Check getpaylist.php input.");
            
            if (!isset($params['startid']) || !isset($params['endid'])) {
                return false;
            }
            
            if (!Util::check_integer($params['startid']) || !Util::check_integer($params['endid'])) {
                return false;
            }
            
            if ($params['startid'] > $params['endid']) {
                return false;
            }
            
            return true;      
        }
        
        public static function check_postpayedlist_input($params) {
            //self::$logger->Debug("Check postpayedlist.php input.");
            
            if (!isset($params['idlist']) || !Util::check_integer($params['idlist'])) {
                return false;
            }
            
            return true;
        }
    };
    
    Util::initialize();
?>
