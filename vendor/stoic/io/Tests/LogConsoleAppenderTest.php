<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\ConsoleHelper;
	use Stoic\Utilities\LogConsoleAppender;
	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\DispatchBase;
	use Psr\Log\LogLevel;
	use Stoic\Log\Message;
	use Stoic\Log\MessageDispatch;

	class NonMessageDispatch extends DispatchBase {
		public function initialize($input) {
			$this->makeValid();

			return;
		}
	}

	class LogConsoleAppenderTest extends TestCase {
		public function test_Processing() {
			$ch = new ConsoleHelper();
			$messages = array(
				new Message(LogLevel::CRITICAL, "This is a critical message"),
				new Message(LogLevel::INFO, "This is an info message")
			);

			$chain = new ChainHelper();
			$chain->linkNode(new LogConsoleAppender($ch));

			$disp = new MessageDispatch();
			$disp->initialize($messages);

			ob_start();
			$chain->traverse($disp);
			$contents = ob_get_contents();
			ob_end_clean();

			self::assertTrue(stripos($contents, 'This is a critical message') !== false);
			self::assertTrue(stripos($contents, 'This is an info message') !== false);

			$nmDisp = new NonMessageDispatch();
			$nmDisp->initialize(null);

			$chain->traverse($nmDisp);

			return;
		}
	}
