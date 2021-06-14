<?php

	namespace AndyM84\Tests\Config;

	use PHPUnit\Framework\TestCase;
	use AndyM84\Config\ConfigContainer;
	use AndyM84\Config\FieldTypes;

	class ConfigContainerTest extends TestCase {
		public function test_FieldTypes() {
			$ft = FieldTypes::fromString('int');
			self::assertTrue($ft->is(FieldTypes::INTEGER));
			self::assertEquals(FieldTypes::INTEGER, $ft->getValue());
			self::assertEquals('"int"', json_encode($ft));
			self::assertEquals('int', $ft->__toString());

			$ft = FieldTypes::fromString('test');
			self::assertFalse($ft->getName() == 'test');
			self::assertFalse($ft->is(FieldTypes::INTEGER));

			self::assertTrue(FieldTypes::validName('int'));
			self::assertFalse(FieldTypes::validName('test'));
			self::assertTrue(FieldTypes::validValue(FieldTypes::INTEGER));
			self::assertFalse(FieldTypes::validValue(-1));

			return;
		}

		public function test_Loading() {
			$settings = array(
				'schema' => array(
					'test1' => 'int',
					'test2' => 'str'
				),
				'settings' => array(
					'test1' => 5,
					'test2' => 'this is a test'
				)
			);

			$cfg = new ConfigContainer(json_encode($settings));
			self::assertTrue($cfg->has('test1'));
			self::assertEquals(FieldTypes::INTEGER, $cfg->getType('test1')->getValue());
			self::assertEquals(json_encode($settings), json_encode($cfg));

			$cfg = new ConfigContainer("{ \"test: ");
			self::assertFalse($cfg->has('test1'));

			$cfg = new ConfigContainer(json_encode(array('schema' => array('test1' => 'int'), 'settings' => array())));
			self::assertFalse($cfg->has('test1'));

			$cfg = new ConfigContainer(json_encode(array('schema' => array('test1' => 'int'), 'settings' => array('test2' => false))));
			self::assertFalse($cfg->has('test1'));

			return;
		}

		public function test_Retrieval() {
			$settings = array(
				'schema' => array(
					'test1' => 'int',
					'test2' => 'str'
				),
				'settings' => array(
					'test1' => 5,
					'test2' => 'this is a test'
				)
			);

			$cfg = new ConfigContainer(json_encode($settings));

			self::assertEquals(5, $cfg->get('test1'));
			self::assertNull($cfg->get('test3'));
			self::assertEquals(2, count($cfg->getSchema()));
			self::assertEquals(2, count($cfg->getSettings()));
			self::assertEquals(0, $cfg->getType('test3')->getValue());

			return;
		}

		public function test_Manipulation() {
			$settings = array(
				'schema' => array(
					'test1' => 'int',
					'test2' => 'str'
				),
				'settings' => array(
					'test1' => 5,
					'test2' => 'this is a test'
				)
			);

			$cfg = new ConfigContainer(json_encode($settings));

			$cfg->remove('test2');
			self::assertFalse($cfg->has('test2'));

			$cfg->rename('test1', 'test');
			self::assertFalse($cfg->has('test1'));
			self::assertTrue($cfg->has('test'));
			self::assertEquals(5, $cfg->get('test'));

			$cfg->set('test', 10);
			self::assertEquals(10, $cfg->get('test'));

			$cfg->set('test4', false, FieldTypes::BOOLEAN);
			self::assertTrue($cfg->has('test4'));
			self::assertFalse($cfg->get('test4'));

			$cfg->set('test5', 3.14, FieldTypes::FLOAT);
			self::assertTrue($cfg->has('test5'));
			self::assertEquals(3.14, $cfg->get('test5'));

			$cfg->set('test6', 'testing strings', FieldTypes::STRING);
			self::assertTrue($cfg->has('test6'));
			self::assertEquals('testing strings', $cfg->get('test6'));

			$cfg->set('test7', '${test5}', FieldTypes::FLOAT);
			self::assertTrue($cfg->has('test7'));
			self::assertEquals(3.14, $cfg->get('test7'));

			try {
				$cfg->remove('test3');
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot remove a field that doesn't exist", $ex->getMessage());
			}

			try {
				$cfg->rename('test3', 'testing');
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot rename a field that doesn't exist", $ex->getMessage());
			}

			try {
				$cfg->set('test8', 'test', -1);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid type given for new setting", $ex->getMessage());
			}

			return;
		}
	}
