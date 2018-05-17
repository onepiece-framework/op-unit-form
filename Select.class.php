<?php
/**
 * unit-form:/Select.class.php
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

/** Select
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Select
{
	//	trait
	use \OP_CORE;

	/** Build select tag.
	 *
	 * @param array $input
	 */
	static function Build($input)
	{
		//	...
		$attr = [];
		$name = $input['name'];

		//	...
		foreach(['type','class','style'] as $key){
			if( $val = ifset($input[$key]) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		if( $is_multiple = $input['multiple'] ?? false ){
			$attr[] = 'multiple="multiple"';
			$attr[] = sprintf('name="%s[]"', $name);
		}else{
			$attr[] = sprintf('name="%s"', $name);
		}

		//	...
		$multiple = $is_multiple ? '<input type="hidden" name="'.$name.'[]" value=""/>'.PHP_EOL: null;

		//	...
		$attr = join(' ', $attr);

		//	...
		$options = '';

		//	...
		foreach( $input['values'] as $values ){
			//	...
			$label = $values['label'];
			$value = $values['value'];
			$check = $values['check'];

			//	...
			if( isset($input['value']) ){
				if( $is_multiple ){
					$check = in_array((string)$value, $input['value'], true);
				}else{
					$check = (string)$input['value'] === (string)$value ? true: false;
				}
			}

			//	...
			$selected = $check ? 'selected="selected"':'';

			//	...
			$options .= sprintf('<option value="%s" %s>%s</option>', $value, $selected, $label);
		}

		return "$multiple<select $attr>$options</select>";
	}
}
