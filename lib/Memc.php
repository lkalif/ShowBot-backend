<?php
/**
 * Memcached initialization
 * 
 * @package ShowBot-backend
 * @author Latif Khalifa <latifer@streamgrid.net>
 * @copyright Copyright(c) 2014, Latif Khalifa
 * @license http://opensource.org/licenses/MIT
 */
class Memc
{
    static $daemon;
    static $active = false;
    static $expire = 7200;
	
    static function init()
    {
		if (!class_exists("Memcached", false)) return;
		
        self::$daemon = new Memcached("sb");
		$servers = self::$daemon->getServerList();
		if (empty($servers))
		{
			//This code block will only execute if we are setting up a new EG(persistent_list) entry
			self::$daemon->setOption(Memcached::OPT_RECV_TIMEOUT, 1000);
			self::$daemon->setOption(Memcached::OPT_SEND_TIMEOUT, 3000);
			self::$daemon->setOption(Memcached::OPT_TCP_NODELAY, true);
			self::$daemon->setOption(Memcached::OPT_PREFIX_KEY, "sb_");
			self::$daemon->addServer("localhost", 11211);
		}
		
		self::$active = true;
	}
	
	static function getq($q)
	{
		if (!self::$active) return false;

		$key = md5($q);
		return self::$daemon->get($key);
	}

	static function setq($q, $data)
	{
		if (!self::$active) return false;

		$key = md5($q);
		return self::$daemon->set($key, $data, self::$expire);
	}
	
	static function flush()
	{
		if (!self::$active) return false;
		self::$daemon->flush();
	}

}
?>
