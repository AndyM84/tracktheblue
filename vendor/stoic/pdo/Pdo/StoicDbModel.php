<?php

	namespace Stoic\Pdo;

	/**
	 * Abstract base class that provides simplistic ORM
	 * functionality via the PdoHelper wrapper and
	 * without much fuss/overhead.
	 *
	 * @package Stoic\Pdo
	 * @version 1.0.3
	 */
	abstract class StoicDbModel extends BaseDbModel {
		/**
		 * Internal PdoHelper instance;
		 *
		 * @var PdoHelper
		 */
		protected $db;


		/**
		 * Optional method to initialize an object
		 * after the constructor has finished.
		 *
		 * @throws \InvalidArgumentException
		 * @return void
		 */
		protected function __initialize() : void {
			parent::__initialize();

			if (!($this->db instanceof PdoHelper)) {
				throw new \InvalidArgumentException("StoicDbModel and derived classes require a PdoHelper");
			}

			return;
		}
	}
