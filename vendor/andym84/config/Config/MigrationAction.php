<?php

	namespace AndyM84\Config;

	/**
	 * Class that repsents a single action from
	 * an instruction file.
	 *
	 * @version 1.0
	 * @author Andrew Male (AndyM84)
	 * @package AndyM84\Config
	 */
	class MigrationAction {
		/**
		 * The name of the field the action will
		 * be performed upon.
		 *
		 * @var string
		 */
		public $field;
		/**
		 * The operator representing the operation
		 * which will be performed.
		 *
		 * @var MigrationOperators
		 */
		public $operator;
		/**
		 * The type of the field, if provided.
		 *
		 * @var FieldTypes
		 */
		public $type;
		/**
		 * The value of the field, if provided.
		 *
		 * @var mixed
		 */
		public $value;


		/**
		 * Instantiates a new MigrationAction object, parsing
		 * the provided instruction line into its pieces.
		 *
		 * @param string $string String value of line from instruction file.
		 * @throws \InvalidArgumentException Thrown if field is the reserved 'configVersion' field or if a non-REMOVE action is missing its value.
		 */
		public function __construct($string) {
			$field = substr($string, 0, stripos($string, ' '));
			
			if (stripos($field, '[') !== false) {
				$this->field = substr($field, 0, stripos($field, '['));
				$this->type = FieldTypes::fromString(substr($field, stripos($field, '[') + 1, 3));
			} else {
				$this->field = $field;
			}

			if (strtolower($this->field) == 'configversion') {
				throw new \InvalidArgumentException("Cannot access or modify the configVersion setting");
			}

			$this->operator = MigrationOperators::fromString(substr($string, strlen($field) + 1, 1));

			if (strlen($string) > (strlen($field) + 3)) {
				$this->value = substr($string, strlen($field) + 3);
			}

			if ($this->operator->getValue() < 3 && $this->value === null) {
				throw new \InvalidArgumentException("Non-REMOVE action without a value");
			}

			if ($this->value == '""') {
				$this->value = '';
			}

			return;
		}
	}
