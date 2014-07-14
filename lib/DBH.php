<?php
class DBH
{
	public
	$db_name,
	$db_pass,
	$db_user,
	$db_host,
	$dbh,
	$last_error = "";
	
	// instance
	static $db = null;

	function log($line)
	{
		return;
		static $f = false;
		static $failed = false;

		if ($failed) {
			return false;
		}

		if (!$f) {
			$f = @fopen(SITE_ROOT.'/lib/logs/sql.log', 'a');
		}

		if (!$f) {
			$failed = true;
			return false;
		}

		@fwrite($f, "[".date('Y-m-d H:i')."] ".$line."\n");
	}
	
	static function getInstance()
	{
		if (self::$db === null)
		{
		    self::$db = new DBH;
		}
		
		return self::$db;
	}

	function connect($db_name, $db_host, $db_user, $db_pass)
	{
		$this->db_name = $db_name;
		$this->db_pass = $db_pass;
		$this->db_user = $db_user;
		$this->db_host = $db_host;

		$this->dbh = @mysql_pconnect($db_host, $db_user, $db_pass);

		if (!$this->dbh) {
			DBH::log("[error] connection to database failed");
			DBH::log("[error] connection string used '$conn_str'");
			return false;
		} 

		if (!mysql_select_db($db_name)) {
			DBH::log("[error] database {$db_name} dose not exist");
			return false;
		}


		//$this->query("SET SQL_MODE='TRADITIONAL'");
		$this->query("SET NAMES 'utf8'");
		return true;

	}

	function query($q)
	{
		$res = @mysql_query($q, $this->dbh);

		if (!$res) {
			DBH::log("[error] ".$q);
			DBH::log("[error_msg] " . mysql_error($this->dbh));
			$this->last_error = mysql_error($this->dbh);

			$e = debug_backtrace();
			$c = count($e);
			$btr = "";

			for ($i=0; $i<$c; $i++) {
				$btr .= "{$e[$i]['class']}::{$e[$i]['function']} {$e[$i]['file']}({$e[$i]['line']})\n";
			}

			DBH::log("[backtrace]\n".$btr);

			return false;
		} else {
			if ($res !== TRUE) {
				$result_id = (int)$res;
				if (!isset($this->field_desc[$result_id])) {
					$nf = mysql_num_fields($res);
					for ($i=0; $i<$nf; $i++) {
						$this->field_desc[$result_id][mysql_field_name($res, $i)] = mysql_field_type($res, $i);
					}
				}
			}
			DBH::log("[success] ".$q);
			return $res;
		}
	}

	function loadFromDbRow(&$obj, $res, $row)
	{
		foreach ($row as $symbolicName => $nativeName){
			if ($nativeName && ($this->field_desc[(int)$res][$symbolicName] == "timestamp" ||
			    $this->field_desc[(int)$res][$symbolicName] == "date" ||
                $this->field_desc[(int)$res][$symbolicName] == "datetime")) {
				$obj->{$symbolicName} = strtotime($nativeName);
			} else {
				$obj->{$symbolicName} = $nativeName;
			}
		}
		return true;
	}

    function insertID()
    {
		return @mysql_insert_id($this->dbh);
    }
    
	function numRows($res)
	{
		return @mysql_num_rows($res);
	}

	function affectedRows()
	{
        return @mysql_affected_rows($this->dbh);
	}

	function fieldName($res, $num)
	{
		return @mysql_field_name($res, $num);
	}

	function numFields($res)
	{
		return @mysql_num_fields($res);
	}

	function fetchRow($res)
	{
		return @mysql_fetch_assoc($res);
	}

	function begin()
	{
		return $this->query('begin');
	}

	function rollback()
	{
		return $this->query('rollback');
	}

	function commit()
	{
		return $this->query('commit');
	}

	/* FIXME: port from postgres */
	function nextId($seq)
	{
		$res = $this->query("select nextval('$seq') as n");

		if (!$res || !$row = $this->fetchRow($res)) {
			return false;
		} else {
			return (int)$row['n'];
		}
	}

	/* Date time conversion */
	function db2unix($s)
	{
		return strtotime($s);
	}

}
/*
* Local variables:
* tab-width: 4
* c-basic-offset: 4
* End:
* vim600: sw=4 ts=4 fdm=marker
* vim<600: sw=4 ts=4
*/
?>