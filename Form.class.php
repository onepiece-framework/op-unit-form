<?php
/**
 * unit-form:/Form.class.php
 *
 * v1.0 SecureForm
 * v2.0 onepiece-framework
 * v3.0 Unit Gen1 2017
 * v3.1 Unit Gen2 2018
 *
 * @created   2017-01-25
 * @version   3.1
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-01-22
 */
namespace OP\UNIT;

/** Used class
 *
 */
use Exception;
use OP\OP_CORE;
use OP\OP_UNIT;
use OP\OP_SESSION;
use OP\OP_DEBUG;
use OP\IF_UNIT;
use OP\IF_FORM;
use OP\Env;
use OP\Notice;
use OP\Cookie;
use function OP\CompressPath;

/** Form
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Form implements IF_FORM, IF_UNIT
{
	/** Trait
	 *
	 */
	use OP_CORE, OP_UNIT, OP_SESSION, OP_DEBUG;

	/** Form configuration.
	 *
	 * @var array
	 */
	private $_form;

	/** Error of validation.
	 *
	 * @var array
	 */
	private $_errors;

	/** Session
	 *
	 */
	private $_session;

	/** Request
	 *
	 */
	private $_request;

	/** Validate
	 *
	 */
	private $_validate;

	/** Start method was called flag.
	 *
	 * @var boolean
	 */
	private $_is_start;

	/** Is token validation.
	 *
	 * @var boolean
	 */
	private $_is_token;

	/** Construct
	 *
	 */
	function __construct()
	{
		/*
		//	Recovery session is do _InitForm().
		$this->_session = $this->Session($form_name);
		*/
	}

	/** Destruct
	 *
	 */
	function __destruct()
	{
		//	Save to session.
		if( $name = $this->_form['name'] ?? null ){
			$this->Session($name, $this->_session);
		}
	}

	/** Initialize form config.
	 *
	 * @param	 string|array	 $form
	 * @throws	 Exception		 $e
	 * @return	 boolean		 $io
	 */
	private function _InitForm($form)
	{
		//	...
		if( is_string($form) ){
			//	...
			if(!file_exists($form) ){
				throw new Exception("Does not found this file. ($form)");
			}

			//	...
			$form = include($form);
		};

		//	...
		if( $this->_form ){
			throw new Exception("This form config is already initialized. (form name: {$this->_form['name']})");
		}

		//	...
		if(!$form_name = $form['name'] ?? false ){
			throw new Exception('Form name is empty. EX: $form["name"] = "form-name";');
		}

		//	...
		if( empty($form['method']) ){
			$form['method'] = 'post';
		}

		//	...
		$this->_form = \OP\Encode($form);

		//	Convert to associative array from numberling array.
		if( isset($this->_form['input'][0]) ){
			//	...
			$inputs = $this->_form['input'];

			//	...
			unset($this->_form['input']);

			//	...
			foreach( $inputs as $name => $input ){

				//	...
				if( is_int($name) ){
					$name = $input['name'] ?? null;
				};

				//	...
				if(!$name ){
					throw new Exception("Has not been set input name.");
				};

				//	...
				if( $this->_form['input'][$name] ?? null ){
					throw new Exception("This input name has already set. ($name)");
				}

				//	...
				$this->_form['input'][$name] = $input;
			};
		};

		/*
		//	...
		$app_id = Env::Get(_OP_APP_ID_);
		$class  = __CLASS__;
		$form_name = $this->_form['name'];

		//	...
		if( empty($_SESSION['OP'][$app_id][$class][$form_name]) ){
			$_SESSION['OP'][$app_id][$class][$form_name] = [];
		};

		//	...
		$this->_session =& $_SESSION['OP'][$app_id][$class][$form_name];
		*/

		//	Recovery saved session value.
		$this->_session = $this->Session($form_name);

		//	Regenerate session id.
		/*
		session_regenerate_id();
		*/

		//	...
		return true;
	}

	/** Initialize request.
	 *
	 */
	private function _InitRequest()
	{
		$this->_request = \OP\Encode( strtolower($this->_form['method']) === 'post' ? $_POST ?? []: $_GET  ?? [] );
	}

	/** Initialize input config.
	 *
	 */
	private function _InitInput($name=null)
	{
		//	...
		$form_name = $this->_form['name'];

		//	Result of token authentication.
		$token = $this->Token();

		//	...
		$cookie = Cookie::Get($form_name, []);

		//	Why necessary this routine?
		if( $name === null ){
			$names = array_keys($this->_form['input']);
		}else{
			$names[] = $name;
		};

		//	...
	//	foreach( $this->_form['input'] as $name => &$input ){
		foreach( $names as $name ){ // Why necessary this routine?
			//	...
			$input = &$this->_form['input'][$name];

			//	...
			$type = strtolower($this->_form['input'][$name]['type'] ?? null);

			//	The value of the button will be sent only when clicked.
			if( 'button' === $type ){
				continue;
			}

			//	...
			if( $type === 'select' or $type === 'radio' or $type === 'checkbox' ){
				$this->_InitOption($input);
			}

			//	Calc value.
			if( isset($this->_request[$name]) ){
				//	request
				$value = $this->_request[$name];
			}else if( isset($this->_session[$name]) ){
				//	session
				$value = $this->_session[$name];
			}else if( isset($cookie[$name]) ){
				//	cookie
				$value = $cookie[$name];
			}else{
				//	default
				$value = $input['value'] ?? null;
			}

			//	The value will overwrite.
			if( $value !== null ){
				//	That will not be saved in the session.
				$input['value'] = $value;

				//	Save to session?
				$is_session = $input['session'] ?? true;

				//	Check token result.
				if( $token ){
					//	Overwrite to session from submitted value.
					if( $input['session'] ?? true ){
						$this->_session[$name] = $value;
					}

					//	Save to cookie?
					if( $input['cookie'] ?? null ){
						$cookie[$name] = $value;
					}
				}else{
					//	...
				}

				//	Discard the saved session. (For developer feature)
				if( $is_session === false ){
					unset($this->_session[$name]);
				}
			}else{
				//	That was not submitted this time. (If is transmitted at different time)
				if( isset($this->_session[$name]) ){
					//	Overwrite to form config from session.
					$input['value'] = $this->_session[$name];
				}
			}
		}

		//	...
		if( count($cookie) ){
			Cookie::Set($form_name, $cookie);
		}
	}

	/** Init input option.
	 *
	 * @param	 array	&$input
	 */
	private function _InitOption(&$input)
	{
		//	In case of empty.
		if( empty($input['option']) ){
			$input['option'] = $input['options'] ?? $input['values'] ?? [];
		}

		//	In case of string.
		if( is_string($input['option']) ){
			$input['option'] = explode(',', $input['option']);
		}

		//	...
		foreach( $input['option'] as $index => &$option ){
			//	...
			$check = false;
			$value = null;

			//	...
			if( is_string($option) ){
				if( is_numeric($index) ){
					$label = $value = $option;
				}else{
					$label = $option;
					$value = $index;
				}
			}else{
				$value = $option['value'];
				$label = $option['label'];
				$check = $option['check'] ?? $option['checked'] ?? $option['select'] ?? $option['selected'] ?? false;
			}

			//	...
			$option = [];
			$option['label'] = $label;
			$option['value'] = $value;
			$option['check'] = $check;
		}
	}

	/** Get/Set form configuration.
	 *
	 * @param  string $form
	 * @return array  $form
	 */
	function Config($form=null)
	{
		//	...
		if( $form ){
			//	...
			try {
				//	...
				$this->_InitForm($form);

				//	...
				$this->_InitRequest();

				//	...
				$this->_InitInput();

				//	...
				if( Env::isAdmin() ){
					if(!FORM\Test::Config($this->_form) ){
						D( FORM\Test::Error() );
					}
				}
			} catch ( \Throwable $e ){
				Notice::Set($e);
			}
		}

		//	...
		return $this->_form;
	}

	/** Return the result of token authentication.
	 *
	 * <pre>
	 * RETURN VALUE:
	 *   null:    Token has not been set yet.
	 *   boolean: Token match result.
	 * </pre>
	 *
	 * @see    \OP\IF_FORM
	 * @return  boolean
	 */
	function Token()
	{
		//	...
		if(!isset($this->_is_token) ){
			//	Initialize.
			$this->_is_token = null;

			//	Last time token.
			$token = $this->_session['token'] ?? null;

			//	Regenerate new token.
		//	$this->_session['token'] = Hasha1(microtime());
			$this->_session['token'] = random_int(1000, 9999);

			//	Confirmation of request token.
			if( $token ){
				$this->_is_token = ($token == ($this->_request['token'] ?? false));
			};

			//	For developers.
			if( Env::isAdmin() ){
				/*
				//	...
				$token_session = $token;
				$token_request = $this->_request['token'] ?? 'null';
				$this->__DebugSet(__FUNCTION__, "{$token_session}, {$token_request}");
				*/
				/*
				require_once(__DIR__.'/function/token_debug.php');
				FORM\token_debug($this);
				*/
			};
		}

		//	...
		return $this->_is_token;
	}

	/** Print form tag. (open)
	 *
	 * @param array $config
	 */
	function Start($config=[])
	{
		//	...
		if( $this->_is_start ){
			Notice::Set("Form has already started. ({$this->_form['name']})");
		}else{
			$this->_is_start = true;
		}

		//	...
		if(!$this->_form ){
			throw new Exception("Has not been set configuration.");
		}

		//	...
		$attr = [];

		//	...
		if( empty($config['class']) ){
			$config['class'] = 'OP ';
		}else{
			$config['class'] = 'OP ' . $config['class'] . ' ';
		}

		//	...
		foreach(['action','method','name','id','class','style'] as $key){
			//	...
			$val = $config[$key] ?? $this->_form[$key] ?? null;

			//	...
			$attr[] = sprintf('%s="%s"', $key, $val);
		}

		//	...
		printf('<form %s>', join(' ', $attr));
		printf('<input type="hidden" name="form_name" value="%s" />', $this->_form['name']    );
		printf('<input type="hidden" name="token"     value="%s" />', $this->_session['token']);
	}

	/** Print form tag. (close)
	 */
	function Finish()
	{
		//	...
		if( $this->_is_start === null ){
			D("Start method was not called.");
		}

		//	...
		print "</form>";
	}

	/** Get input label.
	 *
	 * @param  string $name
	 * @return string $label
	 */
	function GetLabel($name)
	{
		//	...
		if( empty( $this->_form['input'][$name] ) ){
			Notice::Set("Does not exists this name. ($name)");
			return;
		}

		//	...
		return $this->_form['input'][$name]['label'] ?? $name;
	}

	/** Print input label.
	 *
	 * @param string $name
	 */
	function Label($name)
	{
		echo $this->GetLabel($name);
	}

	/** Generate input tag.
	 *
	 * @param	 string			 $name
	 * @return	 string
	 */
	function GetInput($name)
	{
		try {
			//	...
			if( empty($this->_form['input'][$name]) ){
				throw new Exception("This name has not been into config. ($name)");
			}

			//	...
			$input = $this->_form['input'][$name];

			//	...
			if( empty($input['name']) ){
				$input['name'] = $name;
			};

			//	...
			switch( $type = ucfirst($input['type'] ?? '') ){
				case 'Checkbox':
				case 'Radio':
				case 'Select':
				case 'Button':
					$path = "\OP\UNIT\FORM\\$type";
					return $path::Build($input);

				case 'Submit':
					return \OP\UNIT\FORM\Button::Build($input);

				default:
					return \OP\UNIT\FORM\Input::Build($input);
			}
		} catch ( \Throwable $e ) {
			Notice::Set($e);
		}
	}

	/** Print generated input tag.
	 *
	 * @param	 string	 $name
	 * @param	 array	 $input
	 */
	function Input($name, $input=null)
	{
		//	...
		if( $this->_is_start === null ){
			$this->_is_start  =  false;
			D("Start method was not called.");
		}

		//	...
		if( $input ){
			$this->SetInput($name, $input);
		}else{
			echo $this->GetInput($name);
		};
	}

	/** Set input value.
	 *
	 * @param	 string		 $name
	 * @param	 string		 $value
	 * @param	 boolean	 $session Overwrite to saved session value.
	 */
	function SetValue($name, $value, $session=true)
	{
		//	Config
		$this->_form['input'][$name]['value'] = $value;

		//	Request
		$this->_request[$name] = $value;

		//	Session
		if( $session and !empty($this->_form['input'][$name]['session']) ){
			$this->_session[$name] = $value;
		}

		//	Cookie
		$form_name = $this->_form['name'];
		$cookie = Cookie::Get($form_name, null);
		unset($cookie[$name]);
		Cookie::Set($form_name, $cookie);
	}

	/** Get value of input.
	 *
	 * @param	 string		 $name
	 * @return	 string		 $value
	 */
	function GetValue($name)
	{
		//	...
		$value = $this->_session[$name] ?? $this->_request[$name] ?? null;

		//	...
		if( gettype($value) === 'array' ){
			//	...
			if( $this->_form['input'][$name]['type'] === 'checkbox' ){
				//	Remove top index. top index is empty value.
				array_shift($value);
			}
		}

		//	...
		if( isset($this->_form['input'][$name]['option']) ){
			//	...
			if( is_string($value) ){
				//	...
				foreach( $this->_form['input'][$name]['option'] as $option ){
					//	...
					if( $value === (string)$option['value'] ){
						return $value;
					}
				}
				return null;
			}
		}

		//	...
		return $value;
	}

	/** Get saved values.
	 *
	 * @return array
	 */
	function Values()
	{
		//	Get saved session value.
		$saved_session_value = $this->_session;

		//	Remove token value.
		unset($saved_session_value['token']);

		//	...
		$result = [];

		//	Generate result each input name.
		foreach( $this->Config()['input'] as $name => $input ){
			//	If not save to session.
			if( $input['session'] ?? true ){
				//	Calc value.
				$value = $saved_session_value[$name] ?? $input['value'] ?? null;

				//	Set to result.
				$result[$name] = $value;
			}else{
				//	Set currently sent value.
				if( isset($this->_request[$name]) ){
					$result[$name] = $this->_request[$name];
					//	Do not set not sent input value.
					//	$result[$name] = $this->_request[$name] ?? null;
				}
			}
		}

		//	...
		return $result;
	}

	/** Display value at input name.
	 *
	 * @param  string $name
	 */
	function Value($name)
	{
		//	...
		$input = $this->_form['input'][$name];

		//	...
		$value = $this->GetValue($name);

		//	...
		if( $input['type'] === 'select' and ($input['multiple'] ?? null) ){
			$input['type'] = 'multiple';
		}

		//	...
		switch( /* $type = */ $input['type'] ){
			case 'radio':
			case 'select':
				foreach( $input['option'] as $option ){
					//	...
					if(!isset($option['value']) ){ continue; }

					//	...
					if( $value === (string)$option['value'] ){
						$value = $option['label'];
						break;
					}
				}
				break;

			case 'checkbox':
			case 'multiple':
				$label = null;
				foreach( $input['option'] as $option ){
					if( is_array($value) and in_array($option['value'], $value, false) ){
						$label .= '<span>'.$option['label'].'</span>';
					}
				}
				$value = $label;
				break;

			case 'textarea':
				$value = nl2br($value);
				break;

			default:
		}

		//	...
		echo $value;
	}

	/** Get error.
	 *
	 * @param string $name
	 */
	function GetError($name)
	{
		return $this->_errors[$name] ?? [];
	}

	/** Display error message.
	 *
	 * @param string $name
	 */
	function Error($name, $format='<span class="error">$label is $rule error.</span>')
	{
		//	...
		$config = $this->Config();

		//	...
		$format = $config['error'] ?? $format;

		//	...
		foreach( $this->GetError($name) as $rule => $var ){
			//	...
			if( $var === false ){
				continue;
			}

			//	...
			$input = $config['input'][$name];
			$label = $input['label'] ?? $name;
			$error = $input['errors'][$rule] ?? $input['error'] ?? $format;

			//	...
			print str_replace(
				['$label','$Name','$name','$Rule','$rule','$value'],
				[$label, ucfirst($name), $name, ucfirst($rule), $rule, $var],
				$error
			);
		};
	}

	/** Clear saved session value.
	 *
	 */
	function Clear($input_name='')
	{
		//	...
		if(!$this->_form ){
			Notice::Set("Has not been set form configuration.");
			return;
		}

		//	...
		$token = $this->_session['token'];
		$this->_session = [];
		$this->_session['token'] = $token;
		$this->Session($this->_form['name'], $this->_session);

		//	...
		Cookie::Set($this->_form['name'], []);

		//	...
		$this->_request = null;

		//	...
		foreach( $this->_form['input'] as &$input ){
			unset($input['value']);
		}
	}

	/** Set input config.
	 *
	 * @param	 array	 $input
	 */
	function SetInput($input)
	{
		//	...
		if(!$name = $input['name'] ?? null ){
			Notice::Set("Has not been set input name.");
			return;
		}

		//	Overwrite existing settings if there is one.
		foreach( $input as $key => $val ){
			$this->_form['input'][$name][$key] = $val;
		}

		//	...
		$this->_InitInput($name);
	}

	/** Set input config.
	 *
	 * @param	 string	 $name
	 * @param	 array	 $option
	 */
	function SetOption($input_name, $option)
	{
		//	...
		if( empty($this->_form['input'][$input_name]) ){
			Notice::Set("Has not been set this input. ($input_name)");
			return;
		}

		//	...
		$this->_form['input'][$input_name]['option'] = $option;

		//	...
		$this->_InitOption($this->_form['input'][$input_name]);
	}

	/** Validate
	 *
	 * <pre>
	 * Return value
	 *   Null is unmatch token. (Not do validation.)
	 *   Boolean is validation result. (true is no problem.)
	 * </pre>
	 *
	 * @param	 string			 $input_name
	 * @return	 null|boolean	 $io
	 */
	function Validate($input_name=null)
	{
		//	...
		if(!$this->_validate ){
			//	...
			if(!\OP\Unit::Load('validate') ){
				return;
			}

			//	Check if validate.
			if( $this->_errors ){
				//	Already validation.
			}else{
				//	...
				$config = $this->Config();
				$values = $this->Values();

				//	Each inputs.
				foreach( $config['input'] as $name => $input ){

					//	...
					$validate = $input['validate'] ?? $input['rule'] ?? null;

					//	Get validation rule.
					if(!$validate ){
						continue;
					};

					//	Do validation.
					$this->_validate[$name] = $this->Unit('validate')->Evaluation($validate, $values[$name] ?? null, $this->_errors[$name], $values);
				};
			};
		};

		//	...
		return $input_name ? ($this->_validate[$input_name] ?? null): $this->_validate;
	}

	/** Validation result.
	 *
	 * @see \OP\IF_FORM::isValidate()
	 * @return null|boolean
	 */
	function isValidate()
	{
		//	...
		if(!$this->Token() ){
			return null;
		};

		//	...
		$validate = $this->Validate() ?? [];

		//	...
		return (array_search(false, $validate, true) === false) ? true: false;
	}

	/** Configuration test.
	 *
	 */
	function Test()
	{
		//	...
		if(!Env::isAdmin() ){
			return false;
		}

		//	...
		if(!$io = FORM\Test::Config($this->_form) ){
			return FORM\Test::Error();
		}

		//	...
		return $io;
	}

	/** Pre debug method.
	 *
	 * @deprecated 2020-08-25
	 * @param  string  $topic
	 */
	private function _PreDebug($topic=null)
	{
		//	...
		$form_name = $this->_form['name'];

		//	...
		$this->_debug['config']  = $this->_form['input'][$topic] ?? $this->_form;
		$this->_debug['request'] = $this->_request[$topic]       ?? $this->_request;
		$this->_debug['session'] = $this->_session[$topic]       ?? $this->_session;
		$this->_debug['cookie']  = Cookie::Get($form_name, null);
	}
}
