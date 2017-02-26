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

/**
 * Input
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
	use OP_CORE;

	/**
	 * Build input tag.
	 *
	 * @param array $input
	 */
	static function Build($input, $session=null)
	{
		//	...
		$attr = [];
		foreach(['class','style'] as $key){
			if( $val = Escape(ifset($input[$key])) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		$type  = ifset($input['type']);
		$name  = ifset($input['name']);
		$value = ifset($input['value']);

		//	...
		if( $session ){
			$saved = $session;
		}else if( ifset($input['cookie']) ){
			$saved = Cookie::Get($name);
		}else{
			$saved = null;
		}

		//	...
		switch( $type ){
			case 'submit':
			case 'button':
				break;

			case 'radio':
			case 'checkbox':
				if( $value === $saved ){
					$attr[] = 'checked="checked"';
				}
				break;

			default:
				$value = $saved;
			break;
		}

		//	...
		if( $type === 'textarea' or $type === 'button' ){
			return sprintf('<%s name="%s" %s>%s</%s>', $type, $name, join(' ', $attr), $value, $type);
		}else{
			return sprintf('<input type="%s" name="%s" value="%s" %s />', $type, $name, $value, join(' ', $attr));
		}
	}
}