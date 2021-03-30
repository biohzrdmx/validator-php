<?php
	/**
	 * Validator
	 * @author 	biohzrdmx <github.com/biohzrdmx>
	 * @version 3.0
	 * @license MIT
	 */

	namespace Validator;

	use Validator\ValidationException;

	class Rule {

		/**
		 * Rule name
		 * @var string
		 */
		public $name;

		/**
		 * Rule value
		 * @var mixed
		 */
		public $value;

		/**
		 * Rule type
		 * @var string
		 */
		public $type;

		/**
		 * Rule options
		 * @var mixed
		 */
		public $opt;

		/**
		 * Constructor
		 * @param mixed  $name  Name of the rule
		 * @param mixed  $value Value of the rule
		 * @param string $type  Type of the rule
		 * @param mixed  $opt   Additional options for the rule
		 */
		function __construct($name, $value, $type, $opt = null) {
			$this->name = $name;
			$this->value = $value;
			$this->type = $type;
			$this->opt = $opt;
		}

		/**
		 * Check the rule
		 * @return boolean TRUE if the rule was satisfied, FALSE otherwise
		 */
		public function check() {
			$ret = false;
			$message = '';
			switch ( $this->type ) {
				case 'required':
					$ret = !empty( $this->value );
					$message = $ret ? '' : sprintf("Field '%s' is required", $this->name);
					break;
				case 'email':
					$pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";
					$ret = preg_match($pattern, $this->value ) == 1;
					$message = $ret ? '' : sprintf("'%s' must be a valid email address", $this->name);
					break;
				case 'regex':
					$pattern = $this->opt;
					$ret = preg_match($pattern, $this->value ) == 1;
					$message = $ret ? '' : sprintf("'%s' has an invalid format", $this->name);
					break;
				case 'equal':
					$ret = $this->value == $this->opt;
					$message = $ret ? '' : sprintf("'%s' must be equal", $this->name);
					break;
				case 'checkboxes':
					$ret = is_array( $this->value ) && count( $this->value ) > 0;
					$message = $ret ? '' : sprintf("You must select some elements for '%s'", $this->name);
					break;
				case 'at least':
					$ret = is_array( $this->value ) && count( $this->value ) >= $this->opt;
					$message = $ret ? '' : sprintf("You must select at least %d elements for '%s'", $this->opt, $this->name);
					break;
				case 'at most':
					$ret = is_array( $this->value ) && count( $this->value ) <= $this->opt;
					$message = $ret ? '' : sprintf("You must select at most %d elements for '%s'", $this->opt, $this->name);
					break;
				case 'custom':
					$ret = is_callable($this->opt) ? call_user_func($this->opt, $this->value) === true : false;
					break;
			}
			if (! $ret ) {
				throw new ValidationException($this, $message);
			}
			return $ret;
		}
	}

?>