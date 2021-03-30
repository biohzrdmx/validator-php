<?php
	namespace Validator\Tests;

	use PHPUnit\Framework\TestCase;
	use Validator\Tests\CustomRule;
	use Validator\ValidationException;
	use Validator\Validator;
	use Validator\Rule;

	class RulesTest extends TestCase {

		public function testFactory() {
			# Test instantiation
			$validator = Validator::newInstance();
			$this->assertInstanceOf(Validator::class, $validator);
		}

		public function testAddRule() {
			$value = 'foo';
			$validator = Validator::newInstance()->addRule('value', $value, 'required');
			$this->assertEquals(1, count( $validator->getRules() ));
		}

		public function testRemoveRule() {
			$value = 'foo';
			$validator = Validator::newInstance()->addRule('value', $value, 'required')->removeRule('value');
			$this->assertEquals(0, count( $validator->getRules() ));
		}

		public function testClearRules() {
			$value = 'foo';
			$validator = Validator::newInstance()->addRule('value', $value, 'required')->clearRules();
			$this->assertEquals(0, count( $validator->getRules() ));
		}

		public function testGetRules() {
			$value = 'foo';
			$validator = Validator::newInstance()->addRule('value', $value, 'required');
			$this->assertIsArray($validator->getRules());
			$this->assertEquals(1, count( $validator->getRules() ));
		}

		public function testGetRule() {
			$value = 'foo';
			$validator = Validator::newInstance()->addRule('value', $value, 'required');
			$this->assertInstanceOf(Rule::class, $validator->getRule('value'));
		}

		public function testGetErrors() {
			$value = '';
			$validator = Validator::newInstance()->addRule('value', $value, 'required');
			$validator->validate();
			$errors = $validator->getErrors();
			$this->assertIsArray($errors);
		}

		public function testExceptionGetRule() {
			$value = 'foo';
			$validator = Validator::newInstance()->addRule('value', $value, 'required');
			$rule = $validator->getRule('value');
			$rule_from_exception = null;
			try {
				throw new ValidationException($rule, 'Testing');
			} catch (ValidationException $e) {
				$rule_from_exception = $e->getRule();
			}
			$this->assertInstanceOf(Rule::class, $rule_from_exception);
		}

		public function testRequiredRule() {
			# Create instance
			$validator = Validator::newInstance();
			# Try with a valid value
			$value = 'foo';
			$validator->addRule('value', $value, 'required')->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = '';
			$validator->addRule('value', $value, 'required')->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testEmailRule() {
			# Create instance
			$validator = Validator::newInstance();
			# Try with a simple domain
			$value = 'test@example.com';
			$validator->addRule('value', $value, 'email')->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with a subdomain
			$value = 'test@subdomain.example.com';
			$validator->addRule('value', $value, 'email')->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = '@foo.bar';
			$validator->addRule('value', $value, 'email')->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testRegexRule() {
			# Create instance
			$validator = Validator::newInstance();
			# Create the regular expression
			$expr = '/\d{1,2}-\d{1,2}-\d{4}/';
			# Try with a valid value
			$value = '01-01-2000';
			$validator->addRule('value', $value, 'regex', $expr)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = 'aa-bb-cccc';
			$validator->addRule('value', $value, 'regex', $expr)->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testEqualRule() {
			# Create instance
			$validator = Validator::newInstance();
			# Create the other expression
			$other = 'foo';
			# Try with a valid value
			$value = 'foo';
			$validator->addRule('value', $value, 'equal', $other)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = 'bar';
			$validator->addRule('value', $value, 'equal', $other)->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testCheckboxesRule() {
			# Create instance
			$validator = Validator::newInstance();
			# Try with a valid value
			$value = ['foo', 'bar', 'baz'];
			$validator->addRule('value', $value, 'checkboxes')->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = [];
			$validator->addRule('value', $value, 'checkboxes')->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testAtLeastRule() {
			# Create instance
			$validator = Validator::newInstance();
			$threshold = 2;
			# Try with the exact value
			$value = ['foo', 'bar'];
			$validator->addRule('value', $value, 'at least', $threshold)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with more than required
			$value = ['foo', 'bar', 'baz'];
			$validator->addRule('value', $value, 'at least', $threshold)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with less than required
			$value = ['foo'];
			$validator->addRule('value', $value, 'at least', $threshold)->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testAtMostRule() {
			# Create instance
			$validator = Validator::newInstance();
			$threshold = 2;
			# Try with the exact value
			$value = ['foo', 'bar'];
			$validator->addRule('value', $value, 'at most', $threshold)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with more than required
			$value = ['foo', 'bar', 'baz'];
			$validator->addRule('value', $value, 'at most', $threshold)->validate();
			$this->assertEquals( false, $validator->isValid() );
			# Try with less than required
			$value = ['foo'];
			$validator->addRule('value', $value, 'at most', $threshold)->validate();
			$this->assertEquals( true, $validator->isValid() );
		}

		public function testCustomRule() {
			# Create instance
			$validator = Validator::newInstance();
			# The handler function, returns TRUE for everything that is not empty or 'bar'
			$handler = function($value) {
				return !empty($value) && $value != 'bar';
			};
			# Try with a valid value
			$value = 'foo';
			$validator->addRule('value', $value, 'custom', $handler)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = 'bar';
			$validator->addRule('value', $value, 'custom', $handler)->validate();
			$this->assertEquals( false, $validator->isValid() );
		}

		public function testCustomRuleClass() {
			# Create instance
			$validator = Validator::newInstance();
			# Try with a valid value
			$value = 'foo';
			$validator->addRule('value', $value, CustomRule::class)->validate();
			$this->assertEquals( true, $validator->isValid() );
			# Try with an invalid value
			$value = 'bar';
			$validator->addRule('value', $value, CustomRule::class)->validate();
			$this->assertEquals( false, $validator->isValid() );
		}
	}

?>