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

/**
 * Test
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
	use OP_CORE;

	/** Configuration test.
	 *
	 * @param  array $form
	 * @return boolean
	 */
	static function Config($form)
	{
		$io = null;

		//	...
		$io = self::Form($form) ? $io: false;

		//	...
		foreach( $form['input'] as $name => $input ){
			//	...
			if( gettype($name) !== 'string' ){
				Html::P("\$form[input] is array. (not assoc)\n Ex. \$form[input][input-name] = \$input;");
			}

			//	...
			$io = self::Input($input) ? $io: false;
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
			Html::P("\$form has not been set name attribute.");
			return;
		}

		//	...
		foreach(['action','method'] as $key){
			if(!isset($form[$key])){
				Html::P("\$form has not been set $key attribute. ($name)");
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
		if(!$name = ifset($input['name'])){
			Html::P("Has not been set name attribute.");
			D($input);
			return false;
		}

		//	...
		foreach(['type'] as $key){
			if(!isset($input[$key])){
				Html::P("Input config has not been set $key attribute. ($name)");
				$io = false;
			}
		}

		//	...
		return $io;
	}
}