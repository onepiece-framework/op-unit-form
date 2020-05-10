Error
===

 Hot to display error message.

```php
$form->Error('input_name');
```

 Hot to configuration.

```php
$form['input']['error'] = '$Name is $Rule error. ($label, $name, $rule, $value)';
$form['input']['nickname']['error] = 'Nickname is $Rule error. ($value)';
$form['input']['nickname']['errors']['required'] = 'This is required error.'
```
