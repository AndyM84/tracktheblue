<?php

	namespace Stoic\Tests\Utilities;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\FileHelper;

	class CommunicativeFileHelper extends FileHelper {
		public function __construct($corePath) {
			parent::__construct($corePath);

			return;
		}

		public function getProcessedPath($path) {
			return $this->processRoot($path);
		}
	}

	function anotherCounterFunction($returnOnly = false) {
		static $count = 0;

		if ($returnOnly !== false) {
			return $count;
		}

		return ++$count;
	}

	class FileHelperTest extends TestCase {
		const FOLDER_ONE = 'randomFolderWithLongName';
		const FOLDER_TWO = 'anotherRandomFolderWithLongName';


		public function test_FakeCorePathThrowsException() {
			self::expectException(\Exception::class);
			$io = new FileHelper("some-directory-that-shouldnt-exist");

			return;
		}

		public function test_CorePathing() {
			$io = new CommunicativeFileHelper('./');

			self::assertEquals('./', $io->getRelativePath());
			self::assertEquals('./someFile.php', $io->getProcessedPath('~someFile.php'));
			self::assertEquals('./someFile.php', $io->getProcessedPath('~/someFile.php'));
			self::assertEquals('./someDir/someFile.php', $io->getProcessedPath('~someDir/someFile.php'));
			self::assertEquals('./someDir/someFile.php', $io->getProcessedPath('~/someDir/someFile.php'));

			return;
		}

		public function test_PathJoin() {
			$io = new FileHelper("./");

			self::assertEquals('./', $io->pathJoin('~/'));
			self::assertEquals('./test/', $io->pathJoin('~/', '/test/'));
			self::assertEquals('/', $io->pathJoin('/'));
			self::assertEquals('./testing/some/dirs/', $io->pathJoin('~/', 'testing/', '\\some', 'dirs/'));

			return;
		}

		public function test_FileOperations() {
			$io = new FileHelper('./');

			self::assertFalse($io->fileExists('~/someFile.txt'));

			self::assertFalse($io->touchFile(''));

			$io->touchFile('~/someFile.txt');
			$io->touchFile('~/someFile.txt', time(), time());
			self::assertTrue($io->fileExists('~/someFile.txt'));

			$io->removeFile('~/someFile.txt');
			self::assertFalse($io->fileExists('~/someFile.txt'));

			self::assertTrue($io->putContents('~/someFile.txt', "Testing") > 0);
			self::assertTrue($io->fileExists('~/someFile.txt'));
			self::assertEquals('Testing', $io->getContents('~/someFile.txt'));
			self::assertTrue($io->removeFile('~/someFile.txt'));

			$io->putContents('~/someFile.txt', 'Testing');
			self::assertFalse($io->fileExists('~/someFile_copy.txt'));
			
			$io->copyFile('~/someFile.txt', '~/someFile_copy.txt');
			self::assertTrue($io->fileExists('~/someFile_copy.txt'));
			self::assertEquals($io->getContents('~/someFile.txt'), $io->getContents('~/someFile_copy.txt'));

			self::assertFalse($io->removeFile(''));

			self::assertTrue($io->removeFile('~/someFile.txt'));
			self::assertTrue($io->removeFile('~/someFile_copy.txt'));

			return;
		}

		public function test_GetContentsEmptyArgsThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->getContents('');
			
			return;
		}
		
		public function test_PutContentsEmptyArgsThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			self::assertFalse($io->putContents('', '') === true);

			return;
		}
		
		public function test_PutContentsBlankContentsThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			self::assertFalse($io->putContents('~/someFile.txt', '') === true);

			return;
		}
		
		public function test_CopyFileEmptyArgsThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->copyFile('', '');

			return;
		}

		public function test_CopyFileNoDestinationThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->copyFile('~/someFile.txt', '');

			return;
		}
		
		public function test_CopyFileDirectoriesThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->copyFile('~/someFile/', '~/anotherFile/');

			return;
		}
		
		public function test_CopyFileInvalidSourceThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->copyFile('~/nonExistentFile.txt', '~/someOtherFile.txt');

			return;
		}

		public function test_FolderOperations() {
			$io = new FileHelper('./');
			$folder = '~/' . self::FOLDER_ONE;

			self::assertFalse($io->folderExists($folder));

			$io->makeFolder($folder);
			self::assertTrue($io->folderExists($folder));

			$io->makeFolder($folder);
			
			self::assertEquals(0, count($io->getFolderItems($folder)));
			self::assertEquals(0, count($io->getFolderItems($folder, true)));
			self::assertEquals(0, count($io->getFolderFiles($folder)));
			self::assertEquals(0, count($io->getFolderFolders($folder)));
			self::assertNull($io->getFolderItems(''));

			$io->touchFile($folder . '/someFile.txt');
			$io->makeFolder($folder . '/someFolder');

			self::assertEquals('./' . self::FOLDER_ONE . '/someFile.txt', $io->getFolderFiles($folder)[0]);
			self::assertEquals('./' . self::FOLDER_ONE . '/someFolder/', $io->getFolderFolders($folder)[0]);
			self::assertEquals(2, count($io->getFolderItems($folder)));

			$io->copyFolder('~/' . self::FOLDER_ONE, '~/' . self::FOLDER_TWO);
			self::assertEquals(array(
			  './' . self::FOLDER_TWO . '/someFile.txt',
			  './' . self::FOLDER_TWO . '/someFolder/'
			), $io->getFolderItems('~/' . self::FOLDER_TWO));

			self::assertNull($io->getFolderItems('./folderThatdoesntExist'));

			self::assertTrue($io->removeFile('~/' . self::FOLDER_TWO . '/someFile.txt'));
			self::assertTrue($io->removeFolder('~/' . self::FOLDER_TWO . '/someFolder'));
			self::assertTrue($io->removeFolder('~/' . self::FOLDER_TWO));

			self::assertFalse($io->removeFolder(''));

			self::assertTrue($io->removeFile($folder . '/someFile.txt'));
			self::assertTrue($io->removeFolder($folder . '/someFolder'));
			self::assertTrue($io->removeFolder($folder));
			self::assertFalse($io->folderExists($folder));

			return;
		}

		public function test_CopyFolderEmptyArgsThrowsException() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->copyFolder('', '');

			return;
		}

		public function test_CopyFolderNonExistentSourceThrowsError() {
			$io = new FileHelper('./');
			self::expectException(\Exception::class);
			$io->copyFolder('~/nonExistentFolder', '~/' . self::FOLDER_ONE);

			return;
		}

		public function test_LoadOperations() {
			$io = new FileHelper('./', ['someRandomFile.php']);

			try {
				$io->load('~/ioTests/test1.php');
				self::assertFalse(true);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid file provided for FileHelper::load() -> ~/ioTests/test1.php", $ex->getMessage());
			}

			$io->makeFolder('~/ioTests');
			$io->putContents('~/ioTests/test1.php', '<?php Stoic\Tests\Utilities\anotherCounterFunction(); ?>');
			$io->putContents('~/ioTests/test2.php', '<?php Stoic\Tests\Utilities\anotherCounterFunction(); ?>');
			$io->putContents('~/ioTests/test3.php', '<?php Stoic\Tests\Utilities\anotherCounterFunction(); ?>');

			$io->load('~/ioTests/test1.php');
			self::assertEquals(1, anotherCounterFunction(true));

			$io->loadGroup(array('~/ioTests/test2.php', '~/ioTests/test3.php'));
			self::assertEquals(3, anotherCounterFunction(true));

			$io->load('~/ioTests/test1.php', true);
			self::assertEquals(4, anotherCounterFunction(true));

			$io->loadGroup(array('~/ioTests/test2.php', '~/ioTests/test3.php'), true);
			self::assertEquals(6, anotherCounterFunction(true));

			self::assertEquals("~/ioTests/test1.php", $io->load("~/ioTests/test1.php"));
			self::assertEquals(2, count($io->loadGroup(['~/ioTests/test2.php', '~/ioTests/test3.php'])));

			try {
				$io->load('');
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Invalid file path provided for FileHelper::load()", $ex->getMessage());
			}

			self::assertEquals(0, count($io->loadGroup(array())));

			$io->removeFile('~/ioTests/test1.php');
			$io->removeFile('~/ioTests/test2.php');
			$io->removeFile('~/ioTests/test3.php');
			$io->removeFolder('~/ioTests');

			return;
		}
	}
