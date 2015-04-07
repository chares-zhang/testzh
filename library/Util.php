<?php
class Util
{
    static private $_utilM;
   
	static public function factory($utilName){
		if (empty(self::$_utilM)) {
			$utilClassName = 'Util_'.ucfirst($utilName);
			self::$_utilM = new $utilClassName();
		}
		return self::$_utilM;
	}

}
