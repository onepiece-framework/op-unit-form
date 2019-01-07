<?php
/**
 * unit-form:/Button.class.php
 *
 * @created   2018-01-16
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-01-16
 */
namespace OP\UNIT\FORM;

/** Button
 *
 * @created   2018-01-16
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Button
{
	//	...
	use \OP_CORE;

	/**
	 * Build input tag as type of checkbox.
	 *
	 * @param array $input
	 */
	static function Build($input)
	{
		//	...
		$values = [];

		//	...
		if( empty($input['values']) ){
			$values[] = ['value'=>$input['value']];
			$input['values'] = $values;
		}

		//	...
		$result = '';
		foreach( $input['values'] as $values ){
			$input['value'] = $values['value'];
			$result .= Input::Build($input);
		}

		//	...
		return $result;
	}
}
