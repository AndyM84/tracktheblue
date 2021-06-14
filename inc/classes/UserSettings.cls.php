<?php

	namespace Zibings;

	use Stoic\Log\Logger;
	use Stoic\Pdo\BaseDbTypes;
	use Stoic\Pdo\PdoDrivers;
	use Stoic\Pdo\PdoHelper;
	use Stoic\Pdo\StoicDbModel;

	/**
	 * Class for representing settings for a user's experience.
	 *
	 * @package Zibings
	 */
	class UserSettings extends StoicDbModel {
		/**
		 * Whether or not emails should be displayed as HTML.
		 *
		 * @var boolean
		 */
		public $htmlEmails;
		/**
		 * Whether or not site sound effects should be played.
		 *
		 * @var boolean
		 */
		public $playSounds;
		/**
		 * Integer identifier of user who owns these settings.
		 *
		 * @var integer
		 */
		public $userId;


		/**
		 * Static method for retrieving a user's settings.
		 *
		 * @param integer $userId Integer identifier for user.
		 * @param PdoHelper $db PdoHelper instance for internal use.
		 * @param Logger|null $log Optional Logger instance for internal use, new instance created by default.
		 * @return UserSettings
		 */
		public static function fromUser(int $userId, PdoHelper $db, Logger $log = null) : UserSettings {
			$ret = new UserSettings($db, $log);
			$ret->userId = $userId;

			if ($ret->read()->isBad()) {
				$ret->userId = 0;
			}

			return $ret;
		}


		/**
		 * Determines if the system should attempt to create a UserSettings in the database.
		 *
		 * @return boolean
		 */
		protected function __canCreate() {
			if ($this->userId < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Determines if the system should attempt to delete a UserSettings from the database.
		 *
		 * @return boolean
		 */
		protected function __canDelete() {
			if ($this->userId < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Determines if the system should attempt to read a UserSettings from the database.
		 *
		 * @return boolean
		 */
		protected function __canRead() {
			if ($this->userId < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Determines if the system should attempt to update a UserSettings in the database.
		 *
		 * @return boolean
		 */
		protected function __canUpdate() {
			if ($this->userId < 1) {
				return false;
			}

			return true;
		}
		
		/**
		 * Initializes a new UserSettings object.
		 *
		 * @return void
		 */
		protected function __setupModel() : void {
			if ($this->db->getDriver()->is(PdoDrivers::PDO_SQLSRV)) {
				$this->setTableName('[dbo].[UserSettings]');
			} else {
				$this->setTableName('UserSettings');
			}

			$this->setColumn('htmlEmails', 'HtmlEmails', BaseDbTypes::BOOLEAN, false, true, true);
			$this->setColumn('playSounds', 'PlaySounds', BaseDbTypes::BOOLEAN, false, true, true);
			$this->setColumn('userId', 'UserID', BaseDbTypes::INTEGER, true, true, false);

			$this->htmlEmails = false;
			$this->playSounds = false;
			$this->userId     = 0;
			
			return;
		}
	}
