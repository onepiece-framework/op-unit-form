<?php
/**
 * unit-form:/Test.class.php
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-04-20
 */
namespace OP\UNIT\FORM;

/** Test
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Test
{
	//	...
	use \OP_CORE;

	/** Configuration test.
	 *
	 * @param  array $form
	 * @return boolean
	 */
	static function Config($form)
	{
		//	...
		$io = true;

		//	...
		$io = self::Form($form);

		//	...
		foreach( $form['input'] as $name => $input ){
			//	...
			if( gettype($name) !== 'string' ){
				self::Error("\$form[input] is array. (not assoc)\n Ex. \$form[input][input-name] = \$input;");
			}

			//	...
			if(!self::Input($input) ){
				$io = false;
			}
		}

		//	...
		return $io;
	}

	/** Form configuration test.
	 *
	 * @param  array $form
	 * @return boolean
	 */
	static function Form($form)
	{
		$io = true;

		//	...
		if(!$name = ifset($form['name'])){
			self::Error("\$form has not been set name attribute.");
			return;
		}

		//	...
		foreach(['action','method'] as $key){
			if(!isset($form[$key])){
				self::Error("\$form has not been set $key attribute. ($name)");
				$io = false;
			}
		}

		//	...
		return $io;
	}

	/** Input configuration test.
	 *
	 * @param  array $input
	 * @return boolean
	 */
	static function Input($input)
	{
		$io = true;

		//	...
		foreach(['type'] as $key){
			if(!isset($input[$key])){
				self::Error("Input config has not been set $key attribute. ($name)");
				$io = false;
			}
		}

		//	...
		return $io;
	}

	/** Get/Set Error.
	 *
	 * @param string $error
	 */
	static function Error( string $error=null )
	{
		//	...
		static $_error = [];

		//	...
		if( $error ){
			//	...
			$_error[] = $error;
		}else{
			//	...
			return $_error;
		}
	}
}