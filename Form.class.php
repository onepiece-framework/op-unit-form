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

/**
 * Form
 *
 * @created   2017-01-25
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Form
{
	//	...
	use OP_CORE, OP_SESSION;

	/** Form configuration.
	 *
	 * @var array
	 */
	private $_form = [];

	/** Cookie
	 *
	 * @var array
	 */
	private $_cookie;

	/** Session
	 *
	 * @var array
	 */
	private $_sesssion;

	/** Error of validation.
	 *
	 * @var array
	 */
	private $_errors;

	/** Construct
	 *
	 */
	function __construct()
	{
		$this->_sesssion = &$this->Session('STORE');
	}

	/** Destruct
	 *
	 */
	function __destruct()
	{
		//	Save changed token value.
		$this->_sesssion['token'] = $this->_form['token']['value'];

		//	Save cookie.
		Cookie::Set($this->_form['name'], $this->_cookie);
	}

	/** Uniqueness
	 *
	 */
	private function _Uniqueness()
	{
		return ifset($_REQUEST['u8s'] ,Hasha1(microtime()));
	}

	/** Get/Set form configuration.
	 *
	 * @param  string $form
	 * @return array  $form
	 */
	function Config($form=null)
	{
		if( $form ){
			//	...
			if( $this->_form ){
				Notice::Set("Already initialized. {$this->_form['name']}");
				return;
			}

			//	...
			if( empty($form['name']) ){
				Notice::Set('Form name is empty. $form["name"] = "form-name";');
				return;
			}

			//	...
			$this->_form = Escape($form);
			$this->_form['escaped'] = true;

			//	Uniqueness
			if( ifset($this->_form['u8s'], true) ){
				$this->_form['u8s'] = $this->_Uniqueness();
			}

			//	Token
			if( ifset($this->_form['token'], true) ){
				$this->_form['token'] = [
					'key'   => ifset($this->_form['u8s'], 'token'),
					'value' => Hasha1(microtime())
				];
			}

			//	Initialize of cookie.
			$this->_cookie = Cookie::Get($this->_form['name']);

			/*
			//	Expire time
			if( empty($this->_form['expire']) ){
				$this->_form['expire'] = 60 * 60 * 1;
			}
			*/
		}else{
			return $this->_form;
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
				Notice::Set("Does not exists this file. ($file_path)");
			}
		} catch ( Throwable $e ) {
			Notice::Set($e->getMessage());
		}
	}

	/** Save submit value to session.
	 *
	 * @param  array $request
	 * @return array $session
	 */
	function Save($request=null)
	{
		//	Get form name. Saved session value has separated to each form name.
		$form = $this->_form['name'];

		//	...
		if( $request ){
			//	Each input.
			foreach($this->_form['input'] as $name => $input){
				//	Submitted value.
				if( isset($request[$name]) ){

					//	Save to Session.
					$this->_sesssion[$form][$name] = $value = Escape($request[$name]);

					//	Save to Cookie.
					$this->_cookie[$name] = ifset($input['cookie']) ? $value: null;
				}
			}
		}

		//	...
		return $this->_sesssion[$form];
	}

	/** Token
	 *
	 * @return boolean
	 */
	function Token()
	{
		$request = Http::Request();

		//	...
		$key = $this->_form['token']['key'];

		//	...
		if( empty($request[$key]) ){
			return false;
		}

		//	...
		if( empty($this->_sesssion['token']) ){
			return false;
		}

		//	...
		if( $this->_sesssion['token'] !== $request[$key] ){
			return false;
		}

		//	...
		return true;
	}

	/** Configuration test.
	 *
	 */
	function Test()
	{
		if( Env::isAdmin() ){
			Test::Config($this->_form);
		}
	}

	/** Print form tag. (open)
	 *
	 * @param string $action
	 */
	function Start($action=null)
	{
		//	...
		$attr = [];

		//	...
		$key = 'action';
		$val = ifset($action, $this->_form[$key]);
		$attr[] = sprintf('%s="%s"', $key, $val);

		//	...
		foreach(['method','name','class','style'] as $key){
			if( $val = ifset($this->_form[$key]) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		printf('<form %s>', join(' ', $attr));
		printf('<input type="hidden" name="form_name" value="%s" />', $this->_form['name']);
		printf('<input type="hidden" name="u8s" value="%s" />',       $this->_form['u8s']);
		printf('<input type="hidden" name="%s"  value="%s" />',       $this->_form['token']['key'], $this->_form['token']['value']);
	}

	/** Print form tag. (close)
	 */
	function Finish()
	{
		print "</form>";
	}

	/** Print label name.
	 *
	 * @param string $name
	 */
	function Label($name)
	{
		//	...
		if( isset( $this->_form['input'][$name]['label']) ){
			return $this->_form['input'][$name]['label'];
		}

		//	...
		if( empty($this->_form['input'][$name]) ){
			Notice::Set("Does not exists this name. ($name)");
			return;
		}

		//	...
		if( empty($this->_form['input'][$name]['label']) ){
			Notice::Set("Has not been set label. ($name)");
			return;
		}
	}

	/** Print input tag.
	 *
	 * @param  string $name
	 * @return string
	 */
	function Input($name)
	{
		//	...
		static $request;

		//	...
		if(!$request){
			$request = Http::Request();
		}

		//	...
		if( $input = ifset($this->_form['input'][$name]) ){
			//	...
			if( empty($input['name']) ){
				$input['name'] = $name;
			}

			//	...
			if(!$value = ifset($request[$name], ifset($this->_sesssion[$this->_form['name']][$name]))){
				$value = Cookie::Get($name);
			}

			//	...
			switch( $type = ucfirst(ifset($input['type'])) ){
				case 'Checkbox':
				case 'Radio':
				case 'Select':
					return $type::Build($input, $value);

				default:
					return Input::Build($input, $value);
			}
		}else{
			Notice::Set("This name has not been into config. ($name)");
		}
	}

	/** Output error message by validation.
	 *
	 * @param string $name
	 */
	function Error($name, $tag=null, $class=null)
	{
		//	...
		if( empty($this->_errors[$name]) ){
			return;
		}

		//	...
		$result = null;
		foreach( $this->_errors[$name] as $key => $val ){
			//	...
			if( isset($this->_form['input'][$name]['error'][$key]) ){
				$error = $this->_form['input'][$name]['error'][$key];
			}else{
				$error = "Error of \"$key\" at $name.";
			}

			//	...
			if( $tag ){
				$tag   = Escape($tag);
				$class = Escape($class);
				$error = "<$tag class=\"$class\">$error</$tag>";
			}

			//	...
			$result .= $error;
		}

		return $result;
	}

	/** Get/Set value of input.
	 *
	 * @param string $name
	 * @param string $value Set or Overwrite value.
	 */
	function Value($name, $value=null)
	{
		//	...
		$form = $this->_form['name'];

		//	Override input value.
		if( $value !== null ){
			$this->_sesssion[$form][$name] = Escape($value);
		}

		//	...
		$value = ifset($this->_sesssion[$form][$name]);

		if( gettype($value) === 'array' ){
			if( $this->_form['input'][$name]['type'] === 'checkbox' ){
				//	Remove top index. top index is empty value.
				array_shift($value);
			}
			$value = join(', ', $value);
		}

		return $value;
	}

	/** Set validation result.
	 *
	 * @param array $errors
	 */
	function Validation($errors)
	{
		$this->_errors = $errors;
	}

	/** Clear saved session value.
	 *
	 */
	function Clear()
	{
		$form = $this->_form['name'];
		$this->_sesssion[$form] = null;
	}
}
