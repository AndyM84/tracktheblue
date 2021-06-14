<?php

	namespace AndyM84\Config;

	/**
	 * Enumerated operators available
	 * for use in instructions.
	 *
	 * @version 1.0
	 * @author Andrew Male (AndyM84)
	 * @package AndyM84\Config
	 */
	class MigrationOperators implements \JsonSerializable {
		const ERROR = 0;
		const ADD = 1;
		const CHANGE = 2;
		const REMOVE = 3;
		const RENAME = 4;

		/**
		 * Name of set value.
		 *
		 * @var string
		 */
		protected $name = 'err';
		/**
		 * Value set for object.
		 *
		 * @var integer
		 */
		protected $value = self::ERROR;
		/**
		 * Static lookup for names to values.
		 *
		 * @var array
		 */
		protected static $lookup = array(
			'+' => self::ADD,
			'=' => self::CHANGE,
			'-' => self::REMOVE,
			'>' => self::RENAME
		);


		/**
		 * Static method to generate a new MigrationOperator
		 * object from its string representation.
		 *
		 * @param string $string String representation of an operator value.
		 * @return MigrationOperators
		 */
		public static function fromString($string) {
			$string = \mb_strtolower($string);

			foreach (static::$lookup as $str => $val) {
				if ($string === $str) {
					return new MigrationOperators($val);
				}
			}

			return new MigrationOperators(null);
		}

		/**
		 * Static method to determine if a given name is a
		 * valid string representation of a value.
		 *
		 * @param string $name String representation of an operator value.
		 * @return boolean
		 */
		public static function validName($name) {
			$name = \mb_strtolower($name);

			return array_key_exists($name, static::$lookup);
		}

		/**
		 * Static method to determine if a given integer
		 * is a valid operator value.
		 *
		 * @param integer $value Integer value to check as an operator value.
		 * @return boolean
		 */
		public static function validValue($value) {
			foreach (array_values(static::$lookup) as $validValue) {
				if ($validValue === $value) {
					return true;
				}
			}

			return false;
		}


		/**
		 * Instantiates a new MigrationOperators object.
		 *
		 * @param integer $value Integer value for the operator.
		 */
		public function __construct($value) {
			if ($value !== null) {
				foreach (static::$lookup as $name => $val) {
					if ($value === $val) {
						$this->name = $name;
						$this->value = $val;

						break;
					}
				}
			}

			return;
		}

		/**
		 * Converts a MigrationOperators object into the
		 * name of the operator.
		 *
		 * @return string
		 */
		public function __toString() {
			return $this->name;
		}

		/**
		 * Checks if the operator has the same value
		 * as the provided integer.
		 *
		 * @param integer $value Integer to compare operator value against.
		 * @return boolean
		 */
		public function is($value) {
			if ($this->value === $value) {
				return true;
			}

			return false;
		}

		/**
		 * Serializes the MigrationOperators object, returning
		 * the name string.
		 *
		 * @return string
		 */
		public function jsonSerialize() {
			return $this->name;
		}

		/**
		 * Retrieves the name of the object.
		 *
		 * @return string
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * Retrieves the value of the object.
		 *
		 * @return integer
		 */
		public function getValue() {
			return $this->value;
		}
	}
