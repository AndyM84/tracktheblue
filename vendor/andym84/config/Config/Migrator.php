<?php

	namespace AndyM84\Config;

	/**
	 * Class that performs migration of a
	 * configuration file between versions.
	 *
	 * @version 1.0
	 * @author Andrew Male (AndyM84)
	 * @package AndyM84\Config
	 */
	class Migrator {
		/**
		 * Collection of migration files containing
		 * instructions.
		 *
		 * @var MigrationFile[]
		 */
		protected $files = array();
		/**
		 * Path to the directory containing migration
		 * instruction files.
		 *
		 * @var string
		 */
		protected $migrationDirectory = null;
		/**
		 * Extension to use for migration instruction
		 * files.
		 *
		 * Defaults to '.cfg'.
		 *
		 * @var string
		 */
		protected $migrationExtension = null;
		/**
		 * Path to the settings file which will have
		 * the migration instructions applies to it.
		 *
		 * @var string
		 */
		protected $settingsFile = null;


		/**
		 * Instantiates a new Migrator, attempting to load all
		 * instruction files in preparation for migration.
		 *
		 * @param string $migrationDirectory The directory path where the instruction files exist.
		 * @param string $settingsFile The name of the settings file to read/generate, defaults to 'siteSettings.json'.
		 * @param string $migrationExtension The file extension used by the instruction files, defaults to '.cfg'.
		 * @throws \InvalidArgumentException Thrown if the migration directory doesn''t exist or the extension is empty/null.
		 */
		public function __construct($migrationDirectory, $settingsFile = 'siteSettings.json', $migrationExtension = '.cfg') {
			$migrationDirectory = str_replace("\\", "/", $migrationDirectory);

			if (!is_dir($migrationDirectory)) {
				throw new \InvalidArgumentException("Invalid migration directory");
			}

			if (substr($migrationDirectory, -1) !== '/') {
				$migrationDirectory .= "/";
			}

			$this->migrationDirectory = $migrationDirectory;

			if ($migrationExtension === null || empty(trim($migrationExtension))) {
				throw new \InvalidArgumentException("Invalid migration extension");
			}

			$this->settingsFile = $settingsFile;
			$this->migrationExtension = $migrationExtension;

			foreach (array_values(glob("{$this->migrationDirectory}*{$this->migrationExtension}")) as $file) {
				$fh = @fopen($file, 'r');

				if ($fh) {
					$lines = array();

					while (($buf = fgets($fh)) !== false) {
						$lines[] = trim(preg_replace("/\R/", "", $buf));
					}

					if (feof($fh) && count($lines) > 0) {
						$this->files[] = new MigrationFile($file, $lines, $this->migrationExtension);
					}

					@fclose($fh);
				}
			}

			if (count($this->files) > 0) {
				usort($this->files, function ($a, $b) {
															if ($a->origVersion == $b->origVersion) {
																return 0;
															}

															return ($a->origVersion < $b->origVersion) ? -1 :  1;
														});
			}

			return;
		}

		/**
		 * Perform migration of settings file using the loaded
		 * instruction files.
		 *
		 * @return void
		 */
		public function migrate() {
			$currentSettings = new ConfigContainer();

			if (file_exists($this->settingsFile)) {
				$currentSettings = new ConfigContainer(file_get_contents($this->settingsFile));
			}

			if (!$currentSettings->has('configVersion')) {
				$currentSettings->set('configVersion', 0, FieldTypes::INTEGER);
			}

			$filesToApply = array();
			$currentVersion = $currentSettings->get('configVersion');

			foreach (array_values($this->files) as $file) {
				if ($file->origVersion >= $currentVersion) {
					$filesToApply[] = $file;
				}
			}

			foreach (array_values($filesToApply) as $file) {
				foreach (array_values($file->actions) as $action) {
					switch ($action->operator->getValue()) {
						case MigrationOperators::ADD:
							if (!$currentSettings->has($action->field)) {
								$currentSettings->set($action->field, $action->value, $action->type->getValue());
							}

							break;
						case MigrationOperators::CHANGE:
							$currentSettings->set($action->field, $action->value);

							break;
						case MigrationOperators::REMOVE:
							$currentSettings->remove($action->field);

							break;
						case MigrationOperators::RENAME:
							$currentSettings->rename($action->field, $action->value);

							break;
						default:

							break;
					}
				}

				$currentSettings->set('configVersion', intval($file->destVersion));
			}

			file_put_contents($this->settingsFile, json_encode($currentSettings, JSON_PRETTY_PRINT));

			return;
		}
	}
