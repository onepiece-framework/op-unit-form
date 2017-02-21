<?php
/**
 * unit-form:/index.php
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
spl_autoload_register(function ($name){
	$name = ucfirst($name);
	$path = __DIR__."/{$name}.class.php";
	if( file_exists($path) ){
		include($path);
	}
});