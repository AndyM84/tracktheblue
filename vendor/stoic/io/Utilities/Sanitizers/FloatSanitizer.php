<?php

	namespace Stoic\Utilities\Sanitizers;

	/**
	 * Class FloatSanitizer
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class FloatSanitizer implements SanitizerInterface {
		/**
		 * Convert the supplied variable into a float value.
		 *
		 * @param mixed $input The input that will be sanitized to a float value.
		 * @throws \Exception
		 * @return float
		 */
		public function sanitize($input) : float {
			try {
				if (is_object($input)) {
					$props = get_object_vars($input);
					$value = count($props);
					$value = floatval($value);

				} else if (is_array($input)) {
					$value = count($input);
					$value = floatval($value);

					// We are removing the period from the input so that strings
					// which contain float values aren't processed as strings.
				} else if (is_string($input) && !ctype_digit(str_replace('.', '', $input))) {
					$value = strlen($input);
					$value = floatval($value);

				} else {
					$value = floatval($input);
				}
			// @codeCoverageIgnoreStart
			} catch (\Exception $ex) {
				throw $ex;
			}
			// @codeCoverageIgnoreEnd

			return $value;
		}
	}
