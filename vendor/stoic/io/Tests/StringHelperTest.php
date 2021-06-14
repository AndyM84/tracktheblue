<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\StringHelper;
	use Stoic\Utilities\StringJoinOptions;

	class StringHelperTest extends TestCase {
		const CONTAINER_STR_ONCE = "<html><myhead>testing</myhead></html>";
		const CONTAINER_STR_TWICE = "<html><myhead>testing</myhead><myhead>second</myhead></html>";


		public function doValueAndSizeTest(StringHelper $obj, $value, $size) {
			self::assertTrue($obj->data() === $value, "StringHelper has expected data");
			self::assertEquals($size, $obj->length(), "StringHelper has expected length");

			return;
		}

		public function test_Initialization() {
			$test = new StringHelper();
			$this->doValueAndSizeTest($test, null, 0);

			$test = new StringHelper("five");
			$this->doValueAndSizeTest($test, "five", 4);

			return;
		}

		public function test_Append() {
			$test = new StringHelper();
			$this->doValueAndSizeTest($test, null, 0);

			$test->append("five");
			$this->doValueAndSizeTest($test, "five", 4);

			$test->append("five");
			$this->doValueAndSizeTest($test, "fivefive", 8);

			$test = new StringHelper();
			$this->doValueAndSizeTest($test, null, 0);

			$test->append(new StringHelper("five"));
			$this->doValueAndSizeTest($test, "five", 4);

			$test->append("five");
			$this->doValueAndSizeTest($test, "fivefive", 8);

			$test = new StringHelper();
			$this->doValueAndSizeTest($test, null, 0);

			$test->append(new StringHelper("five"));
			$this->doValueAndSizeTest($test, "five", 4);

			$test->append(new StringHelper("five"));
			$this->doValueAndSizeTest($test, "fivefive", 8);

			$test = new StringHelper("five");
			$this->doValueAndSizeTest($test, "five", 4);

			$test->append("five");
			$this->doValueAndSizeTest($test, "fivefive", 8);

			$test->append("five");
			$this->doValueAndSizeTest($test, "fivefivefive", 12);

			return;
		}

		public function test_At() {
			$test = new StringHelper("five");
			
			self::assertEquals('f', $test->at(0), "StringHelper has expected data");
			self::assertEquals('v', $test->at(2), "StringHelper has expected data");

			self::expectException(\OutOfRangeException::class);
			$test->at(35);

			return;
		}

		public function test_Clear() {
			$test = new StringHelper("five");
			$this->doValueAndSizeTest($test, "five", 4);

			$test->clear();
			$this->doValueAndSizeTest($test, null, 0);

			return;
		}

		public function test_Compare() {
			$blank = new StringHelper();
			$fourC = new StringHelper("four");
			$fiveC = new StringHelper("five");
			$fourI = new StringHelper("FouR");
			$fiveI = new StringHelper("fIVe");

			self::assertFalse($blank->compare('test'));

			self::assertGreaterThan(0, $fourC->compare($fiveC), "StringHelper compared as expected");
			self::assertLessThan(0, $fiveC->compare($fourC), "StringHelper compared as expected");
			self::assertEquals(0, $fiveC->compare($fiveC), "StringHelper compared as expected");
			self::assertGreaterThan(0, $fiveC->compare($fiveI), "StringHelper compared as expected");
			self::assertLessThan(0, $fourI->compare($fiveC), "StringHelper compared as expected");
			self::assertFalse($fourI->compare('test', 55));

			self::assertGreaterThan(0, $fourC->compare($fiveC, 2), "StringHelper compared as expected");
			self::assertLessThan(0, $fiveC->compare($fourC, 2), "StringHelper compared as expected");
			self::assertEquals(0, $fiveC->compare($fiveC, 2), "StringHelper compared as expected");
			self::assertGreaterThan(0, $fiveC->compare($fiveI, 2), "StringHelper compared as expected");
			self::assertLessThan(0, $fourI->compare($fiveC, 2), "StringHelper compared as expected");

			self::assertGreaterThan(0, $fourC->compare($fiveC, null, true), "StringHelper compared as expected");
			self::assertLessThan(0, $fiveC->compare($fourC, null, true), "StringHelper compared as expected");
			self::assertEquals(0, $fiveC->compare($fiveC, null, true), "StringHelper compared as expected");
			self::assertEquals(0, $fiveC->compare($fiveI, null, true), "StringHelper compared as expected");
			self::assertGreaterThan(0, $fourI->compare($fiveC, null, true), "StringHelper compared as expected");

			self::assertGreaterThan(0, $fourC->compare($fiveC, 2, true), "StringHelper compared as expected");
			self::assertLessThan(0, $fiveC->compare($fourC, 2, true), "StringHelper compared as expected");
			self::assertEquals(0, $fiveC->compare($fiveC, 2, true), "StringHelper compared as expected");
			self::assertEquals(0, $fiveC->compare($fiveI, 2, true), "StringHelper compared as expected");
			self::assertGreaterThan(0, $fourI->compare($fiveC, 2, true), "StringHelper compared as expected");

			return;
		}

		public function test_Copy() {
			$test1 = new StringHelper("five");
			$test2 = $test1->copy();

			self::assertEquals($test1->data(), $test2->data(), "StringHelper copied as expected");
			self::assertEquals($test1->length(), $test2->length(), "StringHelper copied as expected");

			$test2->clear();
			self::assertNotEquals($test1->data(), $test2->data(), "StringHelper cleared separately");
			self::assertNotEquals($test1->length(), $test2->length(), "StringHelper cleared separately");

			return;
		}

		public function test_EndsWith() {
			$test = new StringHelper("five");

			self::assertTrue($test->endsWith("e"), "StringHelper endsWith correctly");
			self::assertTrue($test->endsWith("five"), "StringHelper endsWith correctly");
			self::assertTrue($test->endsWith("vE", true), "StringHelper endsWith correctly");
			self::assertFalse($test->endsWith("Ve"), "StringHelper endsWith correctly");

			return;
		}

		public function test_Find() {
			$blank = new StringHelper();
			$test = new StringHelper("five");

			self::assertFalse($blank->find('test'));

			self::assertFalse($test->find('yes') !== false, "StringHelper doesn't find disparate string");
			self::assertFalse($test->find('yes', 1) !== false, "StringHelper doesn't find disparate string");
			self::assertFalse($test->find('yes', 1, true) !== false, "StringHelper doesn't find disparate string");

			self::assertTrue($test->find('iv') !== false, "StringHelpeer finds string as expected");
			self::assertTrue($test->find('iv', 1) !== false, "StringHelper finds string as expected");
			self::assertTrue($test->find('iv', 2) === false, "StringHelper doesn't find string at wrong offset");
			self::assertTrue($test->find('iv', 1, true) !== false, "StringHelper finds string as expected");

			return;
		}

		public function test_FirstChar() {
			$blank = new StringHelper();
			$test = new StringHelper("five");

			self::assertNull($blank->firstChar());

			self::assertEquals("f", $test->firstChar(), "StringHelper returns correct first character");

			return;
		}

		public function test_IsEmptyOrNull() {
			$test = new StringHelper("five");

			self::assertFalse($test->isEmptyOrNull(), "StringHelper reports non-empty correctly");

			$test->clear();
			self::assertTrue($test->isEmptyOrNull(), "StringHelper reports empty correctly");

			$test = new StringHelper("  ");
			self::assertFalse($test->isEmptyOrNull(), "StringHelper reports whitespace only correctly");

			return;
		}

		public function test_IsEmptyOrNullOrWhitespace() {
			$test = new StringHelper("five");

			self::assertFalse($test->isEmptyOrNullOrWhitespace(), "StringHelper reports non-empty correctly");

			$test->clear();
			self::assertTrue($test->isEmptyOrNullOrWhitespace(), "StringHelper reports empty correctly");

			$test = new StringHelper("  ");
			self::assertTrue($test->isEmptyOrNullOrWhitespace(), "StringHelper reports whitespace only correctly");

			return;
		}

		public function test_LastChar() {
			$blank = new StringHelper();
			$test = new StringHelper("five");

			self::assertNull($blank->lastChar());

			self::assertEquals("e", $test->lastChar(), "StringHelper reports last character correctly");

			return;
		}

		public function test_Replace() {
			$test = new StringHelper("four five two one");
			$test->replace("five", "three");
			self::assertEquals("four three two one", $test->data(), "StringHelper replaces correctly");

			$count = 0;
			$test = new StringHelper("four five two one");
			$test->replace("five", "three", $count);
			self::assertEquals("four three two one", $test->data(), "StringHelper replaces correctly");
			self::assertEquals(1, $count, "StringHelper sets replace count correctly");

			$test = new StringHelper("four five six one");
			$test->replace(array("five", "six"), array("three", "two"));
			self::assertEquals("four three two one", $test->data(), "StringHelper replaces arrays correctly");

			return;
		}

		public function test_ReplaceContained() {
			$test = new StringHelper(self::CONTAINER_STR_ONCE);
			$test->replaceContained("<myhead>", "</myhead>", "TEST_TEXT");
			self::assertEquals("<html>TEST_TEXT</html>", $test->data(), "StringHelper replaces simple contained string correctly");
			self::assertFalse($test->replaceContained('testingtestingtesting', 'testingtestingtestingtesting', 'another'));

			$test = new StringHelper(self::CONTAINER_STR_ONCE);
			$test->replaceContained("<myhead>", "</myHead>", "TEST_TEXT");
			self::assertEquals(self::CONTAINER_STR_ONCE, $test->data(), "StringHelper replaces simple contained string correctly");

			$test = new StringHelper(self::CONTAINER_STR_ONCE);
			$test->replaceContained("<myhead>", "</myHead>", "TEST_TEXT", true);
			self::assertEquals("<html>TEST_TEXT</html>", $test->data(), "StringHelper replaces simple contained string correctly");

			$test = new StringHelper(self::CONTAINER_STR_ONCE);
			$test->replaceContained("<myhead>", "</myhead>", "_%%_%TEXT%_%%_");
			self::assertEquals("<html>_%%_testing_%%_</html>", $test->data(), "StringHelper replaces simple contained string correctly");

			$test = new StringHelper(self::CONTAINER_STR_ONCE);
			$test->replaceContained("<myhead>", "</myHead>", "_%%_%TEXT%_%%_", true);
			self::assertEquals("<html>_%%_testing_%%_</html>", $test->data(), "StringHelper replaces simple contained string correctly");

			$test = new StringHelper(self::CONTAINER_STR_TWICE);
			$test->replaceContained("<myhead>", "</myhead>", "TWICE_TEXT");
			self::assertEquals("<html>TWICE_TEXTTWICE_TEXT</html>", $test->data(), "StringHelper replaces multiple contained strings correctly");

			$test = new StringHelper(self::CONTAINER_STR_TWICE);
			$test->replaceContained("<myhead>", "</myHead>", "TWICE_TEXT", true);
			self::assertEquals("<html>TWICE_TEXTTWICE_TEXT</html>", $test->data(), "StringHelper replaces multiple contained strings correctly");

			$test = new StringHelper(self::CONTAINER_STR_TWICE);
			$test->replaceContained("<myhead>", "</myhead>", "_%%_%TEXT%_%%_");
			self::assertEquals("<html>_%%_testing_%%__%%_second_%%_</html>", $test->data(), "StringHelper replaces multiple contained strings correctly");

			$test = new StringHelper(self::CONTAINER_STR_TWICE);
			$test->replaceContained("<myhead>", "</myHead>", "_%%_%TEXT%_%%_", true);
			self::assertEquals("<html>_%%_testing_%%__%%_second_%%_</html>", $test->data(), "StringHelper replaces multiple contained strings correctly");

			return;
		}

		public function test_ReplaceOnce() {
			$test = new StringHelper("four four four four");
			$test->replaceOnce("four", "five");
			self::assertFalse($test->replaceOnce('', ''));
			self::assertEquals("five four four four", $test->data(), "StringHelper replaces only first occurrence of string correctly");

			$test = new StringHelper("four four four four");
			$test->replaceOnce("four", "five", 1);
			self::assertEquals("four five four four", $test->data(), "StringHelper replaces only first occurrence of string correctly");

			$test = new StringHelper("four four four four");
			$test->replaceOnce("fOur", "five");
			self::assertEquals("four four four four", $test->data(), "StringHelper replaces only first occurrence of string correctly");

			$test = new StringHelper("four four four four");
			$test->replaceOnce("fOur", "five", 0, true);
			self::assertEquals("five four four four", $test->data(), "StringHelper replaces only first occurrence of string correctly");

			return;
		}

		public function test_StartsWith() {
			$test = new StringHelper("five");

			self::assertTrue($test->startsWith("fi"), "StringHelper startsWith correctly");
			self::assertTrue($test->startsWith("five"), "StringHelper startsWith correctly");
			self::assertTrue($test->startsWith("FI", true), "StringHelper startsWith correctly");
			self::assertFalse($test->startsWith("fI"), "StringHelper startsWith correctly");

			return;
		}

		public function test_SubString() {
			$test = new StringHelper("a quick grey fox");

			self::assertEquals("a quick", $test->subString(0, 7), "StringHelper gets sub string correctly");
			self::assertEquals("fox", $test->subString(-3), "StringHelper gets sub string correctly");

			return;
		}

		public function test_ToLowerToUpper() {
			$test = new StringHelper("five");

			$test->toUpper();
			self::assertEquals("FIVE", $test->data(), "StringHelper capitalizes correctly");

			$test->toLower();
			self::assertEquals("five", $test->data(), "StringHelper decapitalizes correctly");

			return;
		}

		public function test_ToString() {
			$test = new StringHelper("five");

			$buf = sprintf("%s", $test);
			self::assertEquals("five", $buf, "StringHelper converts to string correctly");

			return;
		}

		public function test_Join() {
			$cfg = new StringJoinOptions("/", false);
			$cfg2 = new StringJoinOptions("/", true);

			$join1 = StringHelper::join("", "five", "four", "three");
			$join2 = StringHelper::join("-", "five", "four", "three");
			$join3 = StringHelper::join($cfg, "five", "four", "three");
			$join4 = StringHelper::join($cfg, "five/", "four", "/three");
			$join5 = StringHelper::join($cfg2, "five/", "four", "/three");

			self::assertEquals("fivefourthree", $join1->data(), "StringHelper joins strings correctly");
			self::assertEquals("five-four-three", $join2->data(), "StringHelper joins strings correctly");
			self::assertEquals("five/four/three", $join3->data(), "StringHelper joins strings correctly");
			self::assertEquals("five//four//three", $join4->data(), "StringHelper joins strings correctly");
			self::assertEquals("five/four/three", $join5->data(), "StringHelper joins strings correctly");

			self::expectException(\InvalidArgumentException::class);
			StringHelper::join();

			return;
		}
	}
