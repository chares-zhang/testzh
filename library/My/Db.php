<?php

class Db {
	protected $_connection;
	protected $_fetchMode = PDO::FETCH_ASSOC;
	protected $_error;
	
	public function __construct($config) {
		$dsn = 'mysql:host=' . $config['host'] . '; dbname=' . $config['dbname'];
		if (empty($this->_connection)) {
			try {
				$this->_connection = new PDO(
						$dsn, $config['username'], $config['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
				);
				$this->_connection->query("SET NAMES utf8");
			} catch (PDOException $e) {
				throw new Exception('pdo connection failed.' . var_export($e, true));
				die($e->getMessage()); //Error
			}
		}
		return $this->_connection;
	}
	
	public function query($sql,$bind = array()) {
		$sql = trim($sql);
		if (preg_match("#^(update|delete)#is", $sql)) {
			if (stripos($sql,"where") === false) {
				throw new Exception("UPDATE_OR_DELETE_WHERE_IS_EMPTY");
			}
		}
		 
		$stmt = $this->_connection->prepare($sql);
		if (!empty($bind)) {
			$sqlCount = substr_count($sql,"?");
			$bindCount = count($bind);
			if ($sqlCount != $bindCount) {
				throw new Exception("query param number error:\n; [#SQL#]:" . var_export($sql, true).";bind:".var_export($bind,true));
			}
			foreach($bind as $key=>$value){
				$i = $key+1;
				$paramType = $this->paramType($value);
				$stmt -> bindValue($i,$value,$paramType);
			}
		}
		$res = $stmt->execute();
		if ($res === false) {
			$this->_error = $stmt->errorInfo();
			throw new Exception('stmt execute error:' . var_export($stmt->errorInfo(),true) . "\n" . '; [#SQL#]:' . var_export($sql, true).";bind:".var_export($bind,true));
		}
	
		return $stmt;
	}
	
	public function paramType($value)
	{
		if (is_int($value)) {
			return PDO::PARAM_INT;
		}
		if (is_bool($value)) {
			return PDO::PARAM_BOOL;
		}
		if (is_null($value)) {
			return PDO::PARAM_NULL;
		}
		return PDO::PARAM_STR;
	}
	
	public function getError()
	{
		return $this->_error;
	}
	
	public function fetchAll($sql,$bind=array()) {
		$stmt = $this->query($sql,$bind);
		$result = $stmt->fetchAll($this->_fetchMode);
		return $result;
	}
	
	public function fetchRow($sql,$bind=array()) {
		$stmt = $this->query($sql,$bind);
		$result = $stmt->fetch($this->_fetchMode);
		return $result;
	}
	
	public function lastInsertId() {
		return $this->_connection->lastInsertId();
	}
	
	public function insert($table, $data) {
		$cols = $vals = array();
		foreach ($data as $col => $val) {
			$cols[] = '`' . $col . '`';
			$vals[] = "?";
			if(is_int($val)){
				$val = (string)$val;
			}
			$bind[] = $val;
		}
		$sql = "INSERT INTO "
				. $table
				. ' (' . implode(', ', $cols) . ') '
						. 'VALUES (' . implode(', ', $vals) . ')';
		try {
			$stmt = $this->query($sql,$bind);
		} catch (PDOException $e) {
			throw new Exception('pdo error.' . var_export($e, true));
			echo $e->getMessage(); 
			exit;
		}
	
		return $this->_connection->lastInsertId();
	}
	
	public function update($table, $data, $where, $bind=array()) {
		$cols = $vals = array();
		foreach ($data as $col => $val) {
			//$set[] = "`" . $col . "` = " . "'".$val."'";
			$set[] = "`{$col}` = ?";
			$setBind[] = $val;
		}
		$bind = array_merge($setBind,$bind);
		$sql = "UPDATE "
				. $table
				. ' SET ' . implode(', ', $set)
				. (($where) ? " WHERE $where" : '');
		try {
			$stmt = $this->query($sql,$bind);
		} catch (PDOException $e) {
			throw new Exception('pdo error.' . var_export($e, true));
		}
		$result = $stmt->rowCount();
		return $result;
	}
	
	public function delete($table, $where, $bind=array()) {
		$sql = "DELETE FROM "
				. $table
				. (($where) ? " WHERE $where" : '');
		try {
			$stmt = $this->query($sql,$bind);
		} catch (PDOException $e) {
			throw new Exception('pdo error.' . var_export($e, true));
			echo $e->getMessage();
			exit;
		}
		$result = $stmt->rowCount();
		return $result;
	}
	
	public function replace($table, $data) {
		$cols = $vals = array();
		foreach ($data as $col => $val) {
			$set[] = "`{$col}` = ?";
			if(is_int($val)){
				$val = (string)$val;
			}
			$bind[] = $val;
		}
		$sql = "REPLACE INTO "
				. $table
				. ' SET ' . implode(', ', $set);
		try {
			$stmt = $this->query($sql,$bind);
		} catch (PDOException $e) {
			throw new Exception('pdo error.' . var_export($e, true));
			echo $e->getMessage();
			exit;
		}
		$result = $stmt->rowCount();
		return $result;
	}
	
	public function quote($data) {
		return $this->_connection->quote($data);
	}

}
