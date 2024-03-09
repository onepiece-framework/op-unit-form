Validate
===

# Usage

  Add validate assoc.

```php
//  Init form config.
$form = [];
$form['name'] = 'testcase';

//  Init input config.
$input = [];
$input['name']	 = 'text';
$input['type']	 = 'text';
$input['label']	 = 'Text';
$input['cookie'] =  true;
$input['session']=  true;
$input['placeholder'] = 'Please input text';
$input['validate'] = [
	'required' => true,
];
$form['input'][] = $input;
```

  If you want to more validate option. See validate reference.
