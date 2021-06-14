<?php

	namespace AndyM84\Config;

	/**
	 * Class that represents a file with
	 * migration instructions.
	 *
	 * @version 1.0
	 * @author Andrew Male (AndyM84)
	 * @package AndyM84\Config
	 */
	class MigrationFile {
		/**
		 * Collection of actions from within
		 * instruction file.
		 *
		 * @var MigrationAction[]
		 */
		public $actions = array();
		/**
		 * Destination version for this instruction file.
		 *
		 * @var integer
		 */
		public $destVersion = null;
		/**
		 * Stripped file name for this instruction file.
		 *
		 * @var string
		 */
		public $fileName = null;
		/**
		 * Original version for this instruction file.
		 *
		 * @var integer
		 */
		public $origVersion = null;


		/**
		 * Instantiates a new MigrationFile object.
		 *
		 * @param string $fileName File name, must be in <VER>-<VER>.cfg format (all extra path information is stripped).
		 * @param string[] $lines Collection of strings that represent the lines of the instruction file.
		 * @param string $extension Optional file extension, defaults to '.cfg'.
		 * @throws \InvalidArgumentException Thrown if the file name has an incorrect format or the version numbers in the file name are not numbers.
		 */
		public function __construct($fileName, array $lines, $extension = '.cfg') {
			if (stripos($fileName, '/') !== false) {
				$this->fileName = substr($fileName, strripos($fileName, '/') + 1);
			} else if (stripos($fileName, '\\') !== false) {
				$this->fileName = substr($fileName, strripos($fileName, '\\') + 1);
			} else {
				$this->fileName = $fileName;
			}

			if (stripos($this->fileName, '-') === false) {
				throw new \InvalidArgumentException("File name was in incorrect format, should be in {VER}-{VER}{$extension}");
			}
			
			$nameParts = explode('-', str_replace($extension, '', $this->fileName));

			if (!is_numeric($nameParts[0]) || !is_numeric($nameParts[1])) {
				throw new \InvalidArgumentException("File name version numbers must be integers");
			}

			$this->origVersion = intval($nameParts[0]);
			$this->destVersion = intval($nameParts[1]);

			foreach (array_values($lines) as $line) {
				$this->actions[] = new MigrationAction($line);
			}

			return;
		}
	}
