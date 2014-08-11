<?php

/** 
  * 转换特殊字符为HTML代码
  */
function doHtmlspecialchars()
{
	$_GET     = htmlspecialcharsDeep($_GET);
	$_POST    = htmlspecialcharsDeep($_POST);
	$_COOKIE  = htmlspecialcharsDeep($_COOKIE);
	$_REQUEST = htmlspecialcharsDeep($_REQUEST);
}

/** 
  * 递归转换特殊字符为HTML代码
  */

function htmlspecialcharsDeep($value) 
{
	$value = is_array($value) ? array_map('htmlspecialcharsDeep', $value) : htmlspecialchars($value, ENT_QUOTES);
	return $value;
}


/** 
  * 去除多余的转义字符
  */
function doStripslashes() 
{
	if (get_magic_quotes_gpc()){
		$_GET     = stripslashesDeep($_GET);
		$_POST    = stripslashesDeep($_POST);
		$_COOKIE  = stripslashesDeep($_COOKIE);
		$_REQUEST = stripslashesDeep($_REQUEST);
	}
}

/**
 * 递归去除转义字符
 */
function stripslashesDeep($value){
	$value = is_array($value) ? array_map('stripslashesDeep', $value) : stripslashes($value);
	return $value;
}

/** 
  * 自动加载类
  */
function myAutoload($className) 
{
	if(file_exists(LIBRARY_PATH . '/'. $className . '.php')){
		include LIBRARY_PATH . '/'. $className . '.php';
	}else{
		$classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $className)));
		include $classFile . '.php';
	} 
}

function myErrorHandler($errno, $errstr, $errfile, $errline){
//	var_dump($errno,error_reporting());exit;
    $errno = $errno & error_reporting();
//	var_dump($errno);

	if ($errno == 0) {
        return false;
    }
    if (!defined('E_STRICT')) {
        define('E_STRICT', 2048);
    }
    if (!defined('E_RECOVERABLE_ERROR')) {
        define('E_RECOVERABLE_ERROR', 4096);
    }
    if (!defined('E_DEPRECATED')) {
        define('E_DEPRECATED', 8192);
    }

    $errorMessage = '';

    switch($errno){
        case E_ERROR:
            $errorMessage .= "Error";
            break;
        case E_WARNING:
            $errorMessage .= "Warning";
            break;
        case E_PARSE:
            $errorMessage .= "Parse Error";
            break;
        case E_NOTICE:
            $errorMessage .= "Notice";
            break;
        case E_CORE_ERROR:
            $errorMessage .= "Core Error";
            break;
        case E_CORE_WARNING:
            $errorMessage .= "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errorMessage .= "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errorMessage .= "Compile Warning";
            break;
        case E_USER_ERROR:
            $errorMessage .= "User Error";
            break;
        case E_USER_WARNING:
            $errorMessage .= "User Warning";
            break;
        case E_USER_NOTICE:
            $errorMessage .= "User Notice";
            break;
        case E_STRICT:
            $errorMessage .= "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errorMessage .= "Recoverable Error";
            break;
        case E_DEPRECATED:
            $errorMessage .= "Deprecated functionality";
            break;
        default:
            $errorMessage .= "Unknown error ($errno)";
            break;
    }
    $errorMessage .= ": {$errstr}  in {$errfile} on line {$errline}";
    throw new Exception($errorMessage);

}