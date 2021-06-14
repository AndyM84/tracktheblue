<?php

	namespace AndyM84\Tests\Config;

	use PHPUnit\Framework\TestCase;
	use AndyM84\Config\ConfigContainer;
	use AndyM84\Config\FieldTypes;
	use AndyM84\Config\MigrationAction;
	use AndyM84\Config\MigrationFile;
	use AndyM84\Config\MigrationOperators;
	use AndyM84\Config\Migrator;

	class MigrationTest extends TestCase {
		public function test_Operators() {
			$op = MigrationOperators::fromString('+');
			self::assertEquals(MigrationOperators::ADD, $op->getValue());
			self::assertTrue($op->is(MigrationOperators::ADD));
			self::assertFalse($op->is(-1));
			self::assertEquals('+', $op->getName());

			$op = MigrationOperators::fromString('no');
			self::assertEquals(MigrationOperators::ERROR, $op->getValue());

			self::assertTrue(MigrationOperators::validName('+'));
			self::assertFalse(MigrationOperators::validName('no'));

			self::assertTrue(MigrationOperators::validValue(MigrationOperators::RENAME));
			self::assertFalse(MigrationOperators::validValue(-1));

			$op = MigrationOperators::fromString('+');
			self::assertEquals('+', $op->__toString());
			self::assertEquals('"+"', json_encode($op->jsonSerialize()));

			return;
		}

		public function test_Actions() {
			$act = new MigrationAction("testField[str] + Value!");
			self::assertEquals('testField', $act->field);
			self::assertEquals('Value!', $act->value);
			self::assertTrue($act->type->is(FieldTypes::STRING));
			self::assertTrue($act->operator->is(MigrationOperators::ADD));

			$act = new MigrationAction("testField = Hey there");
			self::assertEquals('testField', $act->field);

			$act = new MigrationAction('testField = ""');
			self::assertEquals('', $act->value);

			try {
				$act = new MigrationAction('configVersion = 5');
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot access or modify the configVersion setting", $ex->getMessage());
			}

			self::expectException(\InvalidArgumentException::class);
			$act = new MigrationAction("testField[str] +");

			return;
		}

		public function test_Files() {
			$lines = array(
				'testField[str] + Value!',
				'testField > testingFields',
				'intField[int] + 5',
				'tempField[str] + Testing',
				'tempField -'
			);

			$file = new MigrationFile('test/files/location/0-1.cfg', $lines);
			self::assertEquals('0-1.cfg', $file->fileName);

			$file = new MigrationFile('test\\file\\location\\0-1.cfg', $lines);
			self::assertEquals('0-1.cfg', $file->fileName);

			try {
				$file = new MigrationFile('test-01.cfg', $lines);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("File name version numbers must be integers", $ex->getMessage());
			}

			self::expectException(\InvalidArgumentException::class);
			$file = new MigrationFile('01.cfg', $lines);

			return;
		}

		public function test_Migrator() {
			$firstMigration = 'coreVersion[int] + 1';
			$secondMigration = "testField[str] + Test Value!\ntempField[int] + 1";
			$thirdMigration = "anotherField[bln] + false";
			$fourthMigration = "testField > testingField\ncoreVersion = 2\ntempField -";

			try {
				$migrator = new Migrator('doesntExist');
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid migration directory", $ex->getMessage());
			}

			mkdir('doesntExist');

			try {
				$migrator = new Migrator('doesntExist', 'siteSettings.json', '');
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid migration extension", $ex->getMessage());
			}

			file_put_contents('doesntExist/0-1.cfg', $firstMigration);
			$migrator = new Migrator('doesntExist');
			$migrator->migrate();

			$cfg = new ConfigContainer(file_get_contents('siteSettings.json'));
			self::assertEquals(1, $cfg->get('configVersion'));
			self::assertEquals(1, $cfg->get('coreVersion'));

			file_put_contents('doesntExist/1-2.cfg', $secondMigration);
			$migrator = new Migrator('doesntExist');
			$migrator->migrate();

			$cfg = new ConfigContainer(file_get_contents('siteSettings.json'));
			self::assertEquals(2, $cfg->get('configVersion'));

			file_put_contents('doesntExist/1-3.cfg', $thirdMigration);
			$migrator = new Migrator('doesntExist');
			$migrator->migrate();

			file_put_contents('doesntExist/2-3.cfg', $fourthMigration);
			$migrator = new Migrator('doesntExist');
			$migrator->migrate();

			$cfg = new ConfigContainer(file_get_contents('siteSettings.json'));
			self::assertEquals(3, $cfg->get('configVersion'));

			foreach (glob('doesntExist/*.cfg') as $file) {
				unlink($file);
			}

			unlink('siteSettings.json');
			rmdir('doesntExist');

			return;
		}
	}
