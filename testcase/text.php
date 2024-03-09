<?php
/** op-unit-form:/testcase/text.php
 *
 * @created   2024-03-09
 * @version   1.0
 * @package   op-unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** Declare strict
 *
 */
declare(strict_types=1);

/** namespace
 *
 */
namespace OP\UNIT\FORM;

/* @var $form \OP\UNIT\Form  */
if(!$unit_form = OP()->Unit('Form') ){
	throw new \Exception("OP()->Unit('Form') is error.");
};

//	...
$form = [];
$form['name'] = 'form_name';

//	input type text
$form['input'][] = [
	'name' => 'text',
	'type' => 'text',
	'validate' => [
		'required' => true,
		'english'  => true,
	],
];

//	submit button
$form['input'][] = [
	'name'  => 'button',
	'type'  => 'button',
	'value' => 'Submit',
];

//	Set form config.
$unit_form->Config($form);

//	Do varidation
$unit_form->Validate();

//	...
echo $unit_form;
