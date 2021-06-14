<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Log\Logger;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Utilities\FileHelper;
	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Stoic;

	class StoicTest extends TestCase {
		public function test_Instantiation() {
			$count = count(Stoic::getInstanceStack());

			$pv = new PageVariables([], [], [], ['test1' => 'val1'], [], [], ['REQUEST_METHOD' => 'GET'], []);
			$stoic = Stoic::getInstance('./', $pv, new Logger());

			self::assertEquals($count + 1, count(Stoic::getInstanceStack()));
			self::assertEquals('val1', Stoic::getInstanceStack()[0]->getRequest()->getGet()->getString('test1'));
			self::assertEquals('val1', $stoic->getRequest()->getGet()->getString('test1'));

			Stoic::getInstance('./', $pv, new Logger());
			self::assertEquals($count + 2, count(Stoic::getInstanceStack()));

			Stoic::getInstance('../', $pv, new Logger());
			self::assertEquals($count + 3, count(Stoic::getInstanceStack()));

			return;
		}

		public function test_GetMethods() {
			$pv = new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'GET'], []);
			$stoic1 = Stoic::getInstance('./', $pv, new Logger());
			$stoic1->setSession(new ParameterHelper());

			self::assertInstanceOf(PdoHelper::class, $stoic1->getDb());
			self::assertInstanceOf(FileHelper::class, $stoic1->getFileHelper());
			self::assertInstanceOf(ParameterHelper::class, $stoic1->getSession());
			self::assertInstanceOf(Logger::class, $stoic1->getLog());

			return;
		}
	}
