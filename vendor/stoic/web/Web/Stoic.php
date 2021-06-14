<?php

	namespace Stoic\Web;

	use AndyM84\Config\ConfigContainer;
	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\FileHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Resources\SettingsStrings;
	use Stoic\Web\Resources\StoicStrings;

	/**
	 * Singleton-ish class in the Stoic framework.  Helps orchestrate common page
	 * -level operations.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class Stoic {
		/**
		 * Local ConfigContainer instance.
		 *
		 * @var ConfigContainer
		 */
		protected $config = null;
		/**
		 * Relative filesystem path for application's 'core' folder.
		 *
		 * @var string
		 */
		protected $corePath = null;
		/**
		 * Local PdoHelper instance.
		 *
		 * @var PdoHelper
		 */
		protected $db = null;
		/**
		 * Local FileHelper instance.
		 *
		 * @var FileHelper
		 */
		protected $fh = null;
		/**
		 * Local Logger instance.
		 *
		 * @var Logger
		 */
		protected $log = null;
		/**
		 * Local instance of current request information.
		 *
		 * @var Request
		 */
		protected $request = null;
		/**
		 * ParameterHelper instance which holds $_SESSION data.
		 *
		 * @var ParameterHelper
		 */
		protected $session = null;


		/**
		 * Static singleton instance.
		 *
		 * @var array
		 */
		protected static $instances = [];


		/**
		 * Static method to retrieve the most recent singleton instance for the
		 * system.  If instance exists but the Logger and PageVariables arguments
		 * are provided, a new instance is created and returned from the stack. If
		 * the instance doesn't exist, one is created.
		 *
		 * @param null|string $corePath Value of the relative filesystem path to get to the application's 'core' folder.
		 * @param PageVariables $variables Collection of 'predefined' variables, if not supplied an instance is created from globals.
		 * @param Logger $log Logger instance for use by instance, if not supplied a new instance is used.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @return Stoic
		 */
		public static function getInstance(?string $corePath = null, PageVariables $variables = null, Logger $log = null, $input = null) {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false) {
				static::$instances[$class] = [];
			}

			if (count(static::$instances[$class]) < 1 || ($corePath !== null && !empty($corePath) && $variables !== null && $log !== null)) {
				if (count(static::$instances[$class]) < 1) {
					static::$instances[$class][] = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger(), $input);
				} else {
					$existingCore = false;
					$insts = $class::getInstanceStack();

					foreach (array_values($insts) as $i) {
						if ($i->getCorepath() == $corePath) {
							$existingCore = true;

							break;
						}
					}

					if ($existingCore) {
						static::$instances[$class][] = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger(), $input, false);
					} else {
						static::$instances[$class][] = new $class($corePath, $variables ?? PageVariables::fromGlobals(), $log ?? new Logger(), $input);
					}
				}
			}

			return static::$instances[$class][count(static::$instances[$class]) - 1];
		}

		/**
		 * Returns a clone of the entire instance stack.
		 *
		 * @return Stoic[]
		 */
		public static function getInstanceStack() {
			$class = get_called_class();

			if (array_key_exists($class, static::$instances) === false || count(static::$instances[$class]) < 1) {
				return [];
			}

			$ret = [];

			foreach (array_values(static::$instances[$class]) as $inst) {
				$ret[] = $inst;
			}

			return $ret;
		}


		/**
		 * Instantiates a new Stoic object.
		 *
		 * @param string $corePath Value of the relative filesystem path to get to the application's 'core' folder.
		 * @param PageVariables $variables Collection of 'predefined' variables.
		 * @param Logger $log Logger instance for use by instance.
		 * @param mixed $input Optional input data to use instead of reading from `php://input` stream.
		 * @param boolean $loadFiles Whether or not to attempt loading auxillary files while instantiating, defaults to true.
		 */
		protected function __construct(string $corePath, PageVariables $variables, Logger $log, $input = null, bool $loadFiles = true) {
			$this->log = $log;
			$this->corePath = $corePath;
			$this->config = new ConfigContainer();
			$this->request = new Request($variables, $input);

			$this->setFileHelper(new FileHelper($this->corePath));

			if ($this->fh->fileExists(StoicStrings::SETTINGS_FILE_PATH)) {
				// @codeCoverageIgnoreStart
				$this->config = new ConfigContainer($this->fh->getContents(StoicStrings::SETTINGS_FILE_PATH));
				// @codeCoverageIgnoreEnd
			}

			$incPath = $this->config->get(SettingsStrings::INCLUDE_PATH, '~/inc');
			
			if ($loadFiles) {
				$clsExt = $this->config->get(SettingsStrings::CLASSES_EXTENSION, '.cls.php');
				$clsPath = $this->fh->pathJoin($incPath, $this->config->get(SettingsStrings::CLASSES_PATH,   'classes'));
				$this->loadFilesByExtension($clsPath, $clsExt, true);
				
				$rpoExt = $this->config->get(SettingsStrings::REPOS_EXTENSION, '.rpo.php');
				$rpoPath = $this->fh->pathJoin($incPath, $this->config->get(SettingsStrings::REPOS_PATH, 'repositories'));
				$this->loadFilesByExtension($rpoPath, $rpoExt, true);
			}

			register_shutdown_function(function (Logger $log) {
				// @codeCoverageIgnoreStart
				$log->output();
				// @codeCoverageIgnoreEnd
			}, $this->log);

			if (!defined('STOIC_DISABLE_DATABASE')) {
				$dsn = $this->config->get(SettingsStrings::DB_DSN, 'sqlite::memory:');
				$user = $this->config->get(SettingsStrings::DB_USER, '');
				$pass = $this->config->get(SettingsStrings::DB_PASS, '');

				$this->setDb(new PdoHelper($dsn, $user, $pass));

				if (!defined('STOIC_DISABLE_DB_EXCEPTIONS')) {
					$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				}
			}

			if (!defined('STOIC_DISABLE_SESSION') && !headers_sent()) {
				// @codeCoverageIgnoreStart
				if (session_status() != PHP_SESSION_ACTIVE && session_status() != PHP_SESSION_DISABLED) {
					session_start();
				}
				// @codeCoverageIgnoreEnd
			}

			if ($loadFiles) {
				$utlExt = $this->config->get(SettingsStrings::UTILITIES_EXT, '.utl.php');
				$utlPath = $this->fh->pathJoin($incPath, $this->config->get(SettingsStrings::UTILITIES_PATH, 'utilities'));
				$this->loadFilesByExtension($utlPath, $utlExt, true);
			}

			return;
		}

		/**
		 * Returns the current ConfigContainer the instance is using as its settings store.
		 *
		 * @return ConfigContainer
		 */
		public function getConfig() : ConfigContainer {
			return $this->config;
		}

		/**
		 * Returns the currently configured relative filesystem path for the 'core'
		 * folder.
		 *
		 * @return string
		 */
		public function getCorePath() : string {
			return $this->corePath;
		}

		/**
		 * Returns the local PdoHelper instance.
		 *
		 * @return PdoHelper
		 */
		public function getDb() : PdoHelper {
			return $this->db;
		}

		/**
		 * Returns the local FileHelper instance.
		 *
		 * @return FileHelper
		 */
		public function getFileHelper() : FileHelper {
			return $this->fh;
		}

		/**
		 * Returns the local Logger instance.
		 *
		 * @return Logger
		 */
		public function getLog() : Logger {
			return $this->log;
		}

		/**
		 * Returns the Request instance for this Stoic instance.
		 *
		 * @return Request
		 */
		public function getRequest() : Request {
			return $this->request;
		}

		/**
		 * Returns the ParameterHelper instance of the $_SESSION data.
		 *
		 * @return ParameterHelper
		 */
		public function getSession() : ParameterHelper {
			return $this->session;
		}

		/**
		 * Loads any files in the provided path if they have the given extension.
		 * Returns an array of any files that were loaded.
		 *
		 * @codeCoverageIgnore
		 * @param string $path Path for folder to look for files within.
		 * @param string $extension Extension to use when searching possible files.
		 * @param boolean $caseInsensitive Whether or not to perform a case-insensitive extension comparison, defaults to `false`.
		 * @param boolean $allowReloads Whether or not to allow loaded files to be reloaded, defaults to `false`.
		 * @return string[]
		 */
		public function loadFilesByExtension(string $path, string $extension, bool $caseInsensitive = false, bool $allowReloads = false) {
			$ret = [];
			$extLen = -1 * strlen($extension);
			$files = $this->fh->getFolderFiles($path);

			if ($files !== null && count($files) > 0) {
				foreach (array_values($files) as $file) {
					$ext = substr($file, $extLen);

					if ($caseInsensitive) {
						$ext = strtolower($ext);
					}

					if ($ext == $extension) {
						$ret[] = $file;
						$this->fh->load($file, $allowReloads);
					}
				}
			}

			return $ret;
		}

		/**
		 * Used to set the local PdoHelper instance.
		 *
		 * @param PdoHelper $db PdoHelper object to use internally.
		 * @return void
		 */
		public function setDb(PdoHelper $db) : void {
			$this->db = $db;

			return;
		}

		/**
		 * Used to set the local FileHelper instance.
		 *
		 * @param FileHelper $fh FileHelper object to use internally.
		 * @return void
		 */
		public function setFileHelper(FileHelper $fh) : void {
			$this->fh = $fh;

			return;
		}

		/**
		 * Attempts to set a header for the current request.  If any output has
		 * occurred prior to this attempt, the method will log the attempt and
		 * silently fail.
		 *
		 * @codeCoverageIgnore
		 * @param string $name String value of header name.
		 * @param string $value String value of header value.
		 * @param boolean $replace Optional toggle to replace vs duplicate a header, default behavior is to replace.
		 * @param null|integer $code Optional HTTP response code to set as response value.
		 * @return void
		 */
		public function setHeader(string $name, string $value, bool $replace = true, ?int $code = null) : void {
			if (headers_sent()) {
				$this->log->warning("Attempted to send the `{$name}` header with value `{$value}` after headers were already sent");

				return;
			}

			$this->log->info("Attempting to set the `{$name}` header with value `{$value}`");

			// @codeCoverageIgnoreStart
			if ($code !== null) {
				header("{$name}: {$value}", $replace, $code);
			} else {
				header("{$name}: {$value}", $replace);
			}
			// @codeCoverageIgnoreEnd

			return;
		}

		/**
		 * Used to set the local ParameterHelper instance with $_SESSION data.
		 *
		 * @param ParameterHelper $session ParameterHelper object to use internally.
		 * @return void
		 */
		public function setSession(ParameterHelper $session) : void {
			$this->session = $session;

			return;
		}
	}
