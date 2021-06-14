<?php

	namespace AndyM84\Config;

	/**
	 * Class that provides basic operations on
	 * configuration settings.
	 *
	 * @version 1.0
	 * @author Andrew Male (AndyM84)
	 * @package AndyM84\Config
	 */
	class ConfigContainer implements \JsonSerializable {
		/**
		 * Collection of configuration settings and their
		 * field types.
		 *
		 * @var array
		 */
		protected $schema = array();
		/**
		 * Collection of configuration settings and their
		 * values.
		 *
		 * @var array
		 */
		protected $settings = array();


		/**
		 * Instantiates a new ConfigContainer object.
		 *
		 * @param string $jsonString Optional JSON string to attempt pulling settings from.
		 */
		public function __construct($jsonString = null) {
			$data = null;

			if ($jsonString !== null) {
				$data = json_decode($jsonString, true);
			}

			if ($data === null || array_key_exists('schema', $data) === false || array_key_exists('settings', $data) === false) {
				return;
			}

			if (count($data['schema']) !== count($data['settings'])) {
				return;
			}

			foreach ($data['schema'] as $field => $type) {
				if (array_key_exists($field, $data['settings']) === false) {
					continue;
				}

				$this->schema[$field] = FieldTypes::fromString($type);
				$this->settings[$field] = $data['settings'][$field];
			}

			return;
		}

		/**
		 * Attempts to retrieve a setting.
		 *
		 * @param string $field Name of field to try retrieving.
		 * @param mixed $defaultValue Optional default value to use if setting not present.
		 * @return mixed
		 */
		public function get($field, $defaultValue = null) {
			if ($this->has($field)) {
				return $this->settings[$field];
			}

			return $defaultValue;
		}

		/**
		 * Retrieves the configuration schema.
		 *
		 * @return array
		 */
		public function getSchema() {
			return $this->schema;
		}

		/**
		 * Retrieves the configuration settings.
		 *
		 * @return array
		 */
		public function getSettings() {
			return $this->settings;
		}

		/**
		 * Retrieves the type of a specific setting, if
		 * possible.
		 *
		 * @param string $field String value of field name.
		 * @return FieldTypes
		 */
		public function getType($field) {
			if ($this->has($field)) {
				return $this->schema[$field];
			}

			return new FieldTypes(null);
		}

		/**
		 * Determines whether or not the setting
		 * exists within the configuration.
		 *
		 * @param string $field String value of field name.
		 * @return boolean
		 */
		public function has($field) {
			return array_key_exists($field, $this->schema) !== false && array_key_exists($field, $this->settings) !== false;
		}

		/**
		 * Converts the configuration object into
		 * a JSON serializable array.
		 *
		 * @return array
		 */
		public function jsonSerialize() {
			return array('schema' => $this->schema, 'settings' => $this->settings);
		}

		/**
		 * Attempts to remove a setting from the configuration.
		 *
		 * @param string $field String value of the field name.
		 * @throws \InvalidArgumentException Thrown if a non-existent field is provided.
		 * @return void
		 */
		public function remove($field) {
			if (!$this->has($field)) {
				throw new \InvalidArgumentException("Cannot remove a field that doesn't exist");
			}

			unset($this->schema[$field]);
			unset($this->settings[$field]);

			return;
		}

		/**
		 * Attempts to rename a setting in the configuration.
		 *
		 * @param string $oldField Current string value of the field name.
		 * @param string $newField New string value of the field name.
		 * @throws \InvalidArgumentException Thrown if a non-existent old field is provided.
		 * @return void
		 */
		public function rename($oldField, $newField) {
			if (!$this->has($oldField)) {
				throw new \InvalidArgumentException("Cannot rename a field that doesn't exist");
			}

			$this->schema[$newField] = $this->schema[$oldField];
			$this->settings[$newField] = $this->settings[$oldField];

			$this->remove($oldField);

			return;
		}

		/**
		 * Attempts to set a setting in the configuration.
		 *
		 * @param string $field String value of the field name.
		 * @param mixed $value Value to set field to in configuration.
		 * @param integer $type Integer value of field type, only used if field doesn't' already exist.
		 * @throws \InvalidArgumentException Thrown if provided with an invalid type.
		 * @return void
		 */
		public function set($field, $value, $type = null) {
			if (stripos($value, '${') !== false) {
				$replacements = array();

				foreach ($this->settings as $key => $val) {
					$replacements["\${{$key}}"] = $val;
				}

				if (count($replacements) > 0) {
					$value = str_replace(array_keys($replacements), array_values($replacements), $value);
				}
			}

			if (!$this->has($field)) {
				if (FieldTypes::validValue($type) === false) {
					throw new \InvalidArgumentException("Invalid type given for new setting");
				}

				$this->schema[$field] = new FieldTypes($type);
			}

			switch ($this->schema[$field]->getValue()) {
				case FieldTypes::BOOLEAN:
					$this->settings[$field] = boolval($value);

					break;
				case FieldTypes::FLOAT:
					$this->settings[$field] = floatval($value);

					break;
				case FieldTypes::INTEGER:
					$this->settings[$field] = intval($value);

					break;
				case FieldTypes::STRING:
					$this->settings[$field] = "{$value}";

					break;
				// @codeCoverageIgnoreStart
				default:

					break;
				// @codeCoverageIgnoreEnd
			}

			return;
		}
	}
