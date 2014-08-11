<?php

/**
  * Memcache缓存model
  */

class MC extends Memcached 
{
  /**
    * @var Memcached
    */
  static private $_m;

  /** 
    * @var servers
    * Memcached 服务器数组
    */
  static private $_servers = array();

  /** 
    * Memcache单例
    */
  public static function getInstance()
  {
    if (!self::$_m) {
      self::$_m = new self();

      // 设置memcached选项
      self::$_m->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
      self::$_m->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
      // 获取memcached配置信息 
      $dbConfig = Common::getConfig('config');
      $servers = $dbConfig['memcache'];
      
      foreach ($servers as $server) {
        array_push(self::$_servers, array_values($server));
      }
      
      self::$_m->addServers(self::$_servers);
            
      
      // 测试单台服务器
      // self::$_m->addServer('192.168.2.9', 11213);
    }
    return self::$_m;
  }

  /*
   ** 返回memcached服务器组信息
   */
  public function getServers()
  {
    return self::$_servers;
  }

  /* 
   ** memcached set操作
   */
  public function set($key, $value, $expire = NULL) {
    if (!is_int($expire)) {
      $expire = 86400;
    }
    parent::set($key, $value, $expire);
  }

  /* 
   ** 加入memcached队列操作
   */
  public function queue($queue_name, $key)
  {
    $queue_name = 'Queue::' . $queue_name;
    $seed_key = $queue_name . '_seed';
    $step_key = $queue_name . '_step';
    $step = parent::get($step_key);
    $i = parent::increment($seed_key);

    if ($step === false) {
      parent::set($step_key, $step_key.date('YmdHis'));
    }
    if ($i === false) {
      parent::set($seed_key, 0);
      $i = parent::increment($seed_key);
    }

    if ($step && $i) {
      $inc_key_i = $step . '_' . $i;
      parent::set($inc_key_i, $key, 86400);
    }
  }
  
  /** 
    * 获取memcached队列操作
    */
  public function getQueue($queue_name, $pc = false)
  {
    $queue_name = 'Queue::' . $queue_name;
    $step_key = $queue_name . '_step';
    $seed_key = $queue_name . '_seed';

    $lstep = parent::get($step_key);
    $count = parent::get($seed_key);

    $next_step_key = $queue_name . date('YmdHis');

    parent::set($step_key, $next_step_key);
    parent::set($seed_key ,0);
    $dataset = array();
    for ($i = 1; $i <= $count; $i++) {
      $val = parent::get($lstep . '_' . $i);

      if ($pc) {
        if (!in_array($val, $dataset)) 
          array_push($dataset, $val);
      } else {
        array_push($dataset, $val);
      }
    }
    return $dataset;
  }

}
?>
