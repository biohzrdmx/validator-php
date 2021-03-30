<?php
	/**
	 * Validator
	 * @author 	biohzrdmx <github.com/biohzrdmx>
	 * @version 3.0
	 * @license MIT
	 */

	namespace Validator;

	use Validator\ValidationException;
	use Validator\Rule;

	class Validator {

		/**
		 * Rules array
		 * @var array
		 */
		protected $rules;

		/**
		 * Errors array
		 * @var array
		 */
		protected $errors;

		/**
		 * Constructor
		 */
		function __construct() {
			$this->rules = [];
			$this->errors = [];
		}

		/**
		 * Factory
		 * @return Validator The Validator instance
		 */
		static function newInstance() {
			$new = new self();
			return $new;
		}

		/**
		 * Add a new validation rule
		 * @param string $name  Name of the rule
		 * @param mixed  $value Value of the variable that needs to be checked
		 * @param string $type  Type of rule: required|email|regex|equal|checkboxes|at least|at most, a callback of the name of a class that extends Rule
		 * @param mixed  $opt   Options for the specified rule-type
		 * @return $this        The Validator instance
		 */
		public function addRule($name, $value, $type = 'required', $opt = null) {
			$class = class_exists($type) ? $type : Rule::class;
			$this->rules[ $name ] = new $class($name, $value, $type, $opt);
			return $this;
		}

		/**
		 * Remove a validation rule
		 * @param  string $name Name of the rule
		 * @return $this        The Validator instance
		 */
		public function removeRule($name) {
			if ( isset( $this->rules[ $name ] ) ) {
				unset( $this->rules[ $name ] );
			}
			return $this;
		}

		/**
		 * Remove all the validation rules
		 * @return $this The Validator instance
		 */
		public function clearRules() {
			$this->rules = [];
			return $this;
		}

		/**
		 * Get the validation rules
		 * @return array Array of rules
		 */
		public function getRules() {
			return $this->rules;
		}

		/**
		 * Get a validation rule
		 * @param  string $name Name of the rule
		 * @return mixed        Rule object if it exists, FALSE otherwise
		 */
		public function getRule($name) {
			$ret = false;
			if ( isset( $this->rules[ $name ] ) ) {
				$ret = $this->rules[ $name ];
			}
			return $ret;
		}

		/**
		 * Check all the values against its associated rule type
		 * @return $this The Validator instance
		 */
		public function validate() {
			$this->errors = array();
			foreach ($this->rules as $name => $rule) {
				try {
					$rule->check();
				} catch (ValidationException $e) {
					$this->errors[] = (object) [
						'name' => $name,
						'message' => $e->getMessage()
					];
				}
			}
			return $this;
		}

		/**
		 * Helper function to determine whether the validation was successful or not
		 * @return boolean TRUE on success, FALSE otherwise
		 */
		public function isValid() {
			return count( $this->errors ) == 0;
		}

		/**
		 * Helper function to determine whether the validation was successful or not
		 * @return array Array of errors
		 */
		public function getErrors() {
			return $this->errors;
		}
	}

?>