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
		$failed = false;

		//	...
		if(!self::Form($form) ){
			$failed = true;
		}

		if(!self::Inputs($form) ){
			$failed = true;
		}

		//	...
		return !$failed;
	}

	/** Form configuration test.
	 *
	 * @param  array $form
	 * @return boolean
	 */
	static function Form($form)
	{
		//	...
		$failed = false;

		//	...
		if(!$name = $form['name'] ?? null ){
			self::Error("\$form has not been set name attribute.");
			return;
		}

		//	...
		foreach([/*'action','method'*/] as $key){
			if(!isset($form[$key])){
				self::Error("\$form has not been set $key attribute. ($name)");
				$failed = true;
			}
		}

		//	...
		return !$failed;
	}

	/** Inputs configuration test.
	 *
	 * @param unknown $form
	 */
	static function Inputs($form)
	{
		//	...
		$failed = false;

		//	...
		foreach( $form['input'] ?? [] as $name => $input ){
			//	...
			if( gettype($name) !== 'string' ){
				self::Error("\$form[input] is array. (not assoc)\n Ex. \$form[input][input-name] = \$input;");
			}

			//	...
			if(!self::Input($input) ){
				$failed = true;
			}
		}

		//	...
		return !$failed;
	}

	/** Input configuration test.
	 *
	 * @param  array $input
	 * @return boolean
	 */
	static function Input($input)
	{
		//	...
		$failed = false;

		//	...
		foreach(['type'] as $key){
			if(!isset($input[$key])){
				self::Error("Input config has not been set $key attribute. ({$input['name']})");
				$failed = true;
			}
		}

		//	...
		return !$failed;
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
			$_error[Hasha1($error)] = $error;
		}else{
			//	...
			return $_error;
		}
	}
}
