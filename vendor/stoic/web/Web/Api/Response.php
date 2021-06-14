<?php

	namespace Stoic\Web\Api;

	use Stoic\Utilities\EnumBase;
	use Stoic\Web\Resources\HttpStatusCodes;

	/**
	 * Class to supply a semi-structured response to API requests.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class Response {
		/**
		 * Data returned by response.
		 *
		 * @var mixed
		 */
		protected $data = null;
		/**
		 * HTTP status code for response.
		 *
		 * @var HttpStatusCodes
		 */
		protected $status = null;


		/**
		 * Instantiates a new API response object.
		 *
		 * @param integer|HttpStatusCodes $status Optional HTTP status code value for response.
		 * @param mixed $data Optional data to store in response.
		 */
		public function __construct($status = null, $data = null) {
			if ($status !== null) {
				$this->status = EnumBase::tryGetEnum($status, HttpStatusCodes::class);
			}

			if ($data !== null) {
				$this->data = $data;
			}

			return;
		}

		/**
		 * Retrieves the raw data for the response.
		 *
		 * @return mixed
		 */
		public function getData() {
			return $this->data;
		}

		/**
		 * Retrieves the HTTP status code for the response.
		 *
		 * @return HttpStatusCodes
		 */
		public function getStatus() : HttpStatusCodes {
			return $this->status ?? new HttpStatusCodes();
		}

		/**
		 * Shortcut for setting Response to be an error. Same as calling
		 * Response::setStatus() and Response::setData().
		 *
		 * @param string $message Error message to use as response data.
		 * @param integer|HttpStatusCodes $status Optional HTTP status code for response, defaults to `HttpStatusCodes::INTERNAL_SERVER_ERROR`.
		 * @throws \InvalidArgumentException
		 * @return void
		 */
		public function setAsError(string $message, $status = HttpStatusCodes::INTERNAL_SERVER_ERROR) : void {
			$status = EnumBase::tryGetEnum($status, HttpStatusCodes::class);

			if ($status->getValue() === null) {
				throw new \InvalidArgumentException("Invalid status code supplied for Response");
			}

			$this->data = $message;
			$this->status = $status;

			return;
		}

		/**
		 * Sets the response data.
		 *
		 * @param mixed $data Data for response to return.
		 * @return void
		 */
		public function setData($data) : void {
			$this->data = $data;

			return;
		}

		/**
		 * Sets the response HTTP status code.
		 *
		 * @param integer|HttpStatusCodes $status HTTP status code for response to return.
		 * @return void
		 */
		public function setStatus($status) : void {
			$this->status = EnumBase::tryGetEnum($status, HttpStatusCodes::class);

			return;
		}
	}
