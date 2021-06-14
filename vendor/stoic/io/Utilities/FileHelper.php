<?php

	namespace Stoic\Utilities;

	use Stoic\Utilities\EnumBase;

	/**
	 * Enumerated types of glob requests used internally with
	 * FileHelper::globFolder().
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class FileHelperGlobs extends EnumBase {
		const GLOB_ALL = 0;
		const GLOB_FOLDERS = 1;
		const GLOB_FILES = 2;
	}

	/**
	 * Class for common filesystem operations.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class FileHelper {
		/**
		 * Array to store cache of included files.
		 *
		 * @var string[]
		 */
		private static $included = [];
		/**
		 * String that will replace '~' in paths.
		 *
		 * @var string
		 */
		protected $relativePath = null;


		/**
		 * Instantiates new FileHelper class.
		 *
		 * @param string $relativePath String value of relative path, replaces '~' in paths.
		 * @param string[] $preIncludes Array of files that have already been included in runtime.
		 * @throws \InvalidArgumentException Thrown if core path provided is invalid/non-existent.
		 */
		public function __construct(string $relativePath, array $preIncludes = null) {
			if (!is_dir($relativePath)) {
				throw new \InvalidArgumentException("Invalid core path provided for FileHelper instance.");
			}

			$this->relativePath = $relativePath;

			if ($preIncludes !== null) {
				foreach (array_values($preIncludes) as $inc) {
					FileHelper::$included[$inc] = true;
				}
			}

			return;
		}

		/**
		 * Copies a single file between paths if file exists at
		 * source and does not already exist at destination.
		 *
		 * @param string $source String value of file source path, must exist and be non-null.
		 * @param string $destination String value of file destination path, must not exist and be non-null.
		 * @throws \InvalidArgumentException Thrown if source or destination are invalid, source doesn't exist, or destination does exist.
		 * @throws \RuntimeException Thrown if copy operation fails.
		 * @return void
		 */
		public function copyFile(string $source, string $destination) : void {
			if (empty($source) || empty($destination)) {
				throw new \InvalidArgumentException("Invalid source or destination path provided to FileHelper::copyFile() -> " . $source . ", " . $destination);
			}

			if (substr($source, -1) == '/' || substr($destination, -1) == '/') {
				throw new \InvalidArgumentException("Neither source nor destination to FileHelper::copyFile() can be directories -> " . $source . ", " . $destination);
			}

			if (!$this->fileExists($source) || $this->fileExists($destination)) {
				throw new \InvalidArgumentException("Source file didn't exist or destination already exists in FileHelper::copyFile() -> " . $source . ", " . $destination);
			}

			if (!copy($this->processRoot($source), $this->processRoot($destination))) {
				// @codeCoverageIgnoreStart
				throw new \RuntimeException("Failed to copy source file, check PHP logs for more information -> " . $source);
				// @codeCoverageIgnoreEnd
			}

			return;
		}

		/**
		 * Copies an entire folder between paths if folder exists
		 * at source and does not exist at destination.
		 *
		 * @param string $source String value of folder source path, must exist and be non-null.
		 * @param string $destination String value of folder destination path, must not exist and be non-null.
		 * @throws \InvalidArgumentException Thrown if source or destination are invalid, source doesn't exist, destination does exist, or any copy operation fails.
		 * @return void
		 */
		public function copyFolder(string $source, string $destination) : void {
			if (empty($source) || empty($destination)) {
				throw new \InvalidArgumentException("Invalid source or destination path provided to FileHelper::copyFolder() -> " . $source . ", " . $destination);
			}

			if (!$this->folderExists($source) || $this->folderExists($destination)) {
				throw new \InvalidArgumentException("Source directory didn't exist or destination already exists in FileHelper::copyFolder() -> " . $source . ", " . $destination);
			}

			$this->recursiveCopy($source, $destination);

			return;
		}

		/**
		 * Determines if a file exists at the given path.
		 *
		 * @param string $path String value of potential file path.
		 * @return boolean
		 */
		public function fileExists(string $path) : bool {
			if ($path !== null && !empty($path) && is_file($this->processRoot($path))) {
				return true;
			}

			return false;
		}

		/**
		 * Determine if a folder exists at the given path.
		 *
		 * @param string $path String value of potential folder path.
		 * @return boolean
		 */
		public function folderExists(string $path) : bool {
			if ($path !== null && !empty($path) && is_dir($this->processRoot($path))) {
				return true;
			}

			return false;
		}

		/**
		 * Retreives the contents of the file.
		 *
		 * @param string $path String value of file path.
		 * @throws \InvalidArgumentException Thrown if file does not exist or path is invalid.
		 * @return string
		 */
		public function getContents(string $path) : string {
			if (!$this->fileExists($path)) {
				throw new \InvalidArgumentException("Non-existent file provided to FileHelper::getContents() -> " . $path);
			}

			return file_get_contents($this->processRoot($path));
		}

		/**
		 * Retrieves all file names in a folder non-recursively.
		 *
		 * @param string $path String value of folder path.
		 * @return null|array
		 */
		public function getFolderFiles(string $path) {
			return $this->globFolder($path, FileHelperGlobs::GLOB_FILES);
		}

		/**
		 * Retrieves all folder names in a folder non-recursively.
		 *
		 * @param string $path String value of folder path.
		 * @return null|array
		 */
		public function getFolderFolders(string $path) {
			return $this->globFolder($path, FileHelperGlobs::GLOB_FOLDERS);
		}

		/**
		 * Retrieves all item names in a folder with option to
		 * do so recursively.
		 *
		 * @param string $path String value of folder path.
		 * @param boolean $recursive Boolean value to toggle recursive traversal, default is false.
		 * @return null|array
		 */
		public function getFolderItems(string $path, bool $recursive = false) {
			return $this->globFolder($path, FileHelperGlobs::GLOB_ALL, $recursive);
		}

		/**
		 * Retrieves the stored relative path value for this
		 * instance.
		 *
		 * @return string
		 */
		public function getRelativePath() : string {
			return $this->relativePath;
		}

		/**
		 * Internal method to traverse a folder's contents with option
		 * to do so recursively.  Must specify return type via $globType
		 * parameter.
		 *
		 * @param string $path String value of folder path.
		 * @param integer $globType Integer value of return type, can be 0 (all), 1 (folders only), and 2 (files only).
		 * @param boolean $recursive Boolean value to toggle recursive traversal, default is false.
		 * @return null|array
		 */
		protected function globFolder(string $path, int $globType, bool $recursive = false) {
			if (empty($path)) {
				return null;
			}

			$ret = array();
			$path = $this->processRoot($path);

			if (!is_dir($path)) {
				return null;
			}

			if (substr($path, -1) != '/') {
				$path .= '/';
			}

			if ($dh = @opendir($path)) {
				while (($item = @readdir($dh)) !== false) {
					if ($item == '.' || $item == '..') {
						continue;
					}

					if (is_dir($path . $item)) {
						if ($globType < FileHelperGlobs::GLOB_FILES) {
							if ($recursive) {
								// @codeCoverageIgnoreStart
								$tmp = $this->globFolder($path . $item, $globType, $recursive);

								if (count($tmp) > 0) {
									foreach (array_values($tmp) as $titem) {
										$ret[] = $titem;
									}
								}
								// @codeCoverageIgnoreEnd
							}

							$ret[] = $path . $item . '/';
						}
					} else if ($globType != FileHelperGlobs::GLOB_FOLDERS) {
						$ret[] = $path . $item;
					}
				}
			}

			@closedir($dh);

			return $ret;
		}

		/**
		 * Attempts to load the given file as a PHP file. Caches
		 * all successful loads and by default will disallow reload.
		 *
		 * @param string $path String value of file to attempt loading.
		 * @param boolean $allowReload Boolean value to allow reload if file has already been loaded, default is false.
		 * @throws \InvalidArgumentException Thrown if file doesn't exist or blank path provided.
		 * @throws \RuntimeException Thrown if file has already been loaded and reloads are disallowed.
		 * @return string
		 */
		public function load(string $path, bool $allowReload = false) : string {
			if (empty($path)) {
				throw new \InvalidArgumentException("Invalid file path provided for FileHelper::load()");
			}

			if (array_key_exists($path, FileHelper::$included) && !$allowReload) {
				return $path;
			}

			if (!$this->fileExists($path)) {
				throw new \InvalidArgumentException("Invalid file provided for FileHelper::load() -> " . $path);
			}

			FileHelper::$included[$path] = true;
			require($this->processRoot($path));

			return $path;
		}

		/**
		 * Attempts to load the given files as PHP files. Caches
		 * all successful loads and by default will disallow reload.
		 * 
		 * @param string[] $paths Array of string values for files to attempt loading.
		 * @param boolean $allowReload Boolean value to allow reload if files have already been loaded, default is false.
		 * @throws \InvalidArgumentException Thrown if a file doesn't exist.
		 * @throws \RuntimeException Thrown if a file has already been loaded and reloads are disallowed.
		 * @return string[]
		 */
		public function loadGroup(array $paths, bool $allowReload = false) {
			if (count($paths) < 1) {
				return [];
			}

			$ret = [];

			foreach (array_values($paths) as $path) {
				$ret[] = $this->load($path, $allowReload);
			}

			return $ret;
		}

		/**
		 * Attempts to create a folder if it doesn't exist.
		 *
		 * @param string $path String value of path for folder to create.
		 * @param int $mode Permission mode to attempt applying to created path (ignored on Windows), defaults to 0777.
		 * @param boolean $recursive Whether or not to create the path recursively, defaults to false.
		 * @return boolean
		 */
		public function makeFolder(string $path, int $mode = 0777, bool $recursive = false) : bool {
			if (empty($path) || $this->folderExists($path)) {
				return false;
			}

			return mkdir($this->processRoot($path), $mode, $recursive);
		}

		/**
		 * Joins paths parts together using the UNIX style
		 * directory separator.
		 *
		 * @param string $start Initial path part, only trailing slashes are managed.
		 * @param string[] $parts Additional path parts, final path part only manages leading slashes.
		 * @return string
		 */
		public function pathJoin(string $start, string ...$parts) : string {
			$path = array();
			$start = str_replace("\\", "/", $start);
			$partsCount = count($parts) - 1;

			if ($partsCount < 0) {
				return $this->processRoot($start);
			}

			if (strlen($start) > 1 && substr($start, -1) == '/') {
				$start = substr($start, 0, strlen($start) - 1);
			}

			$path[] = $start;

			for ($i = 0; $i < $partsCount; $i++) {
				$part = str_replace("\\", "/", $parts[$i]);

				if ($part[0] == '/') {
					$part = substr($part, 1);
				}

				if (substr($part, -1) == '/') {
					$part = substr($part, 0, strlen($part) - 1);
				}

				$path[] = $part;
			}

			if ($partsCount >= 0) {
				$end = str_replace("\\", "/", $parts[$partsCount]);

				if ($end[0] == '/') {
					$end = substr($end, 1);
				}

				$path[] = $end;
			}

			return $this->processRoot(implode('/', array_values($path)));
		}

		/**
		 * Internal method to change '~' prefix into
		 * core path.
		 *
		 * @param string $path String value of path to process.
		 * @return string
		 */
		protected function processRoot(string $path) : string {
			if ($path !== null && $path[0] == '~') {
				$path = $this->relativePath . substr($path, ($path[1] == '/' && $this->relativePath[strlen($this->relativePath) - 1] == '/') ? 2 : 1);
			}

			return $path;
		}

		/**
		 * Attempts to write data to file at path.
		 *
		 * @param string $path String value of file path.
		 * @param mixed $data Data to write to file, see http://php.net/file_put_contents for full details.
		 * @param integer $flags Optional flags to use for writing, see http://php.net/file_put_contents for full details.
		 * @param resource $context Optional stream context to use for writing, see http://php.net/file_put_contents for full details.
		 * @throws \InvalidArgumentException Thrown if file path is invalid or data is null.
		 * @return mixed
		 */
		public function putContents(string $path, $data, int $flags = 0, $context = null) {
			if (empty($path)) {
				throw new \InvalidArgumentException("Invalid file provided to FileHelper::putContents() -> " . $path);
			}

			if ($data === null || empty($data)) {
				throw new \InvalidArgumentException("No data provided to FileHelper::putContents(), should call FileHelper::touchFile() -> " . $path);
			}

			$args = [$this->processRoot($path), $data];

			// @codeCoverageIgnoreStart
			if ($flags > 0) {
				$args[] = $flags;
			}

			if ($context !== null) {
				$args[] = $context;
			}
			// @codeCoverageIgnoreEnd

			return call_user_func_array('file_put_contents', $args);
		}

		/**
		 * Internal method to traverse a folder's items
		 * recursively and copy them to a new destination.
		 *
		 * @param string $source String value of source folder, must exist and be non-null.
		 * @param string $dest String value of destination folder, must not exist and be non-null.
		 * @throws \InvalidArgumentException Thrown if source doesn't exist or destination does exist.
		 * @throws \RuntimeException Thrown if an item copy operation fails.
		 * @return void
		 */
		protected function recursiveCopy(string $source, string $dest) : void {
			if (substr($source, -1) != '/') {
				$source .= '/';
			}

			if (substr($dest, -1) != '/') {
				$dest .= '/';
			}

			if (!$this->folderExists($source) || $this->folderExists($dest)) {
				// @codeCoverageIgnoreStart
				throw new \InvalidArgumentException("Source directory didn't exist or destination directory does exist in FileHelper::recursiveCopy() -> " . $source . ", " . $dest);
				// @codeCoverageIgnoreEnd
			}

			$source = $this->processRoot($source);
			$dest = $this->processRoot($dest);

			$dh = @opendir($source);
			@mkdir($dest);

			while (($item = @readdir($dh)) !== false) {
				if ($item == '.' || $item == '..') {
					continue;
				}

				if (is_dir($source . $item)) {
					$this->recursiveCopy($source . $item, $dest . $item);
				} else {
					if (!copy($source . $item, $dest . $item)) {
						// @codeCoverageIgnoreStart
						@closedir($dh);

						throw new \RuntimeException("Failed to copy item in FileHelper::recursiveCopy() -> " . $source . ", " . $dest);
						// @codeCoverageIgnoreEnd
					}
				}
			}

			@closedir($dh);

			return;
		}

		/**
		 * Deletes a file.
		 *
		 * @param string $path Path to the file.
		 * @return boolean
		 */
		public function removeFile(string $path) : bool {
			if (empty($path)) {
				return false;
			}

			return unlink($this->processRoot($path));
		}

		/**
		 * Deletes a directory.
		 *
		 * @param string $path Path to the directory.
		 * @return boolean
		 */
		public function removeFolder(string $path) : bool {
			if (empty($path)) {
				return false;
			}

			return rmdir($this->processRoot($path));
		}

		/**
		 * Sets access and modification time of file.
		 *
		 * @param string $path String value of file path.
		 * @param null|integer $time The touch time.  If $time is not supplied, the current system time is used.
		 * @param null|integer $atime If present, the access time of the given filename is set to the value of atime. Otherwise, it is set to the value passed to the time parameter. If neither are present, the current system time is used. 
		 * @return boolean
		 */
		public function touchFile(string $path, ?int $time = null, ?int $atime = null) : bool {
			if (empty($path)) {
				return false;
			}

			$path = $this->processRoot($path);
			$time = ($time === null) ? time() : $time;
			$args = array($path, $time);

			if ($atime !== null) {
				$args[] = $atime;
			}

			return call_user_func_array('touch', $args);
		}
	}
