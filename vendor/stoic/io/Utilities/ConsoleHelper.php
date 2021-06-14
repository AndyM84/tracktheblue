<?php

	namespace Stoic\Utilities;

	use Stoic\Utilities\ReturnHelper;

	/**
	 * ConsoleHelper class to aid with CLI interactions.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class ConsoleHelper {
		const ARGINFO_ARGA = 'arga';
		const ARGINFO_ARGACS = 'arga_cs';
		const ARGINFO_ARGC = 'argc';
		const ARGINFO_ARGV = 'argv';


		/**
		 * Collection of arguments.
		 *
		 * @var array
		 */
		private $argInfo = [];
		/**
		 * Whether or not executing environment
		 * is Windows based.
		 *
		 * @var bool
		 */
		private $isWindows = false;
		/**
		 * Allows overriding instances to think
		 * they were called from CLI PHP.
		 *
		 * @var bool
		 */
		private $forceCli = false;
		/**
		 * Attempts to represent the name of the
		 * executed script based on the argv values.
		 *
		 * @var mixed
		 */
		private $self = null;


		/**
		 * Creates a new ConsoleHelper instance.
		 *
		 * @param array $argv Argument collection.
		 * @param boolean $forceCli Force instance to emulate CLI mode.
		 * @return void
		 */
		public function __construct(array $argv = null, bool $forceCli = false) {
			$this->argInfo = array(
				self::ARGINFO_ARGC => 0,
				self::ARGINFO_ARGV => [],
				self::ARGINFO_ARGA => [],
				self::ARGINFO_ARGACS => []
			);

			if ($argv !== null) {
				$this->self = array_shift($argv);
				$this->argInfo[self::ARGINFO_ARGC] = count($argv);
				$this->argInfo[self::ARGINFO_ARGV] = $argv;

				if ($this->argInfo[self::ARGINFO_ARGC] > 0) {
					$this->argInfo[self::ARGINFO_ARGA] = $this->parseParams($argv, true);
					$this->argInfo[self::ARGINFO_ARGACS] = $this->parseParams($argv);
				} else {
					$this->argInfo[self::ARGINFO_ARGA] = array();
					$this->argInfo[self::ARGINFO_ARGACS] = array();
				}
			}

			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$this->isWindows = true;
			}

			$this->forceCli = $forceCli;

			return;
		}

		/**
		 * Compares an argument by key optionally
		 * without case sensitivity. May return
		 * inaccurate results against toggle type
		 * arguments.
		 *
		 * @param string $key String value of key in argument list.
		 * @param string $value Value to compare against.
		 * @param boolean $caseInsensitive Enable case-insensitive comparison.
		 * @return boolean
		 */
		public function compareArg(string $key, string $value, bool $caseInsensitive = false) : bool {
			$key = ($caseInsensitive) ? strtolower($key) : $key;
			$value = ($caseInsensitive) ? strtolower($value) : $value;
			$collection = ($caseInsensitive) ? self::ARGINFO_ARGA : self::ARGINFO_ARGACS;

			if (count($this->argInfo[$collection]) < 1 || !array_key_exists($key, $this->argInfo[$collection])) {
				return false;
			}

			$comparison = ($caseInsensitive) ? strtolower($this->argInfo[$collection][$key]) : $this->argInfo[$collection][$key];

			return $comparison == $value;
		}

		/**
		 * Compares an argument at the given index
		 * optionally without case sensitivity.
		 * Returns false if index is out of bounds.
		 *
		 * @param int $index Integer value for argument offset.
		 * @param string $value String value to compare against.
		 * @param boolean $caseInsensitive Enable case-insensitive comparison.
		 * @return boolean
		 */
		public function compareArgAt(int $index, string $value, bool $caseInsensitive = false) : bool {
			if ($this->argInfo[self::ARGINFO_ARGC] <= $index) {
				return false;
			}

			return ($caseInsensitive) ? strtolower($this->argInfo[self::ARGINFO_ARGV][$index]) == strtolower($value) : $this->argInfo[self::ARGINFO_ARGV][$index] == $value;
		}

		/**
		 * Retrieves $characters from STDIN.
		 *
		 * @codeCoverageIgnore
		 * @param int $characters Number of characters to read from STDIN.
		 * @return string|null
		 */
		public function get(int $characters = 1) : ?string {
			if ($characters < 1) {
				return null;
			}

			return trim(fread(STDIN, $characters));
		}

		/**
		 * Retrieves an entire line from STDIN.
		 *
		 * @codeCoverageIgnore
		 * @return string
		 */
		public function getLine() : string {
			return trim(fgets(STDIN));
		}

		/**
		 * Attempts to retrieve an argument by both short and long
		 * versions, otherwise returns a default value that is
		 * optionally provided.
		 *
		 * @param string $short String value that represents the short name of the parameter.
		 * @param string $long String value that represents the long name of the parameter.
		 * @param mixed $default Default value if parameter not present, set to null if not provided.
		 * @param boolean $caseInsensitive Optional toggle to make comparisons case-insensitive (sensitive by default).
		 * @return mixed
		 */
		public function getParameterWithDefault(string $short, string $long, $default = null, bool $caseInsensitive = false) {
			$collection = ($caseInsensitive) ? self::ARGINFO_ARGA : self::ARGINFO_ARGACS;
			$short = ($caseInsensitive) ? strtolower($short) : $short;
			$long = ($caseInsensitive) ? strtolower($long) : $long;

			if (array_key_exists($short, $this->argInfo[$collection]) || array_key_exists($long, $this->argInfo[$collection])) {
				return (array_key_exists($short, $this->argInfo[$collection])) ? $this->argInfo[$collection][$short] : $this->argInfo[$collection][$long];
			}

			return $default;
		}

		/**
		 * Queries a user repeatedly for input.
		 *
		 * @codeCoverageIgnore
		 * @param string $uery Base prompt, sans-colon.
		 * @param mixed $defaultValue Default value for input, provide null if not present.
		 * @param string $errorMessage Message to display when input not provided correctly.
		 * @param int $maxTries Maximum number of attempts a user can make before the process bails out.
		 * @param callable $validation An optional method or function to provide boolean validation of input.
		 * @param callable $sanitation An optional method or function to provide sanitation of the validated input.
		 * @return \Stoic\Utilities\ReturnHelper
		 */
		public function getQueriedInput(string $query, $defaultValue, string $errorMessage, int $maxTries = 5, ?callable $validation = null, ?callable $sanitation = null) : ReturnHelper {
			$Ret = new ReturnHelper();
			$Prompt = $query;

			if ($defaultValue !== null) {
				$Prompt .= " [{$defaultValue}]";
			}

			$Prompt .= ": ";

			if ($validation === null) {
				$validation = function ($Value) { return !empty(trim($Value)); };
			}

			if ($sanitation === null) {
				$sanitation = function ($Value) { return trim($Value); };
			}

			$Attempts = 0;

			while (true) {
				$this->put($Prompt);
				$Val = $this->getLine();

				if (empty($Val) && $defaultValue !== null) {
					$Ret->makeGood();
					$Ret->addResult($sanitation($defaultValue));

					break;
				}

				$valid = $validation($Val);

				if (($valid instanceof ReturnHelper && $valid->isGood()) || $valid) {
					$Sanitized = $sanitation($Val);
					$Ret->makeGood();

					if ($Sanitized instanceof ReturnHelper) {
						$Ret = $Sanitized;
					} else {
						$Ret->addResult($Sanitized);
					}

					break;
				} else {
					if ($valid instanceof ReturnHelper && $valid->hasMessages()) {
						$this->putLine("** {$errorMessage}" . " (" . $valid->getMessages()[0] . ")");
					} else {
						$this->putLine("** {$errorMessage}");
					}

					$Attempts++;

					if ($Attempts >= $maxTries) {
						$Ret->addMessage("Exceeded maximum number of attempts.");

						break;
					}
				}
			}

			return $Ret;
		}

		/**
		 * Returns the script being called according to the passed
		 * arguments (first argument in $argv).
		 *
		 * @return mixed
		 */
		public function getSelf() {
			return $this->self;
		}

		/**
		 * Checks if the given key exists in the argument list,
		 * optionally without case sensitivity.
		 *
		 * @param string $key Key name to check in argument list.
		 * @param boolean $caseInsensitive Enable case-insensitive comparison.
		 * @return boolean
		 */
		public function hasArg(string $key, bool $caseInsensitive = false) : bool {
			if ($this->argInfo[self::ARGINFO_ARGC] < 1) {
				return false;
			}

			$collection = ($caseInsensitive) ? self::ARGINFO_ARGA : self::ARGINFO_ARGACS;
			$key = ($caseInsensitive) ? strtolower($key) : $key;

			return array_key_exists($key, $this->argInfo[$collection]);
		}

		/**
		 * Checks if the given key exists in the argument list,
		 * using both a short and long version of the key,
		 * optionally without case sensitivity.
		 *
		 * @param string $short Short version of key name to check in argument list.
		 * @param string $long Long version of key name to check in argument list.
		 * @param boolean $caseInsensitive Enable case-insensitive comparison.
		 * @return boolean
		 */
		public function hasShortLongArg(string $short, string $long, bool $caseInsensitive = false) : bool {
			if ($this->argInfo[self::ARGINFO_ARGC] < 1) {
				return false;
			}

			$collection = ($caseInsensitive) ? self::ARGINFO_ARGA : self::ARGINFO_ARGACS;
			$short = ($caseInsensitive) ? strtolower($short) : $short;
			$long = ($caseInsensitive) ? strtolower($long) : $long;

			return array_key_exists($short, $this->argInfo[$collection]) || array_key_exists($long, $this->argInfo[$collection]);
		}

		/**
		 * Returns whether or not PHP invocation is via CLI
		 * or invocation is emulating CLI.
		 *
		 * @return boolean
		 */
		public function isCLI() : bool {
			return $this->forceCli || PHP_SAPI == 'cli';
		}

		/**
		 * Returns whether or not PHP invocation is via CLI
		 * and ignores forced CLI mode.
		 *
		 * @return boolean
		 */
		public function isNaturalCLI() : bool {
			return PHP_SAPI == 'cli';
		}

		/**
		 * Returns the number of arguments.
		 *
		 * @return int Number of arguments supplied to the instance.
		 */
		public function numArgs() : int {
			return $this->argInfo[self::ARGINFO_ARGC];
		}

		/**
		 * Returns the argument collection, either
		 * as-received by the instance or as an
		 * associative array.
		 *
		 * @param boolean $asAssociative Enables returning list as an associative array.
		 * @param boolean $caseSensitive Enables the return of case insensitive associative arrays.
		 * @return array
		 */
		public function parameters(bool $asAssociative = false, bool $caseSensitive = false) {
			$assocIndex = ($caseSensitive) ? self::ARGINFO_ARGACS : self::ARGINFO_ARGA;

			return ($asAssociative) ? $this->argInfo[$assocIndex] : $this->argInfo[self::ARGINFO_ARGV];
		}

		/**
		 * Parses a collection of arguments into an organized
		 * collection.
		 *
		 * Pairs of arguments are put together while toggle
		 * elements (ie, -enable) are given a value of true.
		 * Case sensitivity can be optionally disabled.
		 *
		 * @param array $args Array of arguments to parse.
		 * @param boolean $caseInsensitive Optional argument to disable case sensitivity in resulting array.
		 * @return array
		 */
		protected function parseParams(array $args, bool $caseInsensitive = false) {
			$len = count($args);
			$assoc = array();

			for ($i = 0; $i < $len; ++$i) {
				if ($args[$i][0] == '-' && strlen($args[$i]) > 1) {
					$key = substr($args[$i], ($args[$i][1] == '-') ? 2 : 1);

					if (stripos($key, '=') !== false && strpos($key, '=') != (strlen($key) - 1)) {
						$parts = explode('=', $key, 2);
						$assoc[($caseInsensitive) ? strtolower($parts[0]) : $parts[0]] = $parts[1];
					} else if (($i + 1) < $len) {
						$assoc[($caseInsensitive) ? strtolower($key) : $key] = ($args[$i + 1][0] != '-') ? $args[++$i] : true;
					} else {
						$assoc[($caseInsensitive) ? strtolower($key) : $key] = true;
					}
				} else {
					if (stripos($args[$i], '=') !== false) {
						$parts = explode('=', $args[$i], 2);
						$assoc[($caseInsensitive) ? strtolower($parts[0]) : $parts[0]] = $parts[1];
					} else {
						$assoc[($caseInsensitive) ? strtolower($args[$i]) : $args[$i]] = true;
					}
				}
			}

			return $assoc;
		}

		/**
		 * Outputs the buffer to STDIN.
		 *
		 * @param string $buf Buffer to output.
		 * @return void
		 */
		public function put(string $buf) : void {
			echo($buf);

			return;
		}

		/**
		 * Outputs the buffer followed by a newline
		 * to STDIN.
		 *
		 * @param string $buf Buffer to output.
		 * @return void
		 */
		public function putLine(?string $buf = null) : void {
			if ($buf !== null) {
				echo($buf);
			}

			echo("\n");

			return;
		}
	}
