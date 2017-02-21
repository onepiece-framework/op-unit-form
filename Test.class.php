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

	/**
	 * Configuration test.
	 *
	 * @param array $form
	 */
	static function Config($form)
	{
		//	...
		self::Form($form);

		//	...
		foreach($form['input'] as $name => $input){
			if( gettype($name) !== 'string' ){
				Html::P("Has not been set input name.\n Ex. \$form[input][input-name] = \$input;");
			}
			self::Input($input);
		}
	}

	/**
	 * Form configuration test.
	 *
	 * @param array $form
	 */
	static function Form($form)
	{
		//	...
		if(!$name = ifset($form['name'])){
			Html::P("Form has not been set name attribute.");
			return;
		}

		//	...
		foreach(['action','method'] as $key){
			if(!isset($form[$key])){
				Html::P("Form config has not been set $key attribute. ($name)");
			}
		}
	}

	/**
	 * Input configuration test.
	 */
	static function Input($input)
	{
		//	...
		if(!$name = ifset($input['name'])){
			Html::P("Has not been set name attribute.");
			D($input);
			return;
		}

		//	...
		foreach(['type'] as $key){
			if(!isset($input[$key])){
				Html::P("Input config has not been set $key attribute. ($name)");
			}
		}
	}
}
