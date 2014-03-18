<?php
    class DataBase{

        private static $db_name = 'comments';
        private static $db_host = 'localhost';
        private static $db_user = 'root';
        private static $db_pass = '';

        public function connect(){
            $connection = mysql_connect(self::$db_host, self::$db_user, self::$db_pass);
            mysql_select_db(self::$db_name);
            return $connection;
        }
    }
    $db = new DataBase();
    $db->connect();
?>