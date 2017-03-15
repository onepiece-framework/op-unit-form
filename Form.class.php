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

			//	Remove expired data
			foreach( ifset($_SESSION[_OP_NAME_SPACE_]['unit']['form'], []) as $name => $data ){
				if( $expire = ifset($data['expire']) ){
					if( $expire < Time::Get() ){
						unset($_SESSION[_OP_NAME_SPACE_]['unit']['form'][$name]);
					}
				}
			}

			//	...
			$name   = $this->_form['name'];
			$u8s    = $this->_form['u8s'];
			$expire = $this->_form['expire'];

			//	Set expire of data.
			$_SESSION[_OP_NAME_SPACE_]['unit']['form'][$name]['expire'] = Time::Get() + $expire;

			//	...
			if( count($_SESSION[_OP_NAME_SPACE_]['unit']['form'][$name]) > 10 ){
				unset($_SESSION[_OP_NAME_SPACE_]['unit']['form'][$name]);
			}

			//	...
			$this->_sesssion = &$_SESSION[_OP_NAME_SPACE_]['unit']['form'][$name][$u8s];
		}
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
				$this->_form['token']['key']   = ifset($this->_form['u8s'], 'token');
				$this->_form['token']['value'] = Hasha1(microtime());
			}

			//	Expire time
			if( empty($this->_form['expire']) ){
				$this->_form['expire'] = 60 * 60 * 1;
			}
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
				//	Save to Session.
				$this->_sesssion[$name] = Escape($request[$name]);

				//	Save to Cookie.
				if( ifset($input['cookie']) ){
					Cookie::Set($name, $this->_sesssion[$name]);
				}
			}
		}
	}


	/** Token
	 *
	 * @return boolean
	 */
	function Token()
	{
		//	...
		if(!isset($this->_sesssion)){
			$this->_InitSession();
		}

		//	...
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

	/** Configuration's test.
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
			if(!$request = Http::Request() ){
				$request = $this->_sesssion;
			}
		}

		//	...
		if( $input = ifset($this->_form['input'][$name]) ){

			//	...
			$name  = ifset($input['name'], $name);

			//	...
			if(!$value = ifset($request[$name])){
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
			Notice::Set("Has not been set. ($name)");
		}
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