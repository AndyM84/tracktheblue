<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\FileHelper;
	use Stoic\Utilities\LogFileAppender;
	use Stoic\Utilities\LogFileOutputTypes;
	use Stoic\Chain\ChainHelper;
	use Psr\Log\LogLevel;
	use Stoic\Log\Message;
	use Stoic\Log\MessageDispatch;

	class LogFileAppenderTest extends TestCase {
		public function test_Processing() {
			$io = new FileHelper('./');
			$logFile = '~/randomTestLogFile.testlog';
			$messages = array(
				new Message(LogLevel::CRITICAL, "This is a critical message"),
				new Message(LogLevel::INFO, "This is an info message")
			);

			try {
				new LogFileAppender($io, $logFile, 3);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid output type supplied", $ex->getMessage());
			}

			$chain = new ChainHelper();
			$chain->linkNode(new LogFileAppender($io, $logFile));

			$disp = new MessageDispatch();
			$disp->initialize($messages);

			$chain->traverse($disp);

			$contents = $io->getContents($logFile);
			$io->removeFile($logFile);

			self::assertTrue(stripos($contents, 'This is a critical message') !== false);
			self::assertTrue(stripos($contents, 'This is an info message') !== false);

			$chain = new ChainHelper();
			$chain->linkNode(new LogFileAppender($io, $logFile, LogFileOutputTypes::JSON));

			$disp = new MessageDispatch();
			$disp->initialize($messages);

			$chain->traverse($disp);

			$contents = $io->getContents($logFile);
			$io->removeFile($logFile);

			self::assertTrue(stripos($contents, '"This is a critical message"') !== false);
			self::assertTrue(stripos($contents, '"This is an info message"') !== false);

			$nmDisp = new NonMessageDispatch();
			$nmDisp->initialize(null);

			$chain->traverse($nmDisp);

			return;
		}
	}
