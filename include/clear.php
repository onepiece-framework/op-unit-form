<?php
/** op-unit-form:/include/clear.php
 *
 * @created   2021-01-23
 * @version   1.0
 * @package   op-unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP;

/* @var $this UNIT\Form */

//	...
if(!$this->_form ){
	Notice::Set("Has not been set form configuration.");
	return;
}

/* @var $input_name string */
if( $input_name ){
	//	...
	if(!isset($this->_form['input'][$input_name]) ){
		\OP\Notice("This input name does not exists. ($input_name)");
		return;
	}

	//	...
	$input = &$this->_form['input'][$input_name];

	//	...
	if( $input['echo'] ?? null ){
		\OP\Notice("This input already displayed. ($input_name)");
		return;
	}

	//	...
	if( $input['cookie'] ?? null ){
		$cookie = Cookie::Get($this->_form['name']);
		unset($cookie[$input_name]);
		Cookie::Set($this->_form['name'], $cookie);
	}
	$this->_session[$input_name] = null;
	$input['value'] = $input['original'] ?? null;
	return;
}

//	Save token value.
$token = $this->_session['token'];
//	Saved session is clear.
$this->_session = [];
//	Recovery token value.
$this->_session['token'] = $token;
//	Set empty value.
$this->Session($this->_form['name'], $this->_session);

//	...
Cookie::Set($this->_form['name'], []);

//	...
$this->_request = null;

//	...
foreach( $this->_form['input'] as &$input ){
	//	...
	if( $input['echo'] ?? null ){
		\OP\Notice("This input already displayed. ($input_name)");
		continue;
	}
	/*
	 unset($input['value']);
	 */
	$input['value'] = $input['original'] ?? null;
}
