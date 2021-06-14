<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\ParameterHelper;

	class ParameterHelperTest extends TestCase {
		protected $_params = array(
			'string'  => 'Awesome',
			'integer' => 42,
			'float'   => 3.14,
			'bool'    => true
		);

		protected $_additional = array(
			'name'   => 'Chris',
			'age'    => 32,
			'alive'  => true,
			'height' => 175.26
		);

		public function test_numValues() {
			$ph = new ParameterHelper($this->_params);

			$this->assertEquals(4, $ph->count());
		}

		public function test_hasValue() {
			$ph = new ParameterHelper($this->_params);

			$this->assertTrue($ph->has('string'));
			$this->assertFalse($ph->has('non-existent'));
		}

		public function test_getDefaultValues() {
			$ph = new ParameterHelper();

			self::assertEquals('Chris', $ph->get('non-existent', 'Chris'));
			self::assertEquals(123, $ph->getInt('non-existent', 123));
			self::assertEquals(true, $ph->getBool('non-existent', true));
			self::assertEquals('Test', $ph->getString('non-existent', 'Test'));
			self::assertEquals(18.5, $ph->getFloat('non-existent', 18.5));
			self::assertNull($ph->get('non-existent'));
			self::assertNull($ph->getBool('non-existent'));
			self::assertNull($ph->getFloat('non-existent'));
			self::assertNull($ph->getInt('non-existent'));
			self::assertNull($ph->getString('non-existent'));

			return;
		}

		public function test_GetValues() {
			$ph = new ParameterHelper(array_merge($this->_params, [
				'json' => json_encode(['testing' => 'values'])
			]));

			self::assertEquals('Awesome', $ph->getString('string'));
			self::assertEquals(42, $ph->getInt('integer'));
			self::assertEquals(3.14, $ph->getFloat('float'));
			self::assertEquals(true, $ph->getBool('bool'));
			self::assertEquals("Array\n(\n    [testing] => values\n)\n", print_r($ph->getJson('json', true), true));
			self::assertNull($ph->getJson('notthere'));
			self::assertEquals("Array\n(\n    [string] => Awesome\n    [integer] => 42\n    [float] => 3.14\n    [bool] => 1\n    [json] => {\"testing\":\"values\"}\n)\n", print_r($ph->get(null), true));
			self::assertEquals(42, $ph->get('integer', null, 'int'));
			self::assertEquals(42, $ph->get('integer'));

			return;
		}

		public function test_MutateParams() {
			$ph = new ParameterHelper($this->_params);
			self::assertEquals(4, $ph->count());

			$ph1 = $ph->withParameter('test1', 'test1');
			self::assertEquals(4, $ph->count());
			self::assertEquals(5, $ph1->count());
			self::assertEquals('test1', $ph1->getString('test1'));

			$ph2 = $ph->withParameters(['test1' => 'test1', 'test2' => 'test2']);
			self::assertEquals(4, $ph->count());
			self::assertEquals(6, $ph2->count());
			self::assertEquals('test1', $ph2->getString('test1'));

			$ph3 = $ph1->withoutParameter('test1');
			self::assertEquals(5, $ph1->count());
			self::assertEquals(4, $ph3->count());
			self::assertNull($ph3->get('test1'));

			$ph4 = $ph2->withoutParameters(['test1', 'test2']);
			self::assertEquals(6, $ph2->count());
			self::assertEquals(4, $ph4->count());
			self::assertNull($ph4->get('test1'));
			self::assertNull($ph4->get('test2'));

			return;
		}
	}
