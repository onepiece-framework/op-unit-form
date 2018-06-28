<?php
/**
 * unit-form:/Form.class.php
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-01-22
 */
namespace OP\UNIT;

/** Form
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Form
{
	/** Trait
	 *
	 */
	use \OP_CORE, \OP_SESSION;

	/** Form configuration.
	 *
	 * @var array
	 */
	private $_form = [];

	/** Error of validation.
	 *
	 * @var array
	 */
	private $_errors;

	/** Session
	 *
	 */
	private $_session = [];

	/** Request
	 *
	 */
	private $_request = [];

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

	}

	/** Destruct
	 *
	 */
	function __destruct()
	{
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
				\Notice::Set("Does not found this file. ($form)");
				return;
			}

			//	Load by file path.
			try {
				$form = include($form);
			} catch ( \Throwable $e ){
				\Notice::Set($e);
				return;
			}
		}

		//	...
		if( $this->_form ){
			\Notice::Set("Already initialized. ({$this->_form['name']})");
			return;
		}

		//	...
		if(!$form_name = $form['name'] ?? false ){
			\Notice::Set('Form name is empty. EX: $form["name"] = "form-name";');
			return;
		}

		//	...
		if( empty($form['method']) ){
			$form['method'] = 'post';
		}

		//	...
		$this->_form = Escape($form);

		//	...
		$this->_session = $this->Session($form_name);

		//	...
		return true;
	}

	/** Get/Set form configuration.
	 *
	 * @param  string $form
	 * @return array  $form
	 */
	function Config($form=null)
	{
		//	...
		if(!$form ){
			return $this->_form;
		}else{
			//	...
			if(!$this->Init($form) ){
				return;
			}

			//	...
			$form_name = $this->_form['name'];

			//	Result of token authentication.
			$token = $this->Token();

			//	...
			$request = $this->Request();

			//	...
			$cookie = \Cookie::Get($form_name, []);

			//	...
			if( empty($this->_form['input']) ){
				$this->_form['input'] = [];
			}

			//	...
			foreach( $this->_form['input'] as $name => &$input ){
				//	...
				$type = strtolower($this->_form['input'][$name]['type']);

				//	The value of the button will be sent only when clicked.
				if( 'button' === $type ){
					continue;
				}

				//	...
				if( $type === 'select' or $type === 'radio' or $type === 'checkbox' ){
					$this->_AdjustValues($input);
				}

				//	...
				if( isset($request[$name]) ){
					$value = $request[$name];
				}else if( isset($cookie[$name]) ){
					$value = $cookie[$name];
				}else{
					$value = null;
				}

				//	The value will overwrite.
				if( $value !== null ){
					//	That will not be saved in the session.
					$input['value'] = $value;

					//	Save to session?
					$is_session = ifset($input['session'], true);

					//	Check token result.
					if( $token and $is_session ){
						//	Overwrite to session from submitted value.
						if( $input['session'] ?? true ){
							$this->_session[$name] = $value;
						}

						//	Save to cookie?
						if( ifset($input['cookie']) ){
							$cookie[$name] = $value;
						}
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
				\Cookie::Set($form_name, $cookie);
			}
		}
	}

	/** Configuration is load at file path.
	 *
	 * @param string $file_path
	 */
	function Load($file_path)
	{
		try {
			if( file_exists($file_path) ){
				$form = include($file_path);
				$this->Config($form);
			}else{
				\Notice::Set("Does not exists this file. ($file_path)");
			}
		} catch ( Throwable $e ) {
			\Notice::Set($e->getMessage());
		}
	}

	/** Return the result of token authentication.
	 *
	 * <pre>
	 * RETURN VALUE:
	 *   null:    Token has not been set yet.
	 *   boolean: Token match result.
	 * </pre>
	 *
	 * @return boolean
	 */
	function Token()
	{
		static $io = '';

		//	...
		if( $io === '' ){
			//	Last time token.
			$token = ifset($this->_session['token'], false);

			//	Regenerate new token.
			$this->_session['token'] = Hasha1(microtime());

			//	Confirmation of request token.
			if( $token ){
				$io = ($token === self::Request('token'));
			}else{
				$io = null;
			}
		}

		return $io;
	}

	/** Request
	 *
	 * @param	 null|string	 $name
	 */
	function Request($name='')
	{
		static $_request = null;

		//	...
		if( $_request === null ){
			$_request = ifset($_POST);
		}

		//	Erase cached request value.
		if( $name === null ){
			$_request = [];
		}

		//	...
		return $name ? ifset($_request[$name]): $_request;
	}

	/** Configuration test.
	 *
	 */
	function Test()
	{
		//	...
		if(!\Env::isAdmin() ){
			return false;
		}

		//	...
		if(!$io = \OP\UNIT\FORM\Test::Config($this->_form) ){
			return \OP\UNIT\FORM\Test::Error();
		}

		//	...
		return $io;
	}

	/** Print form tag. (open)
	 *
	 * @param array $config
	 */
	function Start($config=[])
	{
		//	...
		if( $this->_is_start ){
			\Notice::Set("Form has already started. ({$this->_form['name']})");
		}else{
			$this->_is_start = true;
		}

		//	...
		if(!$this->_form ){
			throw new \Exception("Has not been set configuration.");
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
			\Notice::Set("Does not exists this name. ($name)");
			return;
		}

		//	...
		if( isset( $this->_form['input'][$name]['label'] ) ){
			return $this->_form['input'][$name]['label'];
		}

		//	...
		if( empty( $this->_form['input'][$name]['label'] ) ){
			\Notice::Set("Has not been set label. ($name)");
			return;
		}
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
	 * @param  string $name
	 * @return string
	 */
	function GetInput($name)
	{
		static $request;

		//	...
		if(!$request){
			$request = self::Request();
		}

		//	...
		try {
			//	...
			if( empty($this->_form['input'][$name]) ){
				throw new \Exception("This name has not been into config. ($name)");
			}

			//	...
			$input = $this->_form['input'][$name];

			//	...
			$input['name'] = $name;

			//	...
			switch( $type = ucfirst(ifset($input['type'])) ){
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
			\Notice::Set($e);
		}
	}

	/** Print generated input tag.
	 *
	 * @param string $name
	 */
	function Input($name)
	{
		echo $this->GetInput($name);
	}

	/** Get/Set value of input.
	 *
	 * @param string $name
	 * @param string $value Set or Overwrite value.
	 */
	function GetValue($name, $value=null)
	{
		//	Override input value.
		if( $value !== null ){
			$this->_session[$name] = Escape($value);
		}

		//	...
		$value = ifset($this->_session[$name]);

		//	...
		if( $value === null ){
			$value = self::Request($name);
		}

		//	...
		if( gettype($value) === 'array' ){
			//	...
			if( $this->_form['input'][$name]['type'] === 'checkbox' ){
				//	Remove top index. top index is empty value.
				array_shift($value);
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
		//	...
		$values = $this->_session;

		//	...
		unset($values['token']);

		//	...
		return $values;
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
		if( $input['type'] === 'select' and ifset($input['multiple']) ){
			$input['type'] = 'multiple';
		}

		//	...
		switch( $type = $input['type'] ){
			case 'radio':
			case 'select':
				foreach( $input['values'] as $values ){
					//	...
					if(!isset($values['value']) ){ continue; }

					//	...
					if( $value === (string)$values['value'] ){
						$value = $values['label'];
						break;
					}
				}
				break;

			case 'checkbox':
			case 'multiple':
				$labels = [];
				foreach( $input['values'] as $values ){
					if( is_array($value) and in_array($values['value'], $value, false) ){
						$labels[] = $values['label'];
					}
				}
				$value = $labels;
				break;

			case 'textarea':
				$value = nl2br($value);
				break;

			default:
		}

		//	...
		if( is_string($value) ){
			echo $value;
		}else{
			D($value);
		}
	}

	function Error()
	{

	}

	/** Clear saved session value.
	 *
	 */
	function Clear()
	{
		//	...
		if(!$this->_form ){
			\Notice::Set("Has not been set form configuration.");
			return;
		}

		//	...
		$token = $this->_session['token'];
		$this->_session = [];
		$this->_session['token'] = $token;

		//	...
		\Cookie::Set($this->_form['name'], []);

		//	...
		$this->Request(null);

		//	...
		foreach( $this->_form['input'] as &$input ){
			unset($input['value']);
		}
	}
}
