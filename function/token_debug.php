<?php
/**
 * unit-form:/function/token_debug.php
 *
 * @created   2020-01-17
 * @version   1.0
 * @package   unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 */
namespace OP\UNIT\FORM;

/** For developers debugging information.
 *
 * @created   2020-01-17

 */
function token_debug($form){
	//	...
	if( ($form->_form['name'] ?? null) !== ($form->_request['form_name'] ?? null ) ){
		$form_name = 'Config:'.$form->_form['name'].', Request:'.($form->_request['form_name'] ?? 'null');
		$form->__DebugSet(__FUNCTION__, "Form name is unmatch. ($form_name)");
	}else

		//	...
		if( $form->_is_token === null ){
			$form->__DebugSet(__FUNCTION__, "Token has not been set yet.");
	}else

		//	...
		if( $form->_is_token === false ){
			$form->__DebugSet(__FUNCTION__, "Token is unmatch.");
	}else{
		$form->__DebugSet(__FUNCTION__, "Token is match.");
	}
}
