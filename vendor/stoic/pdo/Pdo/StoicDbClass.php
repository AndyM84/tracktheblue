<?php

	namespace Stoic\Pdo;

	use Stoic\Log\Logger;

	/**
	 * Abstract base class that ensures the availability
	 * of a PdoHelper instance, Logger instance, and
	 * some basic meta information on the called class.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.3
	 */
	abstract class StoicDbClass extends BaseDbClass {
		/**
		 * Internal PdoHelper instance.
		 *
		 * @var PdoHelper
		 */
		protected $db;


		/**
		 * Instantiates a new StoicDbClass object with the required dependencies.
		 *
		 * @param \PDO $db PDO instance for use by object.
		 * @param Logger $log Logger instance for use by object, defaults to new instance.
		 * @throws \InvalidArgumentException
		 */
		public function __construct(\PDO $db, $log = null) {
			parent::__construct($db, $log);

			if (!($db instanceof PdoHelper)) {
				throw new \InvalidArgumentException("StoicDbClass and derived classes require a PdoHelper");
			}

			return;
		}
	}
