<?php

	namespace Stoic\Utilities;

	/**
	 * Class to optionally configure StringHelper::join()
	 * calls.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class StringJoinOptions {
		/**
		 * Glue to use when joining strings.
		 *
		 * @var string
		 */
		public $glue;
		/**
		 * Whether or not to guard against
		 * glue duplicates when joining.
		 *
		 * @var boolean
		 */
		public $guardGlue = false;


		/**
		 * Instantiates a new StringJoinOptions object.
		 *
		 * @param string $glue String value to use as glue between tokens.
		 * @param boolean $guardGlue Whether or not to discourage duplication of glue string.
		 */
		public function __construct(string $glue, bool $guardGlue) {
			$this->glue = $glue;
			$this->guardGlue = $guardGlue;

			return;
		}
	}

	/**
	 * Class to provide some basic helper methods for
	 * working with strings.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class StringHelper {
		const CMP_STRPOS = 'strpos';
		const CMP_STRIPOS = 'stripos';


		/**
		 * Internal length cache.
		 *
		 * @var integer
		 */
		private $_length = 0;
		/**
		 * Internal data store for string.
		 *
		 * @var null|string
		 */
		private $_data = null;


		/**
		 * Instantiates a new StringHelper object with
		 * an optional source string.
		 *
		 * @param mixed $source Optional source string to initialize internal data store.
		 */
		public function __construct($source = null) {
			$this->_data = $source;
			$this->_length = strlen($this->_data);

			return;
		}

		/**
		 * Appends a string onto the internal data
		 * store or initializes the store if empty.
		 *
		 * @param string|StringHelper $string String to append to (or initialize as) internal data store.
		 * @return void
		 */
		public function append($string) : void {
			if ($string instanceof StringHelper) {
				$string = $string->data();
			}

			if ($this->_data === null) {
				$this->_data = $string;
			} else {
				$this->_data .= $string;
			}

			$this->_length = strlen($this->_data);

			return;
		}

		/**
		 * Get character in string.
		 *
		 * @param integer $position Value of position to retrieve character within string.
		 * @return string
		 */
		public function at(int $position) : string {
			if ($this->_data === null || $position < 0 || $position > $this->_length) {
				throw new \OutOfRangeException("Position is out of the bounds of the internal data store");
			}

			return $this->_data[$position];
		}

		/**
		 * Erases the contents of the internal data store.
		 *
		 * @return void
		 */
		public function clear() : void {
			$this->_data = null;
			$this->_length = 0;

			return;
		}

		/**
		 * Binary safe comparison of two strings, with optional
		 * specifier for comparing the first $length characters.
		 *
		 * @param string|StringHelper $string Another string (or string object) to compare with.
		 * @param null|integer $length Optional position of first character in internal data store to use for comparison.
		 * @param boolean $caseInsensitive Optional toggle for case sensitivity in comparison, defaults to sensitive.
		 * @return boolean|integer
		 */
		public function compare($string, ?int $length = null, bool $caseInsensitive = false) {
			if ($this->_data === null) {
				return false;
			}

			if ($length !== null) {
				if ($length >= $this->_length || $length >= strlen($string)) {
					return false;
				}

				$findFunction = ($caseInsensitive === false) ? 'strncmp' : 'strncasecmp';

				return call_user_func($findFunction, $this->_data, $string, $length);
			}

			$findFunction = ($caseInsensitive === false) ? 'strcmp' : 'strcasecmp';

			return call_user_func($findFunction, $this->_data, $string);
		}

		/**
		 * Provides a copy of the StringHelper object.
		 *
		 * @return StringHelper
		 */
		public function copy() : StringHelper {
			return clone $this;
		}

		/**
		 * Provides the internal data store.
		 *
		 * @return null|string
		 */
		public function data() : ?string {
			return $this->_data;
		}

		/**
		 * Determines if the string ends with the provided
		 * string.
		 *
		 * @param string|StringHelper $string String value to look for at end of internal data store.
		 * @param boolean $caseInsensitive Optional toggle for case sensitivity of comparison.
		 * @return boolean
		 */
		public function endsWith($string, bool $caseInsensitive = false) : bool {
			$length = strlen($string);
			$endWord = substr($this->_data, -($length + 1));
			$cmpFunction = ($caseInsensitive === false) ? self::CMP_STRPOS : self::CMP_STRIPOS;

			return call_user_func($cmpFunction, $endWord, $string) !== false;
		}

		/**
		 * Attempts to find a string within the internal data store,
		 * with optional position.
		 *
		 * @param string|StringHelper $string Another string (or string object) to find within store.
		 * @param null|integer $position Optional position of first character in internal data store to use for searching.
		 * @param boolean $caseInsensitive Optional toggle for case sensitivity in searching, defaults to sensitive.
		 * @return boolean|integer
		 */
		public function find($string, ?int $position = null, bool $caseInsensitive = false) {
			if ($this->_data === null) {
				return false;
			}

			$findFunction = ($caseInsensitive === false) ? self::CMP_STRPOS : self::CMP_STRIPOS;

			if ($position !== null) {
				if ($position >= $this->_length || $position >= strlen($string)) {
					return false;
				}

				return call_user_func($findFunction, $this->_data, $string, $position);
			}

			return call_user_func($findFunction, $this->_data, $string);
		}

		/**
		 * Retrieves the first character in the
		 * internal data store.
		 *
		 * @return null|string
		 */
		public function firstChar() : ?string {
			if ($this->_data === null) {
				return null;
			}

			return $this->_data[0];
		}

		/**
		 * Returns true if the internal data store is
		 * null or empty.
		 *
		 * @return boolean
		 */
		public function isEmptyOrNull() : bool {
			return $this->_data === null || $this->_length == 0;
		}

		/**
		 * Returns true if the internal data store is
		 * null, empty, or comprised of only whitespace.
		 *
		 * @return boolean
		 */
		public function isEmptyOrNullOrWhitespace() : bool {
			return $this->_data === null || $this->_length == 0 || ctype_space($this->_data);
		}

		/**
		 * Retrieves the last character in the internal
		 * data store.
		 *
		 * @return null|string
		 */
		public function lastChar() : ?string {
			if ($this->_data === null) {
				return null;
			}

			return $this->_data[$this->_length - 1];
		}

		/**
		 * Returns the length of the internal data store.
		 *
		 * @return integer
		 */
		public function length() : int {
			return $this->_length;
		}

		/**
		 * Replaces all occurrences of the search string with
		 * the replacement string.
		 *
		 * @param mixed $search Value being searched for, otherwise known as the needle.  An array may be used to designate multiple needles.
		 * @param mixed $replace The replacement value that replaces found search values.  An array may be used to designate multiple replacements.
		 * @param mixed $count Optional parameter to hold the number of replacements performed.
		 * @return void
		 */
		public function replace($search, $replace, &$count = null) : void {
			if ($count !== null) {
				$this->_data = str_replace($search, $replace, $this->_data, $count);

				return;
			}

			$this->_data = str_replace($search, $replace, $this->_data);

			return;
		}

		/**
		 * Finds and replaces text contained within the $start
		 * and $end tags with the $replace sequence.  Use of
		 * the text between $start and $end is signified in the
		 * $replace string using the string '%TEXT%'.
		 *
		 * @param string|StringHelper $start Beginning tag surrounding text.
		 * @param string|StringHelper $end Ending tag surrounding text.
		 * @param string|StringHelper $replace String to replace tags and inner text (reference inner text with %TEXT%).
		 * @param mixed $caseInsensitive Optional toggle of case sensitivity for searching.
		 * @return boolean
		 */
		public function replaceContained($start, $end, $replace, $caseInsensitive = false) : bool {
			$offset = 0;
			$endLen = strlen($end);
			$startLen = strlen($start);
			$findFunction = ($caseInsensitive === false) ? self::CMP_STRPOS : self::CMP_STRIPOS;

			if ($this->_length <= ($startLen + $endLen)) {
				return false;
			}

			while ($offset < (strlen($this->_data) - ($startLen + $endLen))) {
				$s = array(
					'spos' => call_user_func($findFunction, $this->_data, $start, $offset),
					'epos' => null
				);

				$e = array(
					'spos' => call_user_func($findFunction, $this->_data, $end, $offset),
					'epos' => null
				);

				if ($s['spos'] === false || $e['spos'] === false) {
					return false;
				}

				$s['epos'] = $s['spos'] + $startLen;
				$e['epos'] = $e['spos'] + $endLen;

				if ($s['epos'] == ($e['spos'] - 1)) {
					// @codeCoverageIgnoreStart
					return false;
					// @codeCoverageIgnoreEnd
				}

				$actualStart = substr($this->_data, $s['spos'], $startLen);
				$actualEnd = substr($this->_data, $e['spos'], $endLen);
				$inner = substr($this->_data, $s['epos'], ($e['spos'] - $s['epos']));
				$innerReplace = str_replace('%TEXT%', $inner, $replace);
				$this->replaceOnce($actualStart . $inner . $actualEnd, $innerReplace, $offset, $caseInsensitive);

				$offset = $s['spos'] + strlen($innerReplace);
			}

			return true;
		}

		/**
		 * Tries to find and replace the search string inside
		 * of the internal data store only once.
		 *
		 * @param string|StringHelper $search
		 * @param string|StringHelper $replace
		 * @param mixed $position
		 * @param mixed $caseInsensitive
		 * @return boolean
		 */
		public function replaceOnce($search, $replace, $position = 0, $caseInsensitive = false) : bool {
			$searchLen = strlen($search);
			$replaceLen = strlen($replace);
			$findFunction = ($caseInsensitive === false) ? self::CMP_STRPOS : self::CMP_STRIPOS;

			if ($searchLen < 1 || $replaceLen < 1 || $searchLen > $this->_length) {
				return false;
			}

			$offset = call_user_func($findFunction, $this->_data, $search, $position);

			if ($offset === false) {
				return false;
			}

			$afterStart = $offset + $searchLen;
			$afterLen = $this->_length - ($offset - $searchLen);
			$before = substr($this->_data, 0, $offset);
			$after = substr($this->_data, $afterStart, $afterLen);

			$this->_data = $before . $replace . $after;
			$this->_length = strlen($this->_data);

			return true;
		}

		/**
		 * Determines if the string begins with the provided
		 * string.
		 *
		 * @param string|StringHelper $string String value to look for at beginning of internal data store.
		 * @param boolean $caseInsensitive Optional toggle for case sensitivity of comparison.
		 * @return boolean
		 */
		public function startsWith($string, bool $caseInsensitive = false) : bool {
			$findFunction = ($caseInsensitive === false) ? self::CMP_STRPOS : self::CMP_STRIPOS;

			return call_user_func($findFunction, $this->_data, $string) === 0;
		}

		/**
		 * Return part of the internal data.
		 *
		 * @param integer $start Integer value to start substring selection based on position.
		 * @param null|integer $length Optional integer value to limit returned substring length.
		 * @return string
		 */
		public function subString(int $start, ?int $length = null) : string {
			if ($length !== null) {
				return substr($this->_data, $start, $length);
			}

			return substr($this->_data, $start);
		}

		/**
		 * Converts the internal data store to lowercase.
		 *
		 * @return void
		 */
		public function toLower() : void {
			$this->_data = strtolower($this->_data);

			return;
		}

		/**
		 * Converts the internal data store to uppercase.
		 *
		 * @return void
		 */
		public function toUpper() : void {
			$this->_data = strtoupper($this->_data);

			return;
		}

		/**
		 * Overrides default toString() behavior to instead
		 * output the internal data store.
		 *
		 * @return string
		 */
		public function __toString() : string {
			return $this->_data ?? '';
		}

		/**
		 * Joins strings together using the first argument
		 * as the glue string.  First argument can also
		 * optionally be a StringJoinConfig object.
		 *
		 * @throws \InvalidArgumentException
		 * @return StringHelper
		 */
		public static function join() : StringHelper {
			$argc = func_num_args();
			$argv = func_get_args();

			if ($argc < 2) {
				throw new \InvalidArgumentException("Not enough arguments for StringHelper::join(), must have at least glue and one string");
			}

			$config = new StringJoinOptions("", false);
			$ret = new StringHelper();

			if ($argv[0] instanceof StringJoinOptions) {
				$config = $argv[0];
			} else {
				$config->glue = $argv[0];
			}

			$glueLen = strlen($config->glue);
			$parts = array();

			for ($i = 1; $i < $argc; $i++) {
				if ($config->guardGlue) {
					if ($ret->endsWith($config->glue)) {
						// @codeCoverageIgnoreStart
						$ret = new StringHelper($ret->subString(0, $ret->length() - $glueLen));
						// @codeCoverageIgnoreEnd
					}

					$tmp = new StringHelper($argv[$i]);

					if ($tmp->startsWith($config->glue)) {
						$tmp = new StringHelper($tmp->subString($glueLen));
					}

					if ($tmp->endsWith($config->glue)) {
						$tmp = new StringHelper($tmp->subString(0, $tmp->length() - $glueLen));
					}

					$argv[$i] = $tmp->data();
				}

				$parts[] = $argv[$i];
			}

			$ret->append(implode($config->glue, $parts));

			return $ret;
		}
	}
