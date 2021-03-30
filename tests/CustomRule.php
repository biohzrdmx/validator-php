<?php
	namespace Validator\Tests;

	use Validator\ValidationException;
	use Validator\Rule;

	class CustomRule extends Rule {

		/**
		 * Custom rule check handler
		 * @return boolean TRUE if the value is not empty and is not equal to 'bar', FALSE otherwise
		 */
		public function check() {
			$ret = !empty($this->value) && $this->value != 'bar';
			if (! $ret ) {
				$message = sprintf("'%s' must be non-empty and can not be 'bar'", $this->name);
				throw new ValidationException($this, $message);
			}
			return $ret;
		}
	}

?>