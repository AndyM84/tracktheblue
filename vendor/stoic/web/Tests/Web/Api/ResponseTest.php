<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\Api\Response;
	use Stoic\Web\Resources\HttpStatusCodes;

	class ResponseTest extends TestCase {
		public function test_Instantiation() {
			$resp = new Response();

			self::assertNull($resp->getData());
			self::assertNull($resp->getStatus()->getValue());

			$resp = new Response(HttpStatusCodes::USE_PROXY, 1);

			self::assertEquals(1, $resp->getData());
			self::assertTrue(HttpStatusCodes::validValue($resp->getStatus()->getValue()));

			return;
		}

		public function test_Assignment() {
			$resp = new Response();

			self::assertNull($resp->getData());
			self::assertNull($resp->getStatus()->getValue());

			try {
				$resp->setAsError('Test', 1);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid status code supplied for Response", $ex->getMessage());
			}

			$resp->setData(1);
			$resp->setStatus(HttpStatusCodes::OK);

			self::assertEquals(1, $resp->getData());
			self::assertTrue(HttpStatusCodes::validValue($resp->getStatus()->getValue()));

			$resp->setAsError("Error!");
			self::assertEquals("Error!", $resp->getData());
			self::assertTrue($resp->getStatus()->is(HttpStatusCodes::INTERNAL_SERVER_ERROR));

			return;
		}
	}
