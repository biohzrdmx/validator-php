<?php
	/**
	 * Validator
	 * @author 	biohzrdmx <github.com/biohzrdmx>
	 * @version 3.0
	 * @license MIT
	 */

	namespace Validator;

	use Validator\Rule;

	class ValidationException extends \Exception {

		/**
		 * Rule object
		 * @var Rule
		 */
		protected $rule;

		/**
		 * Constructor
		 * @param Rule       $rule     The failing rule
		 * @param string     $message  Exception message
		 * @param integer    $code     Exception code
		 * @param \Exception $previous Previous exception
		 */
		function __construct($rule, $message = '', $code = 0, $previous = null) {
			$this->rule = $rule;
			$message = $message ?: sprintf("Invalid value for '%s' field", $rule->type);
			parent::__construct($message, $code, $previous);
		}

		/**
		 * Get the failing rule
		 * @return Rule The Rule object
		 */
		public function getRule() {
			return $this->rule;
		}
	}

?>