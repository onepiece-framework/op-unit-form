<?php
/**
 * module-testcase:/unit/form/config.inc.php
 *
 * @creation  2019-03-01
 * @version   1.0
 * @package   module-testcase
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
//	...
$form = [];
$form['name'] = 'testcase-unit-form';

//	...
$input = [];
$input['name']	 = 'text';
$input['type']	 = 'text';
$input['label']	 = 'Text';
$input['cookie'] =  true;
$input['session']=  true;
$input['placeholder'] = 'one line text';
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'password';
$input['type']	 = 'password';
$input['label']	 = 'Password';
$input['cookie'] =  true;
$input['session']=  true;
$input['placeholder'] = 'password';
$input['validate'] = [
	'required' => true,
	'short'    => 8,
	'long'     => 10,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'textarea';
$input['type']	 = 'textarea';
$input['label']	 = 'Textarea';
$input['cookie'] =  true;
$input['session']=  true;
$input['placeholder'] = 'multi line text area';
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'select';
$input['type']	 = 'select';
$input['label']	 = 'Select';
$input['cookie'] =  true;
$input['session']=  true;
$input['option'] = [
	''          => '',
	'apple'     => 'Apple',
	'google'    => 'Google',
	'microsoft' => 'Microsoft',
];
$input['validate'] = [
		'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'radio';
$input['type']	 = 'radio';
$input['label']	 = 'Radio';
$input['cookie'] =  true;
$input['session']=  true;
$input['option'] = [
	'apple'     => 'Apple',
	'google'    => 'Google',
	'microsoft' => 'Microsoft',
];
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'checkbox';
$input['type']	 = 'checkbox';
$input['label']	 = 'Checkbox';
$input['cookie'] =  true;
$input['session']=  true;
$input['option'] = [
	'apple'     => 'Apple',
	'google'    => 'Google',
	'microsoft' => 'Microsoft',
];
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'multiple';
$input['type']	 = 'select';
$input['label']	 = 'Multiple';
$input['cookie'] =  true;
$input['session']=  true;
$input['multiple'] = true;
$input['option'] = [
	'apple'     => 'Apple',
	'google'    => 'Google',
	'microsoft' => 'Microsoft',
];
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'agree';
$input['type']	 = 'checkbox';
$input['label']	 = 'Agree';
$input['cookie'] =  true;
$input['session']=  true;
$input['option'] = [
	'1' => 'I agree',
];
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;

//	...
$input = [];
$input['name']	 = 'button';
$input['type']	 = 'button';
$input['value']	 = 'Submit';
$form['input'][] = $input;

return $form;
