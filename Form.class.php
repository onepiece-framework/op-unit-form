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

	/** Init session
	 *
	 */
	private function _InitSession()
	{
		if(!isset($this->_sesssion)){
			//	...
			if(!$form_name = ifset($this->_form['name'])){
				Notice::Set("Does not set form name.");
				return;
			}

			//	...
			if(!session_id()){
				session_start();
			}

			//	...
			$this->_sesssion = &$_SESSION[_OP_NAME_SPACE_]['unit']['form'][$form_name];
		}
	}

	/**
	 * Print button tag.
	 *
	 * @param string $name
	 */
	function Button($name)
	{
		$input = $this->_form['input'][$name];
		Input::Build($input);
	}

	/**
	 * Print input tag as type of checkbox.
	 *
	 * @param string $name
	 */
	function Checkbox($name)
	{
		$input = $this->_form['input'][$name];
		Checkbox::Build($input, ifset($this->_sesssion[$input['name']]));
	}

	/**
	 * Print form tag. (close)
	 */
	function Finish()
	{
		print "</form>";
	}

	/**
	 * Get form configuration.
	 *
	 * @param string $form
	 */
	function GetForm($attr=null)
	{
		//	...
		if( empty($this->_form) ){
			Notice::Set("Configuration has not been setting.");
			return;
		}

		//	...
		return $attr ? ifset($this->_form[$attr]) : $this->_form;
	}

	/**
	 * Print input tag.
	 *
	 * @param string $name
	 */
	function Input($name)
	{
		if( $input = $this->_form['input'][$name] ){
			switch( $type = ucfirst(ifset($input['type'])) ){
				case 'Select':
					$type::Build($input, ifset($this->_sesssion[$input['name']]));
					break;
				default:
					Input::Build($input, ifset($this->_sesssion[$input['name']]));
			}
		}else{
			Notice::Set("Has not been set. ($name)");
		}
	}

	/**
	 * Print label name.
	 *
	 * @param string $name
	 */
	function Label($name)
	{
		//	...
		if( $label = ifset($this->_form['input'][$name]['label']) ){
			print $label;
			return;
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

	function Load($file_path)
	{
		try {
			if( file_exists($file_path) ){
				include($file_path);
				if( isset($form) ){
					$this->SetForm($form);
				}else{
					Notice::Set("Does not exists \$form variable.");
				}
			}else{
				Notice::Set("Does not exists this file. ($file_path)");
			}
		} catch ( Throwable $e ) {
			Notice::Set( $e->getMessage() );
		}
	}

	/**
	 * Print input tag as type of radio.
	 *
	 * @param string $name
	 */
	function Radio($name)
	{
		$input = $this->_form['input'][$name];
		Radio::Build($input, ifset($this->_sesssion[$input['name']]));
	}

	/**
	 * Save submit value to session.
	 */
	function Save($request)
	{
		//	...
		$this->_InitSession();

		//	...
		foreach($this->_form['input'] as $input){
			$name = $input['name'];
			if( isset($request[$name]) ){
				$this->_sesssion[$name] = Escape($request[$name]);
			}
		}
	}

	/**
	 * Set form configuration.
	 *
	 * @param string $form
	 */
	function SetForm($form)
	{
		if( $this->_form ){
			Notice::Set("Already initialized. {$this->_form['name']}");
			return;
		}

		if( empty($form['name']) ){
			Notice::Set('Form name is empty. $form["name"] = "form-name";');
		}

		//	...
		$this->_form = Escape($form);
		$this->_form['escaped'] = true;
	}

	/**
	 * Print select tag.
	 *
	 * @param string $name
	 */
	function Select($name)
	{
		$input = $this->_form['input'][$name];
		Select::Build($input, ifset($this->_sesssion[$input['name']]));
	}

	/**
	 * Print form tag. (open)
	 */
	function Start()
	{
		$attr = [];
		foreach(['action','method','name','class','style'] as $key){
			if( $val = ifset($this->_form[$key]) ){
				$attr[] = sprintf('%s="%s"', $key, $val);
			}
		}
		printf('<form %s>', join(' ', $attr));
		printf('<input type="hidden" name="form_name" value="%s" />', $this->_form['name']);
	}

	/**
	 * Print input tag as type of submit.
	 *
	 * @param string $name
	 */
	function Submit($name)
	{
		$input = $this->_form['input'][$name];
		Input::Build($input);
	}

	/**
	 * Configuration's test.
	 */
	function Test()
	{
		if( Env::isAdmin() ){
			Test::Config($this->_form);
		}
	}

	/**
	 * Print textarea tag.
	 *
	 * @param string $name
	 */
	function Textarea($name)
	{
		$input = $this->_form['input'][$name];
		self::Input($name);
	}
}