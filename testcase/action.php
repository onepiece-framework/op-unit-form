<?php
/** op-unit-form:/testcase/action.php
 *
 * @created   2019-03-01
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
 * @created    2024-03-09
 */
namespace OP\UNIT\FORM;

/* @var $app    \OP\UNIT\App  */

//	...
if(!$form = $app->Unit('Form') ){
	return;
};

//	...
$form->Config(__DIR__.'/config.inc.php');

//	...
include(__DIR__.'/action.phtml');
