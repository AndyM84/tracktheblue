<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\PageHelper;

	class CommunicativePageHelper extends PageHelper {
		public static function getRegisteredPages() {
			return static::$pages;
		}

		public static function clearRegisteredPages() {
			static::$pages = [];
		}
	}

	class PageHelperTest extends TestCase {
		public function test_Instantiation() {
			self::assertEquals(0, count(CommunicativePageHelper::getRegisteredPages()));

			$ph = new ParameterHelper();
			$page = CommunicativePageHelper::getPage("testing", $ph, $ph, $ph);
			self::assertEquals(1, count(CommunicativePageHelper::getRegisteredPages()));
			self::assertEquals('testing', $page->getName()->__toString());

			CommunicativePageHelper::clearRegisteredPages();

			return;
		}

		public function test_MetaTags() {
			self::assertEquals(0, count(CommunicativePageHelper::getRegisteredPages()));

			$page = CommunicativePageHelper::getPage("testing");
			self::assertEquals(0, count($page->getMetaTags()));

			$page->addMetaTag('test', 'value');
			
			self::assertEquals(1, count($page->getMetaTags()));
			self::assertEquals('<meta name="test" content="value" />', $page->getMetaTags()[0]->render(true)->__toString());

			CommunicativePageHelper::clearRegisteredPages();

			return;
		}

		public function test_Title() {
			self::assertEquals(0, count(CommunicativePageHelper::getRegisteredPages()));

			$page = CommunicativePageHelper::getPage("testing");
			
			$page->setTitlePrefix('');
			self::assertEquals('', $page->getTitle()->__toString());

			$page->setTitle('Testing');
			self::assertEquals('Testing', $page->getTitle()->__toString());

			$page->setTitlePrefix('Test', ' -> ');
			self::assertEquals('Test -> Testing', $page->getTitle()->__toString());

			CommunicativePageHelper::clearRegisteredPages();

			return;
		}

		public function test_Root() {
			$_SERVER['SCRIPT_NAME'] = '/test/file.php';
			$_SERVER['HTTPS'] = 'off';
			$_SERVER['HTTP_HOST'] = 'somedomain.testing.com';
			$page = CommunicativePageHelper::getPage("test/file.php");

			try {
				$page->setRoot('');
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot provide empty root path to PageHelper object", $ex->getMessage());
			}

			$page->setRoot('test');
			self::assertEquals('test/', $page->getRoot()->__toString());
			self::assertEquals('test/', $page->getRootUrlPath()->__toString());
			self::assertEquals('http://somedomain.testing.com/test/', $page->getRootUrlPath(true)->__toString());

			self::assertEquals('/', PageHelper::getRootPath('/test/file.php'));

			CommunicativePageHelper::clearRegisteredPages();

			return;
		}

		public function test_AssetPath() {
			$_SERVER['SCRIPT_NAME'] = '/test/file.php';
			$_SERVER['HTTPS'] = 'off';
			$_SERVER['HTTP_HOST'] = 'somedomain.testing.com';
			$page = CommunicativePageHelper::getPage("file.php");

			try {
				$page->getAssetPath('');
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot provide empty asset path for conversion", $ex->getMessage());
			}

			self::assertEquals('/test/someFile.css', $page->getAssetPath('~someFile.css')->__toString());
			self::assertEquals('/test/someFile.css', $page->getAssetPath('~/someFile.css')->__toString());
			self::assertEquals('http://somedomain.testing.com/test/someFile.css', $page->getAssetPath('~/someFile.css', null, true)->__toString());
			self::assertEquals('/test/someFile.php?test1=val1&test2=val2', $page->getAssetPath('~/someFile.php', ['test1' => 'val1', 'test2' => 'val2'])->__toString());

			CommunicativePageHelper::clearRegisteredPages();

			return;
		}
	}
