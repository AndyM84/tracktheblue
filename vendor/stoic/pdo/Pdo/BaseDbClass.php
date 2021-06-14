<?php

	namespace Stoic\Pdo;

	use Stoic\Log\Logger;
	use Stoic\Utilities\ReturnHelper;

	/**
	 * Abstract base class that ensures the availability
	 * of a PDO instance, Logger instance, and some basic
	 * meta information on the called class.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.3
	 */
	abstract class BaseDbClass {
		/**
		 * Fully qualified current class name.
		 *
		 * @var string
		 */
		protected $className = null;
		/**
		 * Internal PDO instance.
		 *
		 * @var \PDO
		 */
		protected $db = null;
		/**
		 * Internal Logger instance.
		 *
		 * @var \Stoic\Log\Logger
		 */
		protected $log = null;
		/**
		 * Short (non-qualified) current class
		 * name.
		 *
		 * @var string
		 */
		protected $shortClassName = null;


		/**
		 * Instantiates a new BaseDbClass object with the required dependencies.
		 *
		 * @param \PDO $db PDO instance for use by object.
		 * @param Logger $log Logger instance for use by object, defaults to new instance.
		 */
		public function __construct(\PDO $db, Logger $log = null) {
			$this->db = $db;
			$this->log = $log ?? new Logger();
			$this->className = get_called_class();
			$this->shortClassName = (new \ReflectionClass($this))->getShortName();

			$this->__initialize();

			return;
		}

		/**
		 * Optional method to initialize an object after the constructor has been
		 * called.
		 *
		 * @return void
		 */
		protected function __initialize() : void {
			return;
		}

		/**
		 * Method to perform common procedure of unrolling messages from a ReturnHelper into the Logger instance.
		 *
		 * @param ReturnHelper $rh ReturnHelper instance that can contain messages for logging.
		 * @param string $default Default log message if ReturnHelper instance contains no messages.
		 * @param string $level Optional LogLevel string, defaults to 'error'.
		 * @throws \InvalidArgumentException Thrown if empty default message provided.
		 * @return void
		 */
		protected function logReturnHelperMessages(ReturnHelper $rh, string $default, string $level = 'error') : void {
			if (empty(trim($default))) {
				throw new \InvalidArgumentException("Default log message must be defined");
			}

			if ($rh->hasMessages()) {
				foreach (array_values($rh->getMessages()) as $msg) {
					$this->log->log($level, $msg);
				}
			} else {
				$this->log->log($level, $default);
			}

			return;
		}

		/**
		 * Method to perform common wrapping of PDO code blocks in \PDOException
		 * catch and prefix errors with the given message.  Returns any value(s)
		 * returned by the callable code block.
		 *
		 * @param callable $callable Block of code to execute and guard against PDOExceptions.
		 * @param string $errorPrefix String prefix to use when logging exception messages.
		 * @return mixed
		 */
		protected function tryPdoExcept(callable $callable, string $errorPrefix) {
			try {
				return $callable();
			} catch (\PDOException $ex) {
				$this->log->error("{$errorPrefix}: {ERROR}", ['ERROR' => $ex]);
			}

			return null;
		}
	}
