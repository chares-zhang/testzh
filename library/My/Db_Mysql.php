<?php
/**
 * mysql 类，暂时不用了
 * @author chares
 *
 */
class Db
{
	protected $db;
	protected $dbname;

	public function __construct($config)
	{
		$this->dbname = $config['dbname'];
		if( $this->db = mysql_connect( "{$config['host']}:{$config['port']}" , $config['username'] , $config['password'] , true , MYSQL_CLIENT_INTERACTIVE ) )
		{
			mysql_query( "SET NAMES 'utf8'" );
			return mysql_select_db( $config['dbname'], $this->db );
		}
		else
		{
			die( 'Error Code 001' );
			return false;
		}
	}

	public function query($sql)
	{
		return mysql_query($sql,$this->db);
	}

	public function fetchArray($sql)
	{
		$result = $this->query($sql);
		if($result){
			return $this->res2Assoc($result);
		}
		return array();
	}

	public function fetchAssoc($result)
	{
		return mysql_fetch_assoc($result);
	}

	public function fetchRow($query)
	{
		$result = $this->query($query);
		return $this->fetchAssoc($result);
	}

	public function fetchObject($result)
	{
		return mysql_fetch_object($result);
	}

	public function affectedRows()
	{
		return mysql_affected_rows($this->db);
	}

	public function insertId()
	{
		return mysql_insert_id($this->db);
	}

	public function getCount($tables, $condition = "")
	{
		$r = $this->fetchRow("select count(*) as count from $tables " . ( $condition ? " where $condition" : ""));
		return $r['count'];
	}

	public function & res2Assoc(& $res)
	{
		$rows = array();
		while($row = $this->fetchAssoc($res))
		{
			$rows[] = $row;
		}
		return $rows;
	}
	
	public function replace($table,$data){
		$cols = $vals = array();
		foreach ($data as $col => $val) {
            $cols[] = '`'.$col.'`';
            $vals[] = "'".$this->_escape($val)."'";
        }
		$sql = "REPLACE INTO "
             . $table
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';
		try{
			$res = $this->query($sql);
		}catch(Exception $e){
			echo "replace table $table error:" . $e->getMessage();
			exit;
		}
		$result = $this->affectedRows();
		return $result;
	}

	public function insert($table,$data){
		$cols = $vals = array();
		foreach ($data as $col => $val) {
            $cols[] = '`'.$col.'`';
            $vals[] = "'".$this->_escape($val)."'";
        }
		$sql = "INSERT INTO "
             . $table
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';
		try{
			$res = $this->query($sql);
		}catch(Exception $e){
			echo "insert table $table error:" . $e->getMessage();
			exit;
		}
		$result = $this->affectedRows();
		$insertId = $this->insertId();
		return $insertId;
	}

	public function delete($table,$where){
		$sql = "DELETE FROM "
             . $table
             . (($where) ? " WHERE $where" : '');
		try{
			$res = $this->query($sql);
		}catch(Exception $e){
			echo "delete table $table error:" . $e->getMessage();
			exit;
		}
		$result = $this->affectedRows();
		return $result;
	}
	

	public function update($table, $data, $where){
		$cols = $vals = array();
		foreach ($data as $col => $val) {
			$set[] = "`" . $col . "` = " . "'".$this->_escape($val)."'";
        }
		$sql = "UPDATE "
             . $table
             . ' SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');
		try{
			$res = $this->query($sql);
		}catch(Exception $e){
			echo "update table $table error:" . $e->getMessage();
			exit;
		}
		return $res;
	}

	public function getCols($table)
	{
		$fields = mysql_list_fields($this->dbname, $table, $this->db);
		$count = mysql_num_fields($fields);
		$columns = array();
		for ($i = 0; $i < $count; $i++) {
			$columns[] = mysql_field_name($fields, $i);
		} 
		return $columns;
	}

	private function _escape($str){
		return mysql_escape_string($str);
	}

	public function startTransaction()
	{
		mysql_query( "SET AUTOCOMMIT=0" , $this->db );
		mysql_query( "START TRANSACTION" , $this->db );
	}

	public function commitTransaction()
	{
		mysql_query( "COMMIT" );
		mysql_query( "SET AUTOCOMMIT=1" , $this->db );
	}

	public function rollbackTransaction()
	{
		mysql_query( "ROLLBACK" );
	}

}
?>
