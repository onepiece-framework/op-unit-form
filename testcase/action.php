<?php
/**
 * module-testcase:/unit/form/action.php
 *
 * @creation  2019-03-01
 * @version   1.0
 * @package   module-testcase
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
/* @var $app    \OP\UNIT\App  */

//	...
if(!$form = $app->Unit('Form') ){
	return;
};

//	...
$form->Config(__DIR__.'/config.inc.php');

//	...
include(__DIR__.'/action.phtml');
