<?php
/** op-unit-form:/include/toString.php
 *
 * @created   2021-01-21
 * @version   1.0
 * @package   op-unit-form
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/* @var $this  OP\UNIT\Form */
$this->isValidate();

//	...
$this->Start();

//	...
echo '<div class="table">';
foreach( $this->Config()['input'] as $name => $input ){
	echo '<div class="tr">';
	echo '<div class="th">';
	if( $input['type'] === 'button' or $input['type'] === 'submit' ){
		//	...
	}else{
		$this->Label($name);
	}
	echo '</div>';
	echo '<div class="td">';
	$this->Input($name); echo ' ';
	$this->Error($name);
	echo '</div>';
	echo '</div>';
}
echo '</div>';

//	...
$this->Finish();
