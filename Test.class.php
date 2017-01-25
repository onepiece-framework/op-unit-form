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
class Test extends OnePiece
{
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
		foreach($form['input'] as $input){
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
			D("Form has not been set name attribute.");
			return;
		}

		//	...
		foreach(['action','method'] as $key){
			if(!isset($form[$key])){
				D("Form config has not been set $key attribute. ($name)");
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
			D("Has not been set name attribute.");
			D($input);
			return;
		}

		//	...
		foreach(['type','value','label'] as $key){
			if(!isset($input[$key])){
				D("Input config has not been set $key attribute. ($name)");
			}
		}
	}
}
