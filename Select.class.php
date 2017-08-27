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
	use OP_CORE;

	/** Build select tag.
	 *
	 * @param array $input
	 */
	static function Build($input, $session)
	{
		//	...
		foreach(['type','name','class','style','value'] as $key){
			if( $val = ifset($input[$key]) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		$attr = join(' ', $attr);

		//	...
		$options = '';
		if( isset($input['option']) ){
			foreach($input['option'] as $option){
				//	...
				if( is_array($option) ){
					$value = ifset($option['value']);
					$label = ifset($option['label'], $value);
				}else if( is_string($option) or is_numeric($option) ){
					$value = $option;
					$label = $value;
				}

				//	...
				$selected = $session === (string)$value ? 'selected="selected"':'';

				//	...
				$options .= sprintf('<option value="%s" %s>%s</option>', $value, $selected, $label);
			}
		}

		return "<select $attr>$options</select>";
	}
}

/** Option
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Option
{
	//	...
	use OP_CORE;

	/**
	 * Build option tag.
	 *
	 * @param array $input
	 */
	static function Build($option)
	{
		if( is_array($option) ){
			$value = ifset($option['value']);
			$label = ifset($option['label'], $value);
		}else if( is_string($option) or is_numeric($option) ){
			$value = $option;
			$label = $value;
		}

		//	...
		printf('<select %s>%s</select>', join(' ', $attr), $options);
	}
}
