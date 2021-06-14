<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\ConsoleHelper;

	class ConsoleHelperTest extends TestCase {
		public static $TEST_PARAMS = array("test.php", "-e", "--test=truly", "--test2", "truth", "--test3", "testing the stuff", "enabler", "tester=maybe");

		public function test_InstantiationWorksAsExpected() {
			$ch = new ConsoleHelper(array("test.php"));
			self::assertEquals(0, $ch->numArgs());
			self::assertEquals('test.php', $ch->getSelf());
			self::assertFalse($ch->hasArg('test'));
			self::assertEquals(null, $ch->getParameterWithDefault('s', 'something', null));
			self::assertFalse($ch->compareArg('test', 'truly'));
			self::assertFalse($ch->compareArgAt(1, 'truly'));
			self::assertFalse($ch->hasShortLongArg('t', 'test'));

			$ch = new ConsoleHelper(array('test.php', '--help'));
			self::assertEquals(1, $ch->numArgs());
			self::assertEquals('test.php', $ch->getSelf());
			self::assertTrue($ch->hasArg('help'));

			$ch = new ConsoleHelper(static::$TEST_PARAMS);
			self::assertEquals(8, $ch->numArgs());
			self::assertEquals('test.php', $ch->getSelf());
			self::assertTrue($ch->hasArg('e'));
			self::assertTrue($ch->hasArg('E', true));
			self::assertFalse($ch->hasArg('E'));
			self::assertFalse($ch->hasArg('hooey'));
			self::assertFalse($ch->hasArg('hooEy', true));
			self::assertEquals('truly', $ch->getParameterWithDefault('t', 'test'));
			self::assertTrue($ch->getParameterWithDefault('e', 'enable', false));
			self::assertTrue($ch->compareArg('test', 'truly'));
			self::assertTrue($ch->compareArg('test', 'trUly', true));
			self::assertTrue($ch->compareArgAt(1, '--test=truly'));
			self::assertTrue($ch->hasShortLongArg('e', 'enable'));
			self::assertTrue($ch->hasShortLongArg('t', 'TesT', true));
			self::assertFalse($ch->hasShortLongArg('c', 'control', true));
			self::assertTrue($ch->isCLI());
			self::assertTrue($ch->isNaturalCLI());
			self::assertEquals(6, count($ch->parameters(true)));
			self::assertEquals(6, count($ch->parameters(true, true)));

			$ch = new ConsoleHelper();
			self::assertEquals(0, $ch->numArgs());
			self::assertEquals(null, $ch->getSelf());
			self::assertEquals(array(), $ch->parameters());

			ob_start();
			$ch->put('Testing');
			self::assertEquals('Testing', ob_get_contents());
			ob_end_clean();

			ob_start();
			$ch->putLine('Testing');
			self::assertEquals("Testing\n", ob_get_contents());
			ob_end_clean();

			ob_start();
			$ch->putLine();
			self::assertEquals("\n", ob_get_contents());
			ob_end_clean();

			return;
		}
	}
