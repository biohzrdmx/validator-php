validator-php
=============

Server side form validation FTW

### Basic usage

First require `biohzrdmx/validator-php` with Composer.

Then import the namespace, create a new `Validator` instance, add your validation rules and call the `validate` method:

```php
# Import namespace
use Validator\Validator;

# Create a validator instance and add some rules
$validator = Validator::newInstance()
	->addRule('name', $name)
	->addRule('email', $email, 'email')
	->addRule('password', $password)
	->addRule('confirm', $confirm, 'equal', $password)
	->validate();

# And check the result
if (! $validator->isValid() ) {
	$errors = $validator->getErrors();
	foreach ($errors as $error) {
		echo $error->message;
	}
}
```

The `isValid` method will return `false` if something isn't right and `true` if all your rules passed.

In case of failure you may call the `getErrors` method which will return an array of objects with a `name` and a `message` field so that you can inform the user why the validation failed.

#### Adding rules

To add a validation rule you must call the `addRule` method, which has the following signature:

```php
addRule($name, $value, $type = 'required', $opt = null) { ... }
```

The `$name` parameter identifies the rule and will be used for error reporting, while the `$value` refers to the variable you want to check.

`$opt` allows you to pass extra data, depending on the value of `$type`, which is explained below.

#### Rule types

You may specify any of the following built-in rule types for `$type`:

- `required` - A required field, the rule will fail if it is `null` or empty
- `email` - An email field, checked against W3C's regular expression for `email` fields in browsers
- `regex` - A field checked against a regular expression, pass the expression in `$opt`
- `equal` - A field that must be equal to another, pass the other field's value in `$opt`
- `checkboxes` - Intended for checkboxes, for the rule to succeed, its value must be a non-empty `array`
- `at least` - Intended for checkboxes, for the rule to succeed, its value must be a non-empty `array` with _at least n_ items inside. Pass the desired quantity in `$opt`
- `at most` - Intended for checkboxes, for the rule to succeed, its value must be an `array` with _at most n_ items inside. Pass the desired quantity in `$opt`

#### Custom rules

There are two ways of adding custom rules, the first and easiest is to just pass a `Closure` as the rule `opt`:

```php
$validator->addRule('Name', $name, 'custom', function($value) {
	# We do not accept Homers
	return $name != 'Homer';
});
```

The function receives the `$value` and must return `true` or `false` as the result of your validation, anything that is not `true` will make the rule fail.

This is recommended for one-off validation rules.

The second way is intended for validation rules that you will use in more than one place and/or require a complex logic. To do so, you will need to create a class that extends `Rule` and implements the `check` method:

```php
class CustomRule extends Rule {

	public function check() {
		$ret = $name != 'Homer';
		if (! $ret ) {
			$message = sprintf('We do not accept Homers');
			throw new ValidationException($this, $message);
		}
		return $ret;
	}
}
```

Note that you must throw a `ValidationException` when the validation fails AND return either `true` or `false` as in the above case.

Now that you've created your rule class, just pass its name as the rule type:

```php
$validator->addRule('Name', $name, CustomRule::class);
```

As you can see, custom validation classes provide more control as you may also specify a failure message.

You may also pass an extra `$opt` parameter that the rule will store in its own `$opt` member.

### Licensing

This software is released under the MIT license.

Copyright © 2021 biohzrdmx

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

### Credits

**Lead coder:** biohzrdmx [github.com/biohzrdmx](http://github.com/biohzrdmx)