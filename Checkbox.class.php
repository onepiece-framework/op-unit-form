<?php
/**
 * unit-form:/Checkbox.class.php
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2017-12-18
 */
namespace OP\UNIT\FORM;

/** Checkbox
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Checkbox
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
		$attr = [];

		//	...
		foreach(['class','style'] as $key){
			if( $val = ifset($input[$key]) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		$name = $input['name'];

		//	...
		printf('<input type="hidden" name="%s[0]" value="" />', $name);

		//	...
		$i = 1;
		foreach($input['option'] as $option){
			//	...
			$label = $option['label'];
			$value = $option['value'];
			$check = $option['check'];

			//	...
			if( ifset($input['value']) ){
				$check = array_search($value, $input['value']);
			}

			//	...
			$checked = $check ? 'checked="checked"':'';

			//	...
			printf('<label><input type="checkbox" name="%s[%s]" value="%s" %s %s />%s</label>', $name, $i, $value, join(' ', $attr), $checked, $label);

			//	...
			$i++;
		}
	}
}
