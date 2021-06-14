<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\SanitationHelper;
	use Stoic\Utilities\Sanitizers\BooleanSanitizer;
	use Stoic\Utilities\Sanitizers\FloatSanitizer;
	use Stoic\Utilities\Sanitizers\IntegerSanitizer;
	use Stoic\Utilities\Sanitizers\StringSanitizer;
	use Stoic\Utilities\Sanitizers\JsonSanitizer;

	class testObject {
		public $one;
		public $two;
		protected $three;

		public function __toString() {
			return 'test_string';
		}
	}

	class SanitationTest extends TestCase {
		public function test_addSanitizer() {
			$sanitation = new SanitationHelper();
			$sanitation->addSanitizer('string', StringSanitizer::class);
			$sanitation->addSanitizer('integer', new IntegerSanitizer());

			$this->assertTrue($sanitation->hasSanitizer('string'));
			$this->assertTrue($sanitation->hasSanitizer('integer'));

			$this->assertEquals('123', $sanitation->sanitize(123, 'string'));
			$this->assertEquals(123, $sanitation->sanitize('123', 'integer'));
		}

		public function test_nonExistentSanitizerReturnsOriginalValue() {
			$sanitation = new SanitationHelper();
			$this->assertEquals('1984-06-15', $sanitation->sanitize('1984-06-15', 'datetime'));
		}

		public function test_booleanSanitizer() {
			$sanitation = new SanitationHelper();
			$sanitation->addSanitizer('bool', BooleanSanitizer::class);

			$false       = $sanitation->sanitize(false, 'bool');
			$true        = $sanitation->sanitize(true, 'bool');
			$zero        = $sanitation->sanitize(0, 'bool');
			$one         = $sanitation->sanitize(1, 'bool');
			$empty       = $sanitation->sanitize(array(), 'bool');
			$full        = $sanitation->sanitize(array('one', 'two'), 'bool');
			$stringTrue  = $sanitation->sanitize('true', 'bool');
			$stringFalse = $sanitation->sanitize('false', 'bool');

			self::assertFalse($false);
			self::assertTrue(is_bool($false));

			self::assertTrue($true);
			self::assertTrue(is_bool($true));

			self::assertFalse($zero);
			self::assertTrue(is_bool($zero));

			self::assertTrue($one);
			self::assertTrue(is_bool($one));

			self::assertFalse($empty);
			self::assertTrue(is_bool($empty));

			self::assertTrue($full);
			self::assertTrue(is_bool($full));

			self::assertTrue($stringTrue);
			self::assertTrue(is_bool($stringTrue));

			self::assertFalse($stringFalse);
			self::assertTrue(is_bool($stringTrue));

			return;
		}

		public function test_stringSanitizer() {
			$sanitation = new SanitationHelper();
			$sanitation->addSanitizer('string', StringSanitizer::class);

			$object  = $sanitation->sanitize(new testObject(), 'string');
			$array   = $sanitation->sanitize(array(), 'string');
			$true    = $sanitation->sanitize(true, 'string');
			$false   = $sanitation->sanitize(false, 'string');
			$integer = $sanitation->sanitize(42, 'string');
			$float   = $sanitation->sanitize(3.14, 'string');
			$string  = $sanitation->sanitize('actual_string', 'string');

			$this->assertEquals('test_string', $object);
			$this->assertTrue(is_string($object));

			$this->assertEquals(serialize(array()), $array);
			$this->assertTrue(is_string($array));

			$this->assertEquals('true', $true);
			$this->assertTrue(is_string($true));

			$this->assertEquals('false', $false);
			$this->assertTrue(is_string($false));

			$this->assertEquals('42', $integer);
			$this->assertTrue(is_string($integer));

			$this->assertEquals('3.14', $float);
			$this->assertTrue(is_string($float));

			$this->assertEquals('actual_string', $string);
			$this->assertTrue(is_string($string));
		}

		public function test_integerSanitizer() {
			$sanitation = new SanitationHelper();
			$sanitation->addSanitizer('integer', IntegerSanitizer::class);

			$object    = $sanitation->sanitize(new testObject(), 'integer');
			$empty     = $sanitation->sanitize(array(), 'integer');
			$full      = $sanitation->sanitize(array(1,2,3), 'integer');
			$true      = $sanitation->sanitize(true, 'integer');
			$false     = $sanitation->sanitize(false, 'integer');
			$string    = $sanitation->sanitize('string', 'integer');
			$integer   = $sanitation->sanitize('42', 'integer');
			$float     = $sanitation->sanitize(3.14, 'integer');
			$actualInt = $sanitation->sanitize(42, 'integer');

			$this->assertEquals(2, $object);
			$this->assertTrue(is_int($object));

			$this->assertEquals(0, $empty);
			$this->assertTrue(is_int($empty));

			$this->assertEquals(3, $full);
			$this->assertTrue(is_int($full));

			$this->assertEquals(1, $true);
			$this->assertTrue(is_int($true));

			$this->assertEquals(0, $false);
			$this->assertTrue(is_int($false));

			$this->assertEquals(6, $string);
			$this->assertTrue(is_int($string));

			$this->assertEquals(42, $integer);
			$this->assertTrue(is_int($integer));

			$this->assertEquals(3, $float);
			$this->assertTrue(is_int($float));

			$this->assertEquals(42, $actualInt);
			$this->assertTrue(is_int($actualInt));
		}

		public function test_floatSanitizer() {
			$sanitation = new SanitationHelper();
			$sanitation->addSanitizer('float', FloatSanitizer::class);

			$object      = $sanitation->sanitize(new testObject(), 'float');
			$empty       = $sanitation->sanitize(array(), 'float');
			$full        = $sanitation->sanitize(array(1,2,3), 'float');
			$true        = $sanitation->sanitize(true, 'float');
			$false       = $sanitation->sanitize(false, 'float');
			$string      = $sanitation->sanitize('string', 'float');
			$integer     = $sanitation->sanitize('42', 'float');
			$float       = $sanitation->sanitize('3.14', 'float');
			$actualFloat = $sanitation->sanitize(6.66, 'float');

			$this->assertEquals(2, $object);
			$this->assertTrue(is_float($object));

			$this->assertEquals(0, $empty);
			$this->assertTrue(is_float($empty));

			$this->assertEquals(3, $full);
			$this->assertTrue(is_float($full));

			$this->assertEquals(1, $true);
			$this->assertTrue(is_float($true));

			$this->assertEquals(0, $false);
			$this->assertTrue(is_float($false));

			$this->assertEquals(6, $string);
			$this->assertTrue(is_float($string));

			$this->assertEquals(42, $integer);
			$this->assertTrue(is_float($integer));

			$this->assertEquals(3.14, $float);
			$this->assertTrue(is_float($float));

			$this->assertEquals(6.66, $actualFloat);
			$this->assertTrue(is_float($actualFloat));
		}

		public function test_jsonSanitizer() {
			$sanitation = new SanitationHelper();
			$sanitation->addSanitizer('json', JsonSanitizer::class);

			try {
				$sanitation->sanitize('{test: "', 'json');
				self::assertTrue(false);
			} catch (\Exception $ex) {
				self::assertEquals('Syntax error', $ex->getMessage());
			}

			self::assertTrue(array_key_exists('testing', $sanitation->sanitize('{ "testing": "values" }', 'json')));

			return;
		}

		public function test_hasSanitizer() {
			$sanitation = new SanitationHelper();
			$this->assertFalse($sanitation->hasSanitizer('datetime'));

			$sanitation->addSanitizer('datetime', BooleanSanitizer::class);
			$this->assertTrue($sanitation->hasSanitizer('datetime'));
		}
	}
