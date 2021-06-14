<?php

	namespace Stoic\Web;

	use Stoic\Web\Resources\UploadedFile;

	/**
	 * Class that normalizes uploaded file information.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class FileUploadHelper {
		/**
		 * Internal collection of uploaded files.
		 *
		 * @var array
		 */
		protected $files = [];


		/**
		 * Instanties a new FileUploadHelper object.
		 *
		 * @param array $files Optional array of uploaded file information, uses $_FILES global if not provided.
		 */
		public function __construct(array $files = null) {
			$files = $files ?? $_FILES;

			foreach (array_keys($files) as $key) {
				$f = $files[$key];

				if (is_array($f['name'])) {
					$numFiles = count($files[$key]['name']);

					for ($i = 0; $i < $numFiles; ++$i) {
						$this->files[$key][] = new UploadedFile($f['error'][$i], $f['name'][$i], $f['size'][$i], $f['tmp_name'][$i], $f['type'][$i]);
					}
				} else {
					$this->files[$key][] = new UploadedFile($f['error'], $f['name'], $f['size'], $f['tmp_name'], $f['type']);
				}
			}

			return;
		}

		/**
		 * Returns any available uploaded files with the given key.
		 *
		 * @param string $key String value of uploaded file key.
		 * @return UploadedFile[]
		 */
		public function getFile(string $key) {
			if (array_key_exists($key, $this->files) === false) {
				return [];
			}

			return $this->files[$key];
		}
	}
