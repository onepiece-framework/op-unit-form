<?php
/**
 * unit-form:/autoloader.php
 *
 * @created   2018-01-12
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Declare strict
 *
 */
declare(strict_types=1);

//	...
spl_autoload_register( function($name){
	//	...
	$namespace = 'OP\UNIT\FORM\\';

	//	...
	if( $name === 'OP\UNIT\Form' ){
		$name  =  'Form';
	}else
		if( strpos($name, $namespace) === 0 ){
			$name = substr($name, strlen($namespace));
	}else{
		return;
	}

	//	...
	$path = __DIR__."/{$name}.class.php";

	//	...
	if( file_exists($path) ){
		try{
			include($path);
		}catch( Throwable $e ){
			OP\Notice::Set($e);
		};
	}else{
		OP\Notice::Set("Does not exists this file. ($path)");
	};
});
