<?php
/**
 * unit-form:/Input.class.php
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

/** Input
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Input
{
	//	...
	use \OP_CORE;

	/**
	 * Build input tag.
	 *
	 * @param array $input
	 */
	static function Build($input)
	{
		//	...
		$type  = ifset($input['type']);
		$name  = ifset($input['name']);
		$value = ifset($input['value']);

		//	...
		$attr = [];
		$keys = ['class','style','placeholder','disabled','readonly'];

		//	...
		foreach( $keys as $key ){
			//	...
			if( $val = $input[$key] ?? null ){
				//	...
				if( $key === 'disabled' or $key === 'readonly' ){
					$val  =  $key;
				}

				//	...
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		if( $type === 'textarea' or $type === 'button' ){
			return sprintf('<%s name="%s" %s>%s</%s>', $type, $name, join(' ', $attr), $value, $type);
		}else{
			return sprintf('<input type="%s" name="%s" value="%s" %s />', $type, $name, $value, join(' ', $attr));
		}
	}
}
