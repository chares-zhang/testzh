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

    public function query($sql) {
        $stmt = $this->_connection->prepare($sql);
        $res = $stmt->execute();
        if ($res === false) {
        	$this->_error = $stmt->errorInfo();
            throw new Exception('[stmt execute error]:' . var_export($stmt->errorInfo(),true) . "\n" . '; [#SQL#]:' . var_export($sql, true));
        }
        return $stmt;
    }
    
    public function getError()
    {
    	return $this->_error;
    }

    public function fetchAll($sql) {
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll($this->_fetchMode);
        return $result;
    }

    public function fetchRow($sql) {
        $stmt = $this->query($sql);
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
            $vals[] = $this->quote($val);
        }
        $sql = "INSERT INTO "
                . $table
                . ' (' . implode(', ', $cols) . ') '
                . 'VALUES (' . implode(', ', $vals) . ')';
        try {
            $stmt = $this->query($sql);
        } catch (PDOException $e) {
            throw new Exception('pdo error.' . var_export($e, true));
            echo $e->getMessage(); //Error
            exit;
        }

        return $this->_connection->lastInsertId();
    }

    public function update($table, $data, $where) {
        $cols = $vals = array();
        foreach ($data as $col => $val) {
            $set[] = "`{$col}` = {$this->quote($val)}";
        }

        $sql = "UPDATE "
                . $table
                . ' SET ' . implode(', ', $set)
                . (($where) ? " WHERE $where" : '');
        try {
            $stmt = $this->query($sql);
        } catch (PDOException $e) {
            throw new Exception('pdo error.' . var_export($e, true));
        }
        $result = $stmt->rowCount();
        return $result;
    }

    public function delete($table, $where) {
        $sql = "DELETE FROM "
                . $table
                . (($where) ? " WHERE $where" : '');
        try {
            $stmt = $this->query($sql);
        } catch (PDOException $e) {
            throw new Exception('pdo error.' . var_export($e, true));
            echo $e->getMessage(); //Error
            exit;
        }
        $result = $stmt->rowCount();
        return $result;
    }

    public function replace($table, $data) {
        $cols = $vals = array();
        foreach ($data as $col => $val) {
            $set[] = "`" . $col . "` = " . $this->quote($val);
        }
        $sql = "REPLACE INTO "
                . $table
                . ' SET ' . implode(', ', $set);
        try {
            $stmt = $this->query($sql);
        } catch (PDOException $e) {
            throw new Exception('pdo error.' . var_export($e, true));
            echo $e->getMessage(); //Error
            exit;
        }
        $result = $stmt->rowCount();
        return $result;
    }

    public function quote($data) {
        return $this->_connection->quote($data);
    }

}
