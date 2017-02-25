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
	use OP_CORE;

	/** Form configuration.
	 *
	 * @var array
	 */
	private $_form = [];

	/** Session
	 *
	 * @var unknown
	 */
	private $_sesssion;

	/** Destruct
	 *
	 */
	function __destruct()
	{
		//	Save changed token value.
		$this->_sesssion['token'] = $this->_form['token']['value'];
	}

	/** Init session
	 *
	 */
	private function _InitSession()
	{
		if(!isset($this->_sesssion)){
			//	...
			if(!session_id()){
				session_start();
			}

			//	...
			$name = $this->_form['name'];
			$u8s  = $this->_form['u8s'];

			//	...
			$this->_sesssion = &$_SESSION[_OP_NAME_SPACE_]['unit']['form'][$name][$u8s];
		}
	}

	/** Uniqueness
	 *
	 */
	private function _Uniqueness()
	{
		return ifset($_REQUEST['u8s'] ,Hash1(microtime()));
	}

	/** Print button tag.
	 *
	 * @param string $name
	 */
	function Button($name)
	{
		$input = $this->_form['input'][$name];
		Input::Build($input);
	}

	/** Print input tag as type of checkbox.
	 *
	 * @param string $name
	 */
	function Checkbox($name)
	{
		$input = $this->_form['input'][$name];
		Checkbox::Build($input, ifset($this->_sesssion[$input['name']]));
	}

	/** Print form tag. (close)
	 */
	function Finish()
	{
		print "</form>";
	}

	/** Get/Set form configuration.
	 *
	 * @param string $form
	 */
	function Config($form=null)
	{
		if( $form ){
			if( $this->_form ){
				Notice::Set("Already initialized. {$this->_form['name']}");
				return;
			}

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
				$this->_form['token']['name']  = ifset($this->_form['u8s'], 'token');
				$this->_form['token']['value'] = Hash1(microtime());
			}
		}else{
			return $this->_form;
		}
	}

	/** Print input tag.
	 *
	 * @param string $name
	 */
	function Input($name)
	{
		if( $input = ifset($this->_form['input'][$name]) ){
			switch( $type = ucfirst(ifset($input['type'])) ){
				case 'Select':
					return $type::Build($input, ifset($this->_sesssion[$input['name']]));
					break;
				default:
					return Input::Build($input, ifset($this->_sesssion[$input['name']]));
			}
		}else{
			Notice::Set("Has not been set. ($name)");
		}
	}

	/** Print label name.
	 *
	 * @param string $name
	 */
	function Label($name)
	{
		//	...
		if( $label = ifset($this->_form['input'][$name]['label']) ){
			return $label;
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

	/** Configuration is load at file path.
	 *
	 * @param string $file_path
	 */
	function Load($file_path)
	{
		try {
			if( file_exists($file_path) ){
				include($file_path);
				if( isset($form) ){
					$this->Config($form);
				}else{
					Notice::Set("Does not exists \$form variable.");
				}
			}else{
				Notice::Set("Does not exists this file. ($file_path)");
			}
		} catch ( Throwable $e ) {
			Notice::Set($e->getMessage());
		}
	}

	/** Print input tag as type of radio.
	 *
	 * @param string $name
	 */
	function Radio($name)
	{
		$input = $this->_form['input'][$name];
		Radio::Build($input, ifset($this->_sesssion[$input['name']]));
	}

	/** Save submit value to session.
	 *
	 * @param array $name
	 */
	function Save($request)
	{
		//	...
		if(!isset($this->_sesssion)){
			$this->_InitSession();
		}

		//	...
		foreach($this->_form['input'] as $input){
			$name = ifset($input['name']);
			if( isset($request[$name]) ){
				$this->_sesssion[$name] = Escape($request[$name]);
			}
		}
	}

	/** Print select tag.
	 *
	 * @param string $name
	 */
	function Select($name)
	{
		$input = $this->_form['input'][$name];
		Select::Build($input, ifset($this->_sesssion[$input['name']]));
	}

	/** Print form tag. (open)
	 *
	 */
	function Start()
	{
		$attr = [];
		foreach(['action','method','name','class','style'] as $key){
			if( $val = ifset($this->_form[$key]) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}

		//	...
		printf('<form %s>', join(' ', $attr));
		printf('<input type="hidden" name="form_name" value="%s" />', $this->_form['name']);
		printf('<input type="hidden" name="u8s" value="%s" />',       $this->_form['u8s']);
		printf('<input type="hidden" name="%s"  value="%s" />',       $this->_form['token']['name'], $this->_form['token']['value']);
	}

	/** Print input tag as type of submit.
	 *
	 * @param string $name
	 */
	function Submit($name)
	{
		$input = $this->_form['input'][$name];
		Input::Build($input);
	}

	/** Configuration's test.
	 *
	 */
	function Test()
	{
		if( Env::isAdmin() ){
			Test::Config($this->_form);
		}
	}

	/** Print textarea tag.
	 *
	 * @param string $name
	 */
	function Textarea($name)
	{
		$input = $this->_form['input'][$name];
		self::Input($name);
	}

	function Token()
	{
		//	...
		if(!isset($this->_sesssion)){
			$this->_InitSession();
		}

		//	...
		$request = Http::Request();

		//	...
		if( $token = ifset($request[$this->_form['token']['name']]) ){
			if( $token === $this->_sesssion['token'] ){
				return true;
			}
		}

		//	...
		return false;
	}

	/** Vaildate
	 *
	 * @return array $errors
	 */
	function Validate()
	{
		//	...
		$errors = [];

		//	...
		$request = Http::Request();

		//	...
		if(!isset($request['u8s']) ){
			return;
		}

		//	...
		if( Unit::Load('validate') ){
			$this->Save( Validate::Sanitize($this->_form, $request, $errors) );
		}

		//	...
		return $errors;
	}

	/** Get/Set value of input.
	 *
	 * @param string $name
	 * @param string $value Set or Overwrite value.
	 */
	function Value($name, $value=null)
	{
		//	...
		if(!isset($this->_sesssion)){
			$this->_InitSession();
		}

		//	...
		if( $value !== null ){
			$this->_sesssion[$name] = Escape($value);
		}

		//	...
		return ifset($this->_sesssion[$name]);
	}
}