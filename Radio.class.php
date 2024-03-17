<?php
/**
 * unit-form:/Radio.class.php
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Declare strict
 *
 */
declare(strict_types=1);

/** namespace
 *
 * @created   2017-12-18
 */
namespace OP\UNIT\FORM;

/** Radio
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Radio
{
	//	...
	use \OP\OP_CORE;

	/**
	 * Build input tag as type of radio.
	 *
	 * @param array  $input
	 * @param string $session
	 */
	static function Build($input)
	{
		//	...
		$attr = [];

		//	...
		foreach(['class','style'] as $key){
			if( $val = $input[$key] ?? null ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		$name = $input['name'];

		//	...
		foreach($input['option'] as $option){
			//	...
			$label = $option['label'];
			$value = $option['value'];
			$check = $option['check'] ?? null;

			//	Overwrite checked.
			if(($check !== null) and isset($input['value']) ){
				$check = ((string)$input['value'] === (string)$value) ? true: false;
			}

			//	...
			$checked = $check ? 'checked="checked"':'';

			//	...
			printf('<label><input type="radio" name="%s" value="%s" %s %s />%s</label>', $name, $value, join(' ', $attr), $checked, $label);
		}
	}
}
