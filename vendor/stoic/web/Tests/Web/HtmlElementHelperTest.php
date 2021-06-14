<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Utilities\StringHelper;
	use Stoic\Web\HtmlElementHelper;

	class HtmlElementHelperTest extends TestCase {
		public function test_Instantiation() {
			$hh = new HtmlElementHelper('br');

			self::assertEquals('<br />', $hh->render(true));
			self::assertEquals(0, count($hh->getAttributes()));
			self::assertTrue($hh->getContents()->isEmptyOrNullOrWhitespace());

			return;
		}

		public function test_Attributes() {
			$hh = new HtmlElementHelper('test');

			self::assertEquals('<test />', $hh->render(true)->__toString());
			self::assertEquals(0, count($hh->getAttributes()));

			$hh->addAttribute('testing1', 'value');
			self::assertEquals('<test testing1="value" />', $hh->render(true)->__toString());
			self::assertEquals(1, count($hh->getAttributes()));
			self::assertEquals('value', $hh->getAttributes()['testing1']);

			$hh->addAttribute('testing2', 'valueww');
			self::assertEquals('<test testing1="value" testing2="valueww" />', $hh->render(true)->__toString());
			self::assertEquals(2, count($hh->getAttributes()));
			self::assertEquals('valueww', $hh->getAttributes()['testing2']);

			return;
		}

		public function test_Contents() {
			$hh = new HtmlElementHelper('test');

			self::assertEquals('<test />', $hh->render(true)->__toString());
			self::assertTrue($hh->getContents()->isEmptyOrNullOrWhitespace());

			$hh->setContents('testing');

			self::assertEquals('<test>testing</test>', $hh->render(true)->__toString());
			self::assertFalse($hh->getContents()->isEmptyOrNullOrWhitespace());
			self::assertEquals(0, $hh->getContents()->compare('testing'));

			$hh->setContents(new StringHelper('testarino'));

			self::assertEquals('<test>testarino</test>', $hh->render(true)->__toString());
			self::assertFalse($hh->getContents()->isEmptyOrNullOrWhitespace());
			self::assertEquals(0, $hh->getContents()->compare('testarino'));

			$hh->appendContents('testy');

			self::assertEquals('<test>testarinotesty</test>', $hh->render(true)->__toString());
			self::assertFalse($hh->getContents()->isEmptyOrNullOrWhitespace());
			self::assertEquals(0, $hh->getContents()->compare('testarinotesty'));

			return;
		}

		public function test_Render() {
			$hh = new HtmlElementHelper('test');
			$hh->setContents('testing');
			$hh->addAttribute('test', 'value');

			self::assertEquals('<test test="value">testing</test>', $hh->render(true)->__toString());

			ob_start();
			$hh->render();
			$output = ob_get_contents();
			ob_end_clean();

			self::assertEquals('<test test="value">testing</test>', $output);

			return;
		}
	}
