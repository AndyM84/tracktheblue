<?php

	namespace Stoic\Web;

	/**
	 * Performs common pagination math operations.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class PaginateHelper {
		/**
		 * Number of the currently active page.
		 *
		 * @var integer
		 */
		public $currentPage;
		/**
		 * Number of entries to count per page.
		 *
		 * @var integer
		 */
		public $entriesPerPage;
		/**
		 * Entry offset based on current page.
		 *
		 * @var integer
		 */
		public $entryOffset;
		/**
		 * Number of the previous page.
		 *
		 * @var integer
		 */
		public $lastPage;
		/**
		 * Number of the next page.
		 *
		 * @var integer
		 */
		public $nextPage;
		/**
		 * Total number of entries in set.
		 *
		 * @var integer
		 */
		public $totalEntries;
		/**
		 * Total number of pages in set.
		 *
		 * @var integer
		 */
		public $totalPages;


		/**
		 * Instantiates a new PaginateHelper object, calculating
		 * offset and total/next/last pages.
		 *
		 * @param integer $currentPage Current page number for set.
		 * @param integer $totalEntries Total number of entires in set.
		 * @param integer $entriesPerPage Number of entries per page in set.
		 */
		public function __construct($currentPage, $totalEntries, $entriesPerPage) {
			$this->currentPage = $currentPage;
			$this->entriesPerPage = $entriesPerPage;
			$this->totalEntries = $totalEntries;

			$this->calculate();

			return;
		}

		/**
		 * Calculates all pagination metrics based on
		 * supplied data.
		 *
		 * @return void
		 */
		private function calculate() {
			if ($this->totalEntries < 1) {
				$this->totalPages = 1;
				$this->nextPage = 0;
				$this->lastPage = 0;
				$this->entryOffset = 0;

				return;
			}

			if ($this->currentPage < 1) {
				$this->currentPage = 1;
			}

			if ($this->entriesPerPage > $this->totalEntries) {
				$this->totalPages = 1;
			} else {
				if (($this->totalEntries % $this->entriesPerPage) == 0) {
					$this->totalPages = floor($this->totalEntries / $this->entriesPerPage);
				} else {
					$this->totalPages = floor(($this->totalEntries / $this->entriesPerPage) + 1);
				}
			}

			if ($this->currentPage > $this->totalPages) {
				$this->currentPage = $this->totalPages;
			}

			$this->entryOffset = (($this->currentPage - 1) * $this->entriesPerPage);
			$this->lastPage = ($this->currentPage < 2) ? 0 : ($this->currentPage - 1);
			$this->nextPage = (($this->totalPages - $this->currentPage) < 1) ? 0 : ($this->currentPage + 1);

			return;
		}

		/**
		 * Returns array of page numbers based on current metrics.
		 *
		 * @param integer $numPages Optional number of page indices to produce.
		 * @return integer[]
		 */
		public function getPages($numPages = 5) {
			$ret = array();
			$st = 0;

			if ($this->currentPage > 0 && $this->totalPages > 0 && $numPages > 0) {
				if ($this->totalPages < $numPages) {
					$st = 1;
				} else {
					$st = floor($this->currentPage - (($numPages / 2) - 1));

					if (($this->totalPages - $st) < $numPages) {
						$st -= (($numPages - ($this->totalPages - $st)) - 1);

						if ($st < 1) {
							// @codeCoverageIgnoreStart
							$st = 1;
							// @codeCoverageIgnoreEnd
						}
					}
				}
			}

			for (; $st < $this->totalPages; $st++) {
				if (count($ret) == $numPages) {
					break;
				}

				$ret[] = $st;
			}

			return $ret;
		}
	}
