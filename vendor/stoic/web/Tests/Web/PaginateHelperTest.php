<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\PaginateHelper;

	class PaginateHelperTest extends TestCase {
		public function test_Instantiation() {
			$ph = new PaginateHelper(1, 10, 5);
			self::assertEquals(1, $ph->currentPage);

			return;
		}

		public function test_Calculation() {
			$ph = new PaginateHelper(1, 0, 5);
			self::assertEquals(1, $ph->totalPages);
			self::assertEquals(0, $ph->nextPage);
			self::assertEquals(0, $ph->lastPage);
			self::assertEquals(0, $ph->entryOffset);

			$ph = new PaginateHelper(0, 10, 5);
			self::assertEquals(1, $ph->currentPage);

			$ph = new PaginateHelper(1, 5, 10);
			self::assertEquals(1, $ph->totalPages);

			$ph = new PaginateHelper(1, 7, 3);
			self::assertEquals(1, $ph->currentPage);

			$ph = new PaginateHelper(3, 10, 5);
			self::assertEquals(2, $ph->currentPage);

			return;
		}

		public function test_GetPages() {
			$ph = new PaginateHelper(15, 390, 5);
			self::assertEquals(78, $ph->totalPages);

			$pages = $ph->getPages(3);
			self::assertEquals(3, count($pages));

			$pages = $ph->getPages(310);
			self::assertEquals(77, count($pages));

			$ph = new PaginateHelper(75, 390, 5);
			$pages = $ph->getPages(75);
			self::assertEquals(74, count($pages));

			$ph = new PaginateHelper(78, 390, 5);
			$pages = $ph->getPages(5);
			self::assertEquals(4, count($pages));

			return;
		}
	}
