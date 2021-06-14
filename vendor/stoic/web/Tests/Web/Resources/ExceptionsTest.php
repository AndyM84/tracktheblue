<?php

	namespace Stoic\Tests\Web\Resources;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\Resources\HeadersAlreadySentException;

	class ExceptionsTest extends TestCase {
		public function test_HeadersAlreadySent() {
			$ex = HeadersAlreadySentException::newWithHeaders("Testing");
			self::assertEquals("Testing", $ex->getMessage());

			return;
		}
	}
