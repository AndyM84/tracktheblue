<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Pdo\BaseDbClass;
	use Pseudo\Pdo;
	use Stoic\Log\Logger;

	class BasicBaseClass extends BaseDbClass {
		public $testing;


		public function tryBadQuery() {
			return $this->tryPdoExcept(function () {
				throw new \PDOException('This is a test error');
			}, 'Testing');
		}

		public function tryNoQuery() {
			return $this->tryPdoExcept(function () {
				return true;
			}, 'Testing');
		}
	}

	class BaseDbClassTest extends TestCase {
		public function test_Init() {
			$cls = new BasicBaseClass(new Pdo(), new Logger());
			$cls->testing = 'testing';

			self::assertEquals('testing', $cls->testing);

			return;
		}

		public function test_TryPdoExcept() {
			$cls = new BasicBaseClass(new Pdo());

			self::assertTrue($cls->tryNoQuery());
			self::assertNull($cls->tryBadQuery());

			return;
		}
	}