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

/**
 * Radio
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
	use OP_CORE;

	/**
	 * Build input tag as type of radio.
	 *
	 * @param array  $input
	 * @param string $session
	 */
	static function Build($input, $session=null)
	{
		//	...
		foreach(['name','class','style'] as $key){
			if( $val = Escape(ifset($input[$key])) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		if( $session !== null ){
			$selected = $session;
		}else{
			$selected = (string)ifset($input['value']);
		}

		//	...
		if( empty($input['values']) ){
			$input['values'][0]['value'] = ifset($input['value']);
			$input['values'][0]['label'] = ifset($input['label']);
		}

		//	...
		foreach($input['values'] as $values){
			//	...
			if( is_array($values) ){
				$value = ifset($values['value']);
				$label = ifset($values['label'], $value);
			}else if( is_string($values) or is_numeric($values) ){
				$value = $values;
				$label = $values;
			}

			//	...
			$checked = $value === $selected ? 'checked="checked"':'';

			//	...
			printf('<label><input type="radio" value="%s" %s %s />%s</label>', $value, join(' ', $attr), $checked, $label);
		}
	}
}