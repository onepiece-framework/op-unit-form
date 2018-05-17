The Unit of Form of onepiece-framework
===

## How to use

```form.php
<?php
//	Instantiate.
$form = Unit::Instance('Form');

//	Load of configuration file.
$form->Config('config.php');

//	Validation.
$result = Validation::Validate($form);

//	Display form tag.
$form->Start();

//	Display input name.
$form->Label('input_name');

//	Display input tag.
$form->Input('input_name');

//	Display error message.
$form->Error('input_name');

//	Display close form tag.
$form->Finish();

//	Result.
if( $result ){
	//	Clear saved value.
	$form->Clear();
}
```

## How to define configuration file

```form.php
<?php

//	...
$form = [];
$form['name'] = 'testcase';

//	...
$name  = 'text';
$input = [];
$input['label']  = 'Text';
$input['name']   = $name;
$input['type']   = 'text';
$form['input'][$name] = $input;

//	...
$name  = 'textarea';
$input = [];
$input['label']  = 'Textarea';
$input['name']   = $name;
$input['type']   = 'textarea';
$form['input'][$name] = $input;

//	...
$name  = 'select';
$input = [];
$input['label']  = 'Select';
$input['name']   = $name;
$input['type']   = 'select';
$input['option'] = ['','Android','Blackberry','Cymbian'];
$form['input'][$name] = $input;

//	...
$name  = 'multiple';
$input = [];
$input['label']  = 'Multiple';
$input['name']   = $name;
$input['type']   = 'select';
$input['multiple'] = true;
$input['option'] = ['a'=>'Android','b'=>'Blackberry','c'=>'Cymbian'];
$form['input'][$name] = $input;

//	...
$name  = 'radio';
$input = [];
$input['label']  = 'Radio';
$input['name']   = $name;
$input['type']   = 'radio';
$input['option'] = ['a'=>'Android','b'=>'Blackberry','c'=>'Cymbian'];
$form['input'][$name] = $input;

//	...
$name  = 'checkbox';
$input = [];
$input['label']  = 'Checkbox';
$input['name']   = $name;
$input['type']   = 'checkbox';
$input['option'] = ['a'=>'Android','b'=>'Blackberry','c'=>'Cymbian'];
$form['input'][$name] = $input;

//	...
$name  = 'cookie';
$input = [];
$input['label']  = 'Cookie';
$input['name']   = $name;
$input['type']   = 'checkbox';
$input['cookie'] = true;
$input['option'] = ['agree'=>'Save to cookie. (Cross over sessions)'];
$form['input'][$name] = $input;

//	...
$name  = 'session';
$input = [];
$input['label']  = 'Session';
$input['name']   = $name;
$input['type']   = 'checkbox';
$input['session']= false;
$input['option'] = ['agree'=>'Does not save value. (Do not save to session)'];
$form['input'][$name] = $input;

//	...
$name  = 'file';
$input = [];
$input['label']	 = 'File has not been supported.';
$input['name']	 = $name;
$input['type']	 = 'file';
$form['input'][$name] = $input;

//	...
return $form;
```
