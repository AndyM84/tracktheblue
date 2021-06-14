<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\PdoQuery;
	use Stoic\Pdo\PdoError;

	class PdoHelperTestable extends PdoHelper {
		public static function getStoredQueries() {
			return static::$storedQueries;
		}
	}

	class PdoHelperTest extends TestCase {
		protected static $hasSqlite = false;

		public static function setUpBeforeClass() : void {
			if (array_search('sqlite', \PDO::getAvailableDrivers()) !== false) {
				static::$hasSqlite = true;
			}

			return;
		}

		public function test_Structs() {
			$query = new PdoQuery('SELECT * FROM User WHERE Email = :email', [[':email', 'andrew.male@grokspark.com', \PDO::PARAM_STR]]);
			self::assertEquals('{"query":"SELECT * FROM User WHERE Email = :email","arguments":[[":email","andrew.male@grokspark.com",2]]}', json_encode($query));

			$error = new PdoError(new \PDOException('Testing'), $query);
			$errorJson = json_decode(json_encode($error), true);

			self::assertTrue(array_key_exists('message', $errorJson));
			self::assertTrue(array_key_exists('stackTrace', $errorJson));
			self::assertEquals('{"query":"SELECT * FROM User WHERE Email = :email","arguments":[[":email","andrew.male@grokspark.com",2]]}', json_encode($errorJson['query']));

			return;
		}

		public function test_StoredQueries() {
			if (!static::$hasSqlite) {
				self::assertTrue(true);

				return;
			}

			self::assertFalse(PdoHelperTestable::storeQuery('sql', 'sq_test_1', 'SELECT * FROM User WHERE Email = :email', [':email' => \PDO::PARAM_STR]));
			self::assertTrue(PdoHelperTestable::storeQuery('sqlite', 'sq_test_1', 'SELECT * FROM User WHERE Email = :email', [':email' => \PDO::PARAM_STR]));
			self::assertFalse(PdoHelperTestable::storeQuery('sqlite', 'sq_test_1', 'SELECT * FROM User WHERE Email = :email', [':email' => \PDO::PARAM_STR]));
			self::assertTrue(PdoHelperTestable::storeQuery('sqlite', 'sq_test_2', 'INSERT INTO User (Username, Email) VALUES (:username, :email)', [':username' => \PDO::PARAM_STR, ':email' => \PDO::PARAM_STR]));
			self::assertTrue(PdoHelperTestable::storeQuery(PdoDrivers::PDO_SQLITE, 'sq_test_3', 'SELECT ID, Username, Email FROM User WHERE Username = :username', [':username' => \PDO::PARAM_STR]));

			PdoHelperTestable::storeQueries('sqlite', [
				['sq_test_4', 'CREATE TABLE User (ID INTEGER PRIMARY KEY, Username TEXT, Email TEXT)', []],
				['sq_test_5', 'SELECT Username FROM User', []]
			]);

			$storedQueries = PdoHelperTestable::getStoredQueries();

			self::assertTrue(array_key_exists('sqlite', $storedQueries));
			self::assertTrue(array_key_exists('sq_test_1', $storedQueries['sqlite']));
			self::assertTrue(array_key_exists('sq_test_2', $storedQueries['sqlite']));
			self::assertTrue(array_key_exists('sq_test_3', $storedQueries['sqlite']));
			self::assertTrue(array_key_exists('sq_test_4', $storedQueries['sqlite']));
			self::assertTrue(array_key_exists('sq_test_5', $storedQueries['sqlite']));

			return;
		}

		public function test_Initialization() {
			if (!static::$hasSqlite) {
				self::assertTrue(true);

				return;
			}

			self::assertTrue((new PdoHelper('sqlite::memory:'))->isActive());
			self::assertTrue((new PdoHelper('sqlite::memory:'))->getDriver()->getValue() === PdoDrivers::PDO_SQLITE);

			return;
		}

		public function test_Transactions() {
			if (!static::$hasSqlite) {
				self::assertTrue(true);

				return;
			}

			try {
				$db = new PdoHelper('sqlite::memory:');
				$db->beginTransaction();
				$db->execStored('sq_test_4');
				self::assertTrue($db->inTransaction());
				$db->rollback();
				$db->beginTransaction();
				$db->execStored('sq_test_4');
				$db->commit();

				self::assertTrue(true);
			} catch (\PDOException $ex) {
				self::assertFalse(true);
			}

			return;
		}

		public function test_Errors() {
			if (!static::$hasSqlite) {
				self::assertTrue(true);

				return;
			}

			$db = new PdoHelper('sqlite::memory:');
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			self::assertEquals(\PDO::ERRMODE_EXCEPTION, $db->getAttribute(\PDO::ATTR_ERRMODE));

			try {
				$db->execStored('sq_test_4');
				$db->execStored('sq_test_4');

				self::assertTrue(false);
			} catch (\PDOException $ex) {
				self::assertEquals('SQLSTATE[HY000]: General error: 1 table User already exists', $ex->getMessage());
				self::assertEquals('HY000', $db->errorCode());
				self::assertEquals('["HY000",1,"table User already exists"]', json_encode($db->errorInfo()));
				self::assertEquals(1, count($db->getErrors()));
				self::assertEquals(1, count($db->getQueries()));
				self::assertEquals(1, $db->getQueryCount());
			}

			self::assertEquals(0, $db->execStored('sq_test_5'));

			return;
		}

		public function test_Queries() {
			if (!static::$hasSqlite) {
				self::assertTrue(true);

				return;
			}

			$db = new PdoHelper('sqlite::memory:');
			$db->setAttributes();
			$db->setAttributes([\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);

			try {
				$db->execStored('sq_test_4');
				self::assertEquals(0, $db->execStored('sq_test_-1'));

				$stmt = $db->prepareStored('sq_test_2', [':username' => 'AndyM84', ':email' => 'andrew.male@grokspark.com']);
				$stmt->execute();

				self::assertEquals('1', $db->lastInsertId());
				self::assertNull($db->prepareStored('sq_test_2', [':username' => 'AndyM84']));
				self::assertNull($db->prepareStored('sq_test_2', [':username' => 'AndyM84', ':emailAddress' => 'andrew.male@grokspark.com']));

				foreach ($db->queryStored('sq_test_5') as $row) {
					self::assertEquals('AndyM84', $row['Username']);
				}

				self::assertNull($db->queryStored('sq_test_-1'));

				self::assertEquals("'Testing''s'", $db->quote("Testing's"));
				self::assertEquals("'Testing''s'", $db->quote("Testing's", \PDO::PARAM_STR));
			} catch (\PDOException $ex) {
				self::assertFalse(true);
			}

			try {
				$stmt = $db->prepare('INSERT INTO User (ID, Username, Email) VALUES (:id, :username, :email)');
				$stmt->bindValue(':id', 1, \PDO::PARAM_INT);
				$stmt->bindValue(':username', 'AndyM84', \PDO::PARAM_INT);
				$stmt->bindValue(':email', 'andrew.male@grokspark.com', \PDO::PARAM_STR);
				$stmt->execute();
			} catch (\PDOException $ex) {
				self::assertEquals('SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: User.ID', $ex->getMessage());
			}

			return;
		}
	}
