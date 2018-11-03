<?php
/**
 * Database Connector
 *
 * @author Azfar Ahmed
 * @version 1.0
 * @date November 02, 2015
 * @EasyPhp MVC Framework
 * @website www.tutbuzz.com
 */
 
class DBconfig
{
    var $connection;
	
	public function __construct()
	{
		$this->hostname = $GLOBALS['ep_hostname'];
		$this->username = $GLOBALS['ep_username']; 
		$this->password = $GLOBALS['ep_password'];
		$this->database = $GLOBALS['ep_database'];
		
		$connection = new PDO("sqlsrv:server=tcp:merrona.database.windows.net,1433; Database = MerronaDB", "meron", "Merrona123");
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

    function connectToDatabase()
    {
		//$connection = new mysqli($this->hostname,$this->username,$this->password,$this->database);
		
		$connectionInfo = array("UID" => "meron@merrona", "pwd" => "Merrona123", "Database" => "MerronaDB", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
		$serverName = "tcp:merrona.database.windows.net,1433";
		$connection = sqlsrv_connect($serverName, $connectionInfo);
			
        if($connection->connect_errno > 0)
        {
            die ("<div style='background: red; color: yellow; padding: 20px;'>Cannot connect to the database, please check database settings.</div>");
        }

        else
        {
            $this->connection = $connection;
        }

        return $this->connection;

    }

}


