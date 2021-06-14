<?php

	namespace Stoic\Tests\Web;

	use PHPUnit\Framework\TestCase;

	use Stoic\Web\FileUploadHelper;

	class FileUploadHelperTest extends TestCase {
		public function test_Instantiation() {
			$fuh = new FileUploadHelper([
				'test1' => [
					'name' => '',
					'type' => '',
					'size' => 0,
					'tmp_name' => '',
					'error' => UPLOAD_ERR_OK
				],
				'test2' => [
					'name' => ['', ''],
					'type' => ['', ''],
					'size' => [0, 0],
					'tmp_name' => ['', ''],
					'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK]
				]
			]);

			self::assertTrue(count($fuh->getFile('test1')) == 1);
			self::assertTrue(count($fuh->getFile('test2')) == 2);
			self::assertTrue(count($fuh->getFile('test3')) == 0);

			return;
		}
	}
