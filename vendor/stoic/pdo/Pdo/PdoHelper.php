<?php

	namespace Stoic\Pdo;

	use Stoic\Utilities\EnumBase;

	/**
	 * Enumerated PDO driver types, used for meta information.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.1
	 */
	class PdoDrivers extends EnumBase {
		const PDO_UNKNOWN  = 0;
		const PDO_4D       = 1;
		const PDO_CUBRID   = 2;
		const PDO_FIREBIRD = 3;
		const PDO_FREETDS  = 4;
		const PDO_IBM      = 5;
		const PDO_INFORMIX = 6;
		const PDO_MSSQL    = 7;
		const PDO_MYSQL    = 8;
		const PDO_ODBC     = 9;
		const PDO_ORACLE   = 10;
		const PDO_PGSQL    = 11;
		const PDO_SQLITE   = 12;
		const PDO_SQLSRV   = 13;
		const PDO_SYBASE   = 14;
	}

	/**
	 * Data for an argument used with a stored
	 * query.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.1
	 */
	class PdoStoredArgument {
		/**
		 * String value of argument name.
		 *
		 * @var string
		 */
		public $name = null;
		/**
		 * PDO parameter type for argument.
		 *
		 * @var integer
		 */
		public $type = -1;


		/**
		 * Instantiates a new PdoStoredArgument object.
		 *
		 * @param string $name String value of argument name.
		 * @param int $type PDO parameter type for argument.
		 */
		public function __construct(string $name, int $type) {
			$this->name = $name;
			$this->type = $type;

			return;
		}
	}

	/**
	 * Meta information for a query intended for re-use.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.1
	 */
	class PdoStoredQuery {
		/**
		 * String identifier of query.
		 *
		 * @var string
		 */
		public $key = null;
		/**
		 * Query string stored for later use.
		 *
		 * @var string
		 */
		public $query = null;
		/**
		 * Arguments the query string will require
		 * when used.
		 *
		 * @var PdoStoredArgument[]
		 */
		public $arguments = [];


		/**
		 * Instantiates a new PdoStoredQuery object.
		 *
		 * @param string $key String identifier of query.
		 * @param string $query Query string being recorded.
		 * @param PdoStoredArgument[] $arguments Optional array of arguments to use with query.
		 */
		public function __construct(string $key, string $query, array $arguments = null) {
			$this->key = $key;
			$this->query = $query;

			if ($arguments !== null) {
				foreach ($arguments as $name => $type) {
					$this->arguments[$name] = new PdoStoredArgument($name, $type);
				}
			}

			return;
		}
	}

	/**
	 * Query meta class for tracking queries run through the
	 * PdoHelper.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.1
	 */
	class PdoQuery implements \JsonSerializable {
		/**
		 * Collection of arguments provided to query, if available.
		 *
		 * @var array
		 */
		public $arguments = [];
		/**
		 * Query string.
		 *
		 * @var string
		 */
		public $query = null;


		/**
		 * Instantiates a new PdoQuery object.
		 *
		 * @param string $query The query string that is being recorded.
		 * @param array $arguments Optional array of arguments used with query in format ['name', 'value', 'type'].
		 */
		public function __construct(string $query, array $arguments = null) {
			$this->query = $query;

			if ($arguments !== null) {
				$this->arguments = $arguments;
			}

			return;
		}

		/**
		 * Provides serialized data structure for json_encode.
		 *
		 * @return array
		 */
		public function jsonSerialize() {
			return [
				'query' => $this->query,
				'arguments' => $this->arguments
			];
		}
	}

	/**
	 * Error meta class for useful additional information
	 * on queries run through the PdoHelper.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.1
	 */
	class PdoError implements \JsonSerializable {
		/**
		 * The PDOException that was thrown at the time of the error.
		 *
		 * @var \PDOException
		 */
		public $exception = null;
		/**
		 * The query that caused the error, if available.
		 *
		 * @var PdoQuery
		 */
		public $query = null;


		/**
		 * Instantiates a new PdoError object.
		 *
		 * @param \PDOException $exception Exception that is being recorded.
		 * @param PdoQuery $query Query object that caused the thrown exception.
		 */
		public function __construct(\PDOException $exception, PdoQuery $query) {
			$this->exception = $exception;
			$this->query = $query;

			return;
		}

		/**
		 * Provides serialized data structure for json_encode.
		 *
		 * @return array
		 */
		public function jsonSerialize() {
			return [
				'message' => $this->exception->getMessage(),
				'stackTrace' => $this->exception->getTraceAsString(),
				'query' => $this->query
			];
		}
	}

	/**
	 * Class that wraps around a PDO instance to provide
	 * useful common operations and meta information.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.1
	 */
	class PdoHelper extends \PDO {
		/**
		 * Whether or not the class has an active connection.
		 *
		 * @var boolean
		 */
		protected $active = false;
		/**
		 * The DSN that was provided at initialization.
		 *
		 * @var string
		 */
		public $dsn = null;
		/**
		 * Currently configured driver as specified by the
		 * connection string.
		 *
		 * @var PdoDrivers
		 */
		protected $driver = null;
		/**
		 * Key for currently configured driver.
		 *
		 * @var string
		 */
		protected $driverKey = null;
		/**
		 * Collection of errors thrown by connection.
		 *
		 * @var PdoError[]
		 */
		protected $errors = [];
		/**
		 * The array of options provided at initialization, if available.
		 *
		 * @var array
		 */
		public $options = [];
		/**
		 * Collection of query strings that have been run through
		 * the helper.
		 *
		 * @var PdoQuery[]
		 */
		protected $queries = [];
		/**
		 * Number of queries that have been run through the helper.
		 *
		 * @var integer
		 */
		protected $queryCount = 0;


		/**
		 * Static lookup of drivers by DSN prefix.
		 *
		 * @var array
		 */
		protected static $driverLookup = [
			'4D'       => [PdoDrivers::PDO_4D,       '4d'],
			'cubrid'   => [PdoDrivers::PDO_CUBRID,   'cubrid'],
			'firebird' => [PdoDrivers::PDO_FIREBIRD, 'firebird'],
			'dblib'    => [PdoDrivers::PDO_FREETDS,  'freetds'],
			'ibm'      => [PdoDrivers::PDO_IBM,      'ibm'],
			'informix' => [PdoDrivers::PDO_INFORMIX, 'informix'],
			'mssql'    => [PdoDrivers::PDO_MSSQL,    'mssql'],
			'mysql'    => [PdoDrivers::PDO_MYSQL,    'mysql'],
			'odbc'     => [PdoDrivers::PDO_ODBC,     'odbc'],
			'oci'      => [PdoDrivers::PDO_ORACLE,   'oracle'],
			'pgsql'    => [PdoDrivers::PDO_PGSQL,    'postgresql'],
			'sqlite'   => [PdoDrivers::PDO_SQLITE,   'sqlite'],
			'sqlsrv'   => [PdoDrivers::PDO_SQLSRV,   'azure'],
			'sybase'   => [PdoDrivers::PDO_SYBASE,   'sybase']
		];
		/**
		 * Static collection of stored queries, grouped by
		 * driver and key.
		 *
		 * @var array
		 */
		protected static $storedQueries = [];


		/**
		 * Attempts to store a driver-specific query for later recall via the
		 * provided key.
		 *
		 * @param integer|string $driver Driver identifier (integer or string).
		 * @param string $key String identifier for the query.
		 * @param string $query Query string that should be stored by the given key.
		 * @param array $arguments Optional array of arguments the query will require in formation 'name' => 'type'.
		 * @return boolean
		 */
		public static function storeQuery($driver, string $key, string $query, array $arguments = null) : bool {
			$ret = true;
			$foundDriver = false;
			$driverTestZero = true;
			$query = new PdoStoredQuery($key, $query, $arguments);

			if (is_string($driver)) {
				$driverTestZero = false;
				$driver = strtolower($driver);
			}

			foreach (array_values(static::$driverLookup) as $drvr) {
				if (($driverTestZero && $drvr[0] === $driver) || (!$driverTestZero && $drvr[1] === $driver)) {
					$foundDriver = true;

					if ($driverTestZero) {
						$driver = $drvr[1];
					}

					break;
				}
			}

			if (!$foundDriver) {
				return false;
			}

			if (array_key_exists($driver, static::$storedQueries) === false) {
				static::$storedQueries[$driver] = [$key => $query];
			} else if (array_key_exists($key, static::$storedQueries[$driver]) !== false) {
				$ret = false;
			} else {
				static::$storedQueries[$driver][$key] = $query;
			}

			return $ret;
		}

		/**
		 * Attempts to store one or more driver-specific queries for later recall
		 * via the provided key(s).
		 *
		 * @param integer|string $driver Driver identifier (integer or string).
		 * @param array $queries Array of query information to store, in format ['key', 'query', [':argName' => 'argType']].
		 * @return void
		 */
		public static function storeQueries($driver, array $queries = null) : void {
			if ($queries !== null) {
				foreach (array_values($queries) as $query) {
					static::storeQuery($driver, $query[0], $query[1], $query[2]);
				}
			}

			return;
		}


		/**
		 * Instantiates a new PdoHelper object.
		 *
		 * @param string $dsn Data source name (DSN) containing the information to connect to the database.
		 * @param string $username Username for the DSN string, optional depending on PDO driver.
		 * @param string $password Password for the DSN string, optional depending on PDO driver.
		 * @param array $options A key=>value array of driver-specific connection options.
		 */
		public function __construct(string $dsn, string $username = null, string $password = null, array $options = null) {
			try {
				$args = [$dsn];

				// @codeCoverageIgnoreStart
				if ($username !== null) {
					$args[] = $username;

					if ($password !== null) {
						$args[] = $password;

						if ($options !== null) {
							$args[] = $options;
						}
					}
				}
				// @codeCoverageIgnoreEnd

				call_user_func_array(['parent', '__construct'], $args);

				$this->dsn = $dsn;
				$this->options = $options ?? [];

				foreach (array_keys(static::$driverLookup) as $prefix) {
					if (strtolower(substr($dsn, 0, (strlen($prefix) + 1))) == strtolower($prefix) . ':') {
						$this->driverName = static::$driverLookup[$prefix][1];
						$this->driver = new PdoDrivers(static::$driverLookup[$prefix][0]);

						break;
					}
				}

				if ($this->driver === null) {
					// @codeCoverageIgnoreStart
					throw new \PDOException("Invalid driver provided");
					// @codeCoverageIgnoreEnd
				}

				$this->active = true;
			// @codeCoverageIgnoreStart
			} catch (\PDOException $ex) {
				$this->active = false;
				$this->errors[] = new PdoError($ex, new PdoQuery('connect'));
			}
			// @codeCoverageIgnoreEnd

			return;
		}

		/**
		 * Initiates a transaction.
		 *
		 * @return boolean
		 */
		public function beginTransaction() : bool {
			return $this->tryActiveCommand(function () {
				return parent::beginTransaction();
			}, false);
		}

		/**
		 * Commits a transaction.
		 *
		 * @return boolean
		 */
		public function commit() : bool {
			return $this->tryActiveCommand(function () {
				return parent::commit();
			}, false);
		}

		/**
		 * Fetch the SQLSTATE associated with the last operation on
		 * the database handle.
		 *
		 * @return string
		 */
		public function errorCode() : string {
			return $this->tryActiveCommand(function () {
				return parent::errorCode();
			}, '');
		}

		/**
		 * Fetch extended error information associated with the last
		 * operation on the database handle.
		 *
		 * @return array
		 */
		public function errorInfo() {
			return $this->tryActiveCommand(function () {
				return parent::errorInfo();
			}, []);
		}

		/**
		 * Execute a SQL statement and return the number of affected
		 * rows.
		 *
		 * @param string $statement The SQL statement to prepare and execute
		 * @return integer
		 */
		public function exec($query) {
			return $this->tryActiveCommand(function () use ($query) {
				$ret = 0;

				try {
					$ret = parent::exec($query);
					$this->storeQueryRecord($query);
				} catch (\PDOException $ex) {
					$this->errors[] = new PdoError($ex, new PdoQuery($query));

					throw $ex;
				}

				return $ret;
			}, 0);
		}

		/**
		 * Execute a stored SQL statement and return the number of
		 * affected rows.
		 *
		 * @param string $key The key identifying the stored query.
		 * @return integer
		 */
		public function execStored(string $key) : int {
			return $this->tryActiveCommand(function () use ($key) {
				if (array_key_exists($key, static::$storedQueries[$this->driverName]) === false || count(static::$storedQueries[$this->driverName][$key]->arguments) > 0) {
					return 0;
				}

				return $this->exec(static::$storedQueries[$this->driverName][$key]->query);
			}, 0);
		}

		/**
		 * Retrieve a database connection attribute.
		 *
		 * @param integer $attribute One of the \PDO::ATTR_* constants.
		 * @return mixed
		 */
		public function getAttribute($attribute) {
			return $this->tryActiveCommand(function () use ($attribute) {
				return parent::getAttribute($attribute);
			}, null);
		}

		/**
		 * Retrieves the currently configured driver enum.
		 *
		 * @return PdoDrivers
		 */
		public function getDriver() : PdoDrivers {
			return $this->driver;
		}

		/**
		 * Retrieves all errors that have been recorded by
		 * the helper instance.
		 *
		 * @return PdoError[]
		 */
		public function getErrors() {
			return $this->errors;
		}

		/**
		 * Retrieves all queries that have been recorded by
		 * the helper instance.
		 *
		 * @return PdoQuery[]
		 */
		public function getQueries() {
			return $this->queries;
		}

		/**
		 * Retrieves the number of queries that have been
		 * run by the helper instance.
		 *
		 * @return integer
		 */
		public function getQueryCount() : int {
			return $this->queryCount;
		}

		/**
		 * Checks if inside a transaction.
		 *
		 * @return boolean
		 */
		public function inTransaction() : bool {
			return $this->tryActiveCommand(function () {
				return parent::inTransaction();
			}, false);
		}

		/**
		 * Returns whether or not the helper has an active
		 * database connection.
		 *
		 * @return boolean
		 */
		public function isActive() : bool {
			return $this->active;
		}

		/**
		 * Returns the ID of the last inserted row or sequence value.
		 *
		 * @param string $seqname Name of the sequence object from which the ID should be returned.
		 * @return mixed
		 */
		public function lastInsertId($seqname = null) {
			return $this->tryActiveCommand(function () use ($seqname) {
				return parent::lastInsertId($seqname);
			}, '');
		}

		/**
		 * Prepares a statement for execution and returns a statement object.
		 *
		 * @param string $statement This must be a valid SQL statement template for the target database server.
		 * @param array $options Holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns.
		 * @return \PDOStatement
		 */
		public function prepare($statement, $options = null) {
			return $this->tryActiveCommand(function () use ($statement, $options) {
				$ret = null;

				try {
					if ($options !== null) {
						// @codeCoverageIgnoreStart
						$ret = parent::prepare($statement, $options);
						// @codeCoverageIgnoreEnd
					} else {
						$ret = parent::prepare($statement);
					}

					$this->storeQueryRecord($statement);
				// @codeCoverageIgnoreStart
				} catch (\PDOException $ex) {
					$this->errors[] = new PdoError($ex, new PdoQuery($statement));

					throw $ex;
				}
				// @codeCoverageIgnoreEnd

				return $ret;
			}, null);
		}

		/**
		 * Prepares a stored statement for execution and returns a statement object.
		 *
		 * @param string $key Identifier for stored query.
		 * @param array $arguments Argument values in name=>value format to include with statement template.
		 * @param array $options Holds one or more key=>value pairs to set attribute values for the PDOStatement object that this method returns.
		 * @return \PDOStatement
		 */
		public function prepareStored(string $key, array $arguments = [], array $options = null) {
			return $this->tryActiveCommand(function () use ($key, $arguments, $options) {
				if (array_key_exists($key, static::$storedQueries[$this->driverName]) === false || count(static::$storedQueries[$this->driverName][$key]->arguments) !== count($arguments)) {
					return null;
				}

				$args = [];
				$stmt = null;
				$statement = static::$storedQueries[$this->driverName][$key]->query;

				if (count($arguments) > 0) {
					foreach ($arguments as $aKey => $value) {
						if (array_key_exists($aKey, static::$storedQueries[$this->driverName][$key]->arguments) === false) {
							return null;
						}

						$args[] = ["{$aKey}", $value, static::$storedQueries[$this->driverName][$key]->arguments[$aKey]->type];
					}
				}

				try {
					if ($options !== null) {
						// @codeCoverageIgnoreStart
						$stmt = parent::prepare($statement, $options);
						// @codeCoverageIgnoreEnd
					} else {
						$stmt = parent::prepare($statement);
					}

					foreach (array_values($args) as $argSet) {
						$stmt->bindValue($argSet[0], $argSet[1], $argSet[2]);
					}

					$this->storeQueryRecord($statement, $args);
				// @codeCoverageIgnoreStart
				} catch (\PDOException $ex) {
					$this->errors[] = new PdoError($ex, new PdoQuery($statement, $args));

					throw $ex;
				}
				// @codeCoverageIgnoreEnd

				return $stmt;
			}, null);
		}

		/**
		 * Executes a SQL statement, returning a result set as a PDOStatement object.
		 *
		 * @param string $statement The SQL statement to prepare and execute.
		 * @return \PDOStatement
		 */
		public function query(string $statement, ?int $fetchMode = null, mixed ...$fetchModeArgs) {
			return $this->tryActiveCommand(function () use ($statement, $fetchMode, $fetchModeArgs) {
				$ret = null;

				try {
					$ret = parent::query($statement, $fetchMode, ...$fetchModeArgs);
					$this->storeQueryRecord($statement);
				// @codeCoverageIgnoreStart
				} catch (\PDOException $ex) {
					$this->errors[] = new PdoError($ex, new PdoQuery($statement));

					throw $ex;
				}
				// @codeCoverageIgnoreEnd

				return $ret;
			}, null);
		}

		/**
		 * Executes a stored SQL statement, returning a result set as a PDOStatement
		 * object.
		 *
		 * @param string $key Identifier for stored query.
		 * @return \PDOStatement
		 */
		public function queryStored(string $key) {
			return $this->tryActiveCommand(function () use ($key) {
				if (array_key_exists($key, static::$storedQueries[$this->driverName]) === false || count(static::$storedQueries[$this->driverName][$key]->arguments) > 0) {
					return null;
				}

				return $this->query(static::$storedQueries[$this->driverName][$key]->query);
			}, null);
		}

		/**
		 * Quotes a string for use in a query.
		 *
		 * @param string $string The string to be quoted.
		 * @param integer $paramtype Provides a data type hint for drivers that have alternate quoting styles.
		 * @return string
		 */
		public function quote($string, $paramtype = null) {
			return $this->tryActiveCommand(function () use ($string, $paramtype) {
				if ($paramtype > -1) {
					return parent::quote($string, $paramtype);
				}

				return parent::quote($string);
			}, '');
		}
		
		/**
		 * Rolls back a transaction.
		 *
		 * @return boolean
		 */
		public function rollback() : bool {
			return $this->tryActiveCommand(function () {
				return parent::rollBack();
			}, false);
		}

		/**
		 * Set an attribute.
		 *
		 * @param integer $attribute PDO::ATTR_* constant to set on handler.
		 * @param mixed $value Value to set for the selected attribute.
		 * @return boolean
		 */
		public function setAttribute($attribute, $value)  {
			return $this->tryActiveCommand(function () use ($attribute, $value) {
				return parent::setAttribute($attribute, $value);
			}, false);
		}

		/**
		 * Sets multiple attributes in format attribute=>value.
		 *
		 * @param array $attributes Array of attributes and their values to set.
		 * @return void
		 */
		public function setAttributes(array $attributes = null) : void {
			if (!$this->active || $attributes === null) {
				return;
			}

			foreach ($attributes as $attrib => $value) {
				$this->setAttribute($attrib, $value);
			}

			return;
		}

		/**
		 * Stores a query that has been executed, it's arguments if available,
		 * and increments the query counter.
		 *
		 * @param string $query The SQL statement that was executed.
		 * @param array $arguments Possible collection of argument passed to statement.
		 * @return void
		 */
		protected function storeQueryRecord(string $query, array $arguments = null) : void {
			$this->queryCount++;
			$this->queries[] = new PdoQuery($query, $arguments);

			return;
		}

		/**
		 * Helper method to guard statements against being called when there
		 * is no active database handle.
		 *
		 * @param callable $command The code to execute if the handle is active.
		 * @param mixed $default Return value if the handle is inactive.
		 * @return mixed
		 */
		protected function tryActiveCommand(callable $command, $default = null) {
			if ($this->active) {
				return $command();
			}

			// @codeCoverageIgnoreStart
			return $default;
			// @codeCoverageIgnoreEnd
		}
	}
