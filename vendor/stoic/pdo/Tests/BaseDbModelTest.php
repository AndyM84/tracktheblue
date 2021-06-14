<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Pdo\BaseDbModel;
	use Stoic\Pdo\BaseDbField;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\BaseDbQueryTypes;
	use Stoic\Pdo\ClassPropertyNotFoundException;
	use Stoic\Utilities\EnumBase;
	use Stoic\Utilities\ReturnHelper;
	use Pseudo\Pdo;
	use Pseudo\Result;
	use Stoic\Log\Logger;

	class TestEnum extends EnumBase {
		const VALUE_ONE = 1;
		const VALUE_TWO = 2;
	}

	class FromArrayTestClass1 extends BaseDbModel {
		public $test;
	}

	class FromArrayTestClass2 extends BaseDbModel {
		public $test;


		protected function __setupModel() : void {
			$this->setColumn('test', 'test', BaseDbTypes::INTEGER, true, true, true);

			return;
		}
	}

	class BasicTestDbClass extends BaseDbModel {
		protected $test;
		protected $test2;


		protected function __setupModel() : void {
			$this->setTableName('TestTable');
			$this->setColumn('test', 'test', BaseDbTypes::INTEGER, true, true, true);
			$this->setColumn('test2', 'test2', BaseDbTypes::STRING, false, true, true, true);

			return;
		}
	}

	class BadTestDbClass extends BaseDbModel {
		protected function __setupModel() : void {
			$this->setColumn('test', 'test', BaseDbTypes::INTEGER, false, true, true);
			$this->setColumn('test', 'test', BaseDbTypes::INTEGER, false, true, true);

			return;
		}
	}

	class EmptyDbClass extends BaseDbModel {
		public $test;
	}

	class NoInsertUpdateFieldDbClass extends BaseDbModel {
		public $test;


		protected function __setupModel() : void {
			$this->setColumn('test', 'test', BaseDbTypes::INTEGER, false, false, false);

			return;
		}
	}

	class NoPrimariesDbClass extends BaseDbModel {
		public $test;


		protected function __setupModel() : void {
			$this->setColumn('test', 'test', BaseDbTypes::INTEGER, false, true, true);

			return;
		}
	}

	class Role extends BaseDbModel {
		public $id;
		public $name;


		public static function fromId($id, \PDO $db, Logger $log = null) {
			$ret = new Role($db, $log);
			$ret->id = intval($id);
			
			if ($ret->read()->isBad()) {
				$ret->id = 0;
			}

			return $ret;
		}


		protected function __canCreate() {
			if ($this->id > 0 || empty($this->name) || $this->name === null) {
				return false;
			}

			try {
				$stmt = $this->db->prepare("SELECT COUNT(*) FROM Role WHERE Name = :name");
				$stmt->bindValue(':name', $this->name, \PDO::PARAM_STR);
				$stmt->execute();

				if ($stmt->fetch()['COUNT(*)'] > 0) {
					return false;
				}
			} catch (\PDOException $ex) {
				$this->log->error("Error checking for duplicates on creation: {ERROR}", array('ERROR' => $ex->getMessage()));

				return false;
			}

			return true;
		}

		protected function __canDelete() {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}

		protected function __canRead() {
			if ($this->id < 1) {
				return false;
			}

			return true;
		}

		protected function __canUpdate() {
			$ret = new ReturnHelper();
			$ret->makeBad();

			if ($this->id < 1 || empty($this->name) || $this->name === null) {
				$ret->addMessage("Invalid name or identifier for update");

				return $ret;
			}

			try {
				$stmt = $this->db->prepare("SELECT COUNT(*) FROM Role WHERE Name = :name AND ID <> :id");
				$stmt->bindValue(':name', $this->name, \PDO::PARAM_STR);
				$stmt->bindValue(':id', $this->id, \PDO::PARAM_INT);
				$stmt->execute();

				if ($stmt->fetch()['COUNT(*)'] > 0) {
					$ret->addMessage("Found duplicate role with name {$this->name} in database");

					return $ret;
				}
			} catch (\PDOException $ex) {
				$this->log->error("Error checking for duplicates on creation: {ERROR}", array('ERROR' => $ex->getMessage()));

				return $ret;
			}

			$ret->makeGood();

			return $ret;
		}

		protected function __setupModel() : void {
			$this->setTableName('Role');
			$this->setColumn('id', 'ID', BaseDbTypes::INTEGER, true, false, false, false, true);
			$this->setColumn('name', 'Name', BaseDbTypes::STRING, false, true, true);

			$this->id = 0;
			$this->name = null;

			return;
		}
	}

	class CompleteDbClass extends BaseDbModel {
		protected $id;
		protected $name;
		protected $date;
		protected $active;
		/**
		 * Summary of $intEnum
		 * @var TestEnum
		 */
		protected $intEnum;
		/**
		 * Summary of $stringEnum
		 * @var TestEnum
		 */
		protected $stringEnum;


		protected function __setupModel() : void {
			$this->setTableName('Test');
			$this->setColumn('id', 'ID', BaseDbTypes::INTEGER, true, false, false, false, true);
			$this->setColumn('name', 'Name', BaseDbTypes::STRING, false, true, true, true);
			$this->setColumn('date', 'Date', BaseDbTypes::DATETIME, false, true, true);
			$this->setColumn('active', 'Active', BaseDbTypes::BOOLEAN, false, true, true);
			$this->setColumn('intEnum', 'IntEnum', BaseDbTypes::INTEGER, false, true, true);
			$this->setColumn('stringEnum', 'StringEnum', BaseDbTypes::STRING, false, true, true);

			$this->intEnum = new TestEnum(1);
			$this->stringEnum = TestEnum::fromString('VALUE_ONE');

			return;
		}
	}

	class BaseDbModelTest extends TestCase {
		public function test_Instantiation() {
		  $pdo = new Pdo();
		  $role = new Role($pdo);
		  $pdo->mock("SELECT ID, Name FROM Role WHERE ID = :id", new Result(), array(':id' => 1));
		  $pdo->mock("SELECT COUNT(*) FROM Role WHERE Name = :name", array(array('COUNT(*)' => 1)), array(':name' => 'Testing'));

		  self::assertTrue($role->read()->isBad());
		  self::assertTrue($role->update()->isBad());
		  self::assertTrue($role->delete()->isBad());

		  $role->name = 'Testing';
		  self::assertTrue($role->create()->isBad());

		  $role = Role::fromId(1, $pdo);
		  self::assertEquals(0, $role->id);

		  $insertResult = new Result();
		  $insertResult->setInsertId(1);
		  $readResult = new Result(array(array('ID' => 1, 'Name' => 'Testing')), array(':id' => 1));
		  $readResult->setAffectedRowCount(1);

		  $pdo->mock("INSERT INTO Role (Name) VALUES (:name)", $insertResult, array(':name' => 'Testing'));
		  $pdo->mock("UPDATE Role SET Name = :name WHERE ID = :id", new Result(), array(':name' => 'Testarino', ':id' => 1));
		  $pdo->mock("SELECT COUNT(*) FROM Role WHERE Name = :name", array(array('COUNT(*)' => 0)), array(':name' => 'Testing'));
		  $pdo->mock("SELECT COUNT(*) FROM Role WHERE Name = :name AND ID <> :id", array(array('COUNT(*)' => 0)), array(':name' => 'Testarino', ':id' => 1));
		  $pdo->mock("SELECT ID, Name FROM Role WHERE ID = :id", $readResult, array(':id' => 1));
		  $pdo->mock("DELETE FROM Role WHERE ID = :id", new Result(), array(':id' => 1));

		  $role = new Role($pdo);
		  $role = new Role($pdo, new Logger());

		  $role->name = "Testing";
		  self::assertTrue($role->create()->isGood());
		  self::assertEquals(1, $role->id);
		  self::assertFalse($role->create()->isGood());

		  $role->name = "Testarino";
		  self::assertTrue($role->update()->isGood());
		  self::assertTrue($role->delete()->isGood());

		  self::assertEquals(1, Role::fromId(1, $pdo)->id);

		  $role->name = '';
		  self::assertTrue($role->update()->isBad());

		  $pdo = new Pdo();
		  $pdo->mock("SELECT COUNT(*) FROM Role WHERE Name = :name AND ID <> :id", array(array('COUNT(*)' => 1)), array(':name' => 'Testing', ':id' => 1));

		  $role = new Role($pdo);
		  $role->id = 1;
		  $role->name = 'Testing';
		  self::assertTrue($role->update()->isBad());

		  return;
		}

		public function test_FromArray() {
			try {
				new BadTestDbClass(new Pdo());
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot overwrite a field that has already been set", $ex->getMessage());
			}

			try {
				Role::fromArray(['id' => 1, 'roleName' => 'Testing'], new Pdo());
				self::assertTrue(false);
			} catch (ClassPropertyNotFoundException $ex) {
				self::assertEquals("Couldn't find match for roleName index while populating Stoic\Tests\Utilities\Role", $ex->getMessage());
			}

			return;
		}

		public function test_BaseDbTypes() {
			$type = BaseDbTypes::fromString('INTEGER');
			self::assertEquals(\PDO::PARAM_INT, $type->getDbType());

			$type = BaseDbTypes::fromString('STRING');
			self::assertEquals(\PDO::PARAM_STR, $type->getDbType());

			$type = BaseDbTypes::fromString('BOOLEAN');
			self::assertEquals(\PDO::PARAM_BOOL, $type->getDbType());

			$type = BaseDbTypes::fromString('NILL');
			self::assertEquals(\PDO::PARAM_NULL, $type->getDbType());

			$type = new BaseDbTypes(-1);
			self::assertEquals(null, $type->getDbType());

			return;
		}

		public function test_BaseDbField() {
			try {
				new BaseDbField('', BaseDbTypes::INTEGER, false, false, false);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot create a BaseDbField object with no column name", $ex->getMessage());
			}

			try {
				new BaseDbField('test', -1, false, false, false);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot create a BaseDbField object with an invalid BaseDbTypes value", $ex->getMessage());
			}

			return;
		}

		public function test_BaseDbModel_FromArray() {
			try {
				FromArrayTestClass1::fromArray(array(), new Pdo());
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot populate Stoic\Tests\Utilities\FromArrayTestClass1 from empty source array", $ex->getMessage());
			}

			try {
				FromArrayTestClass1::fromArray(array('test' => 1, 'extra' => 2), new Pdo(), new Logger(), ['className']);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot populate Stoic\Tests\Utilities\FromArrayTestClass1 from array, variable count mismatch (class: 1, source: 2)", $ex->getMessage());
			}

			$cls = FromArrayTestClass1::fromArray(array('test' => 1), new Pdo());
			self::assertEquals(1, $cls->test);

			$cls = FromArrayTestClass2::fromArray(array('test' => 1), new Pdo());
			self::assertEquals(1, $cls->test);

			return;
		}

		public function test_BaseDbModel_GetterSetter() {
			$cls = new BasicTestDbClass(new Pdo());
			$cls->test = 1;
			$cls->test2 = 'testing';
			$cls->missing = 2;

			self::assertEquals(1, $cls->test);
			self::assertEquals('testing', $cls->test2);
			self::assertNull($cls->missing);

			return;
		}

		public function test_BaseDbModel_CRUD() {
			$basic = new EmptyDbClass(new Pdo());
			self::assertTrue($basic->create()->isBad());

			$noInsert = new NoInsertUpdateFieldDbClass(new Pdo());
			self::assertTrue($noInsert->create()->isBad());
			self::assertTrue($noInsert->update()->isBad());

			$noPrimary = new NoPrimariesDbClass(new Pdo());
			self::assertTrue($noPrimary->read()->isBad());
			self::assertTrue($noPrimary->update()->isBad());
			self::assertTrue($noPrimary->delete()->isBad());

			$pdo = new Pdo();
			$result = new Result(array(array('ID' => 1, 'Name' => 'Testing', 'Date' => '2014-10-27 01:20:30', 'Active' => 1, 'IntEnum' => 1, 'StringEnum' => 'VALUE_TWO')), array(':id' => 1));
			$result->setAffectedRowCount(1);
			$pdo->mock("SELECT ID, Name, Date, Active, IntEnum, StringEnum FROM Test WHERE ID = :id", $result, array(':id' => 1));
			$complete = new CompleteDbClass($pdo);
			$complete->id = 1;

			self::assertTrue($complete->read()->isGood());
			self::assertEquals(1, $complete->id);
			self::assertEquals('Testing', $complete->name);
			self::assertEquals('2014-10-27 01:20:30', $complete->date);
			self::assertTrue($complete->active);
			self::assertEquals(1, $complete->intEnum->getValue());
			self::assertEquals('VALUE_TWO', $complete->stringEnum->getName());

			$pdo = new Pdo();
			$insertResult = new Result();
			$insertResult->setInsertId(1);
			$pdo->mock("INSERT INTO Test (Name, Date, Active, IntEnum, StringEnum) VALUES (:name, :date, :active, :intEnum, :stringEnum)", $insertResult, array(':name' => 'Testing', ':date' => null, ':active' => 0, ':intEnum' => 2, ':stringEnum' => 'VALUE_ONE'));
			$complete = new CompleteDbClass($pdo);
			$complete->id = 1;
			$complete->name = 'Testing';
			$complete->date = null;
			$complete->active = false;
			$complete->intEnum = new TestEnum(2);
			$complete->stringEnum = TestEnum::fromString('VALUE_ONE');

			self::assertTrue($complete->create()->isGood());

			$pdo = new Pdo();
			$insertResult = new Result();
			$insertResult->setInsertId(1);
			$pdo->mock("INSERT INTO Test (Name, Date, Active, IntEnum, StringEnum) VALUES (:name, :date, :active, :intEnum, :stringEnum)", $insertResult, array(':name' => 'Testing', ':date' => '2014-10-27 01:20:30', ':active' => 1, ':intEnum' => 2, ':stringEnum' => 'VALUE_ONE'));
			$complete = new CompleteDbClass($pdo);
			$complete->id = 1;
			$complete->name = 'Testing';
			$complete->date = '2014-10-27 01:20:30';
			$complete->active = true;
			$complete->intEnum = new TestEnum(2);
			$complete->stringEnum = TestEnum::fromString('VALUE_ONE');

			self::assertTrue($complete->create()->isGood());
			self::assertEquals(1, $complete->id);
			self::assertEquals('Testing', $complete->name);
			self::assertEquals('2014-10-27 01:20:30', $complete->date);
			self::assertEquals(2, $complete->intEnum->getValue());
			self::assertEquals('VALUE_ONE', $complete->stringEnum->getName());

			return;
		}

		public function test_BaseDbModel_Meta() {
			$role = new Role(new Pdo());
			$arrVersion = $role->toArray();
			$dbColumns = $role->getDbColumns();

			self::assertEquals('Role', $role->getShortClassName());
			self::assertEquals('Stoic\Tests\Utilities\Role', $role->getClassName());

			self::assertEquals(0, $arrVersion['id']);
			self::assertEquals('', $arrVersion['name']);

			self::assertEquals('ID', $dbColumns['id']->column->data());
			self::assertEquals('Name', $dbColumns['name']->column->data());

			return;
		}

		public function test_BaseDbModel_QueryGen() {
			$role = new Role(new Pdo());

			self::assertEquals('INSERT INTO Role (Name) VALUES (:name)', $role->generateClassQuery(BaseDbQueryTypes::INSERT));
			self::assertEquals('INSERT INTO Role (Name) VALUES (:name)', $role->generateClassQuery(BaseDbQueryTypes::INSERT, false));
			self::assertEquals('SELECT ID, Name FROM Role WHERE ID = :id', $role->generateClassQuery(BaseDbQueryTypes::SELECT));
			self::assertEquals('SELECT ID, Name FROM Role', $role->generateClassQuery(BaseDbQueryTypes::SELECT, false));
			self::assertEquals('UPDATE Role SET Name = :name WHERE ID = :id', $role->generateClassQuery(BaseDbQueryTypes::UPDATE));
			self::assertEquals('UPDATE Role SET Name = :name WHERE ID = :id', $role->generateClassQuery(BaseDbQueryTypes::UPDATE, false));
			self::assertEquals('DELETE FROM Role WHERE ID = :id', $role->generateClassQuery(BaseDbQueryTypes::DELETE));
			self::assertEquals('DELETE FROM Role WHERE ID = :id', $role->generateClassQuery(BaseDbQueryTypes::DELETE, false));

			return;
		}
	}
