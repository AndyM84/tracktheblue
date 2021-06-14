<?php

	namespace Stoic\Tests\Web\Resources;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\Resources\HttpStatusCodes;

	class EnumsTest extends TestCase {
		public function test_HttpStatusCodes() {
			$code = new HttpStatusCodes(HttpStatusCodes::ACCEPTED);
			self::assertEquals('Accepted', $code->getDescription());

			$code = new HttpStatusCodes();
			self::assertEquals('Unknown Status Code', $code->getDescription());

			return;
		}
	}
