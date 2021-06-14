<?php

	namespace Stoic\Tests\Web\Api;

	use PHPUnit\Framework\TestCase;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\FileUploadHelper;
	use Stoic\Web\Request;
	use Stoic\Web\Resources\InvalidRequestException;
	use Stoic\Web\Resources\NonJsonInputException;
	use Stoic\Web\Resources\PageVariables;
	use Stoic\Web\Resources\RequestType;

	class RequestTest extends TestCase {
		public function test_Initialization() {
			try {
				$vars = new PageVariables([], [], [], [], [], [], [], []);
				$req = new Request($vars);
				self::assertTrue(false);
			} catch (InvalidRequestException $ex) {
				self::assertEquals("Server collection was missing 'REQUEST_METHOD' value", $ex->getMessage());
			}

			try {
				$vars = new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'JIM'], []);
				$req = new Request($vars);
				self::assertTrue(false);
			} catch (InvalidRequestException $ex) {
				self::assertEquals("Invalid request method provided: JIM", $ex->getMessage());
			}

			$vars = new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'GET'], []);
			$req = new Request($vars);
			self::assertTrue($req->isValid());

			$vars = new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'POST'], []);
			$req = new Request($vars, 'true');
			self::assertTrue($req->isValid());
			self::assertEquals('true', $req->getRawInput());

			try {
				self::assertEquals(true, $req->getInput());
			} catch (NonJsonInputException $ex) {
				self::assertEquals("Can't get parameterized input for non-json payload", $ex->getMessage());
			}

			$vars = new PageVariables([], [], [], [], [], [], ['REQUEST_METHOD' => 'POST'], []);
			$req = new Request($vars);
			self::assertFalse($req->isValid());
			self::assertTrue($req->getRequestType()->is(RequestType::POST));

			return;
		}

		public function test_Parameters() {
			$vars = new PageVariables(['test2' => 'val2'], [], [], ['test1' => 'val1'], [], [], ['REQUEST_METHOD' => 'POST'], []);
			$req = new Request($vars, 'true');
			self::assertEquals('val2', $req->getCookies()->getString('test2'));
			self::assertEquals('val1', $req->getGet()->getString('test1'));

			$vars = new PageVariables(['test1' => 'val1'], ['test1' => 'val1'], [
				'test1' => [
					'name' => '',
					'type' => '',
					'size' => 0,
					'tmp_name' => '',
					'error' => UPLOAD_ERR_OK
				]
			], ['test1' => 'val1'], ['test1' => 'val1'], ['test1' => 'val1'], ['REQUEST_METHOD' => 'GET'], ['test1' => 'val1']);
			$req = new Request($vars, null);
			self::assertEquals('val1', $req->getInput()->getString('test1'));

			self::assertInstanceOf(ParameterHelper::class,  $req->getCookies());
			self::assertInstanceOf(ParameterHelper::class,  $req->getEnv());
			self::assertInstanceOf(FileUploadHelper::class, $req->getFiles());
			self::assertInstanceOf(ParameterHelper::class,  $req->getGet());
			self::assertInstanceOf(ParameterHelper::class,  $req->getPost());
			self::assertInstanceOf(ParameterHelper::class,  $req->getRequest());
			self::assertInstanceOf(ParameterHelper::class,  $req->getServer());
			self::assertInstanceOf(ParameterHelper::class,  $req->getSession());
			self::assertInstanceOf(PageVariables::class,    $req->getVariables());

			return;
		}
	}
