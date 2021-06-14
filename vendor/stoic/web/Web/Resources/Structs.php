<?php

	namespace Stoic\Web\Resources;

	/**
	 * Struct for holding the information comprising an endpoint used by the API
	 * subsystem.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class ApiEndpoint {
		/**
		 * Value that determines required authentication 'roles'.  Can be boolean,
		 * a string, or an array of strings.
		 *
		 * @var boolean|string|string[]
		 */
		public $authRoles = null;
		/**
		 * Callback to use when the endpoint is the given route.
		 *
		 * @var null|callable
		 */
		public $callback = null;
		/**
		 * String pattern for the URL matching.
		 *
		 * @var null|string
		 */
		public $pattern = null;


		/**
		 * Instantiates a new ApiEndpoint object using the given optional values.
		 *
		 * @param mixed $authRoles String, array of string values, or boolean representing role(s) or a basic authorized/not-authorized requirement for the request.
		 * @param callable $callback Endpoint callback to use when the pattern matches the request.
		 * @param string $pattern String of URL pattern for callback routing.
		 */
		public function __construct($authRoles = false, callable $callback = null, ?string $pattern = null) {
			$this->authRoles = $authRoles;
			$this->callback = $callback;
			$this->pattern = $pattern;

			return;
		}
	}

	/**
	 * Struct for holding the 'predefined' global variables for a page request.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class PageVariables {
		/**
		 * HTTP cookies.
		 *
		 * @var array
		 */
		public $cookie;
		/**
		 * Environment variables.
		 *
		 * @var array
		 */
		public $env;
		/**
		 * HTTP file upload variables.
		 *
		 * @var array
		 */
		public $files;
		/**
		 * HTTP GET variables.
		 *
		 * @var array
		 */
		public $get;
		/**
		 * HTTP POST variables.
		 *
		 * @var array
		 */
		public $post;
		/**
		 * HTTP request variables.
		 *
		 * @var array
		 */
		public $request;
		/**
		 * Server and execution environment information.
		 *
		 * @var array
		 */
		public $server;
		/**
		 * Session variables.
		 *
		 * @var array
		 */
		public $session;


		/**
		 * Static method to return the 'predefined' global variables assigned to
		 * a struct instance.
		 *
		 * @return PageVariables
		 */
		public static function fromGlobals() {
			return new PageVariables(
				$_COOKIE  ?? [],
				$_ENV     ?? [],
				$_FILES   ?? [],
				$_GET     ?? [],
				$_POST    ?? [],
				$_REQUEST ?? [],
				$_SERVER  ?? [],
				$_SESSION ?? []
			);
		}


		/**
		 * Instantiates a new PageVariables instance using the provided arrays for
		 * the 'predefined' variables.
		 *
		 * @param array $cookie HTTP cookies.
		 * @param array $env Environment variables.
		 * @param array $files HTTP file upload variables.
		 * @param array $get HTTP GET variables.
		 * @param array $post HTTP POST variables.
		 * @param array $request HTTP request variables.
		 * @param array $server Server and execution environment information.
		 * @param array $session Session variables.
		 */
		public function __construct(array $cookie, array $env, array $files, array $get, array $post, array $request, array $server, array $session) {
			$this->cookie = $cookie;
			$this->env = $env;
			$this->files = $files;
			$this->get = $get;
			$this->post = $post;
			$this->request = $request;
			$this->server = $server;
			$this->session = $session;

			return;
		}
	}

	/**
	 * Struct for holding information on an uploaded file.
	 *
	 * @codeCoverageIgnore
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class UploadedFile {
		/**
		 * The error code associated with the file upload.
		 *
		 * @var integer
		 */
		public $error;
		/**
		 * Original name of the file on the client machine.
		 *
		 * @var string
		 */
		public $name;
		/**
		 * The size, in bytes, of the uploaded file.
		 *
		 * @var integer
		 */
		public $size;
		/**
		 * The temporary file name of the file on the server.
		 *
		 * @var string
		 */
		public $tmpName;
		/**
		 * The MIME type of the file, if provided by the browser.
		 *
		 * @var string
		 */
		public $type;


		/**
		 * Instantiates a new UploadedFile instance using the provided information.
		 *
		 * @param integer $error Error code for file upload.
		 * @param string $name Original name of the file.
		 * @param integer $size Size of uploaded file in bytes.
		 * @param string $tmpName Temporary file name on server.
		 * @param string $type MIME type of file.
		 */
		public function __construct(int $error, string $name, int $size, string $tmpName, string $type) {
			$this->error = $error;
			$this->name = $name;
			$this->size = $size;
			$this->tmpName = $tmpName;
			$this->type = $type;

			return;
		}

		/**
		 * Returns a string explaining the uploaded file's error code.
		 *
		 * @return string
		 */
		public function getError() : string {
			$ret = '';

			switch ($this->error) {
				case UPLOAD_ERR_OK:
					$ret = "Upload completed successfully.";

					break;
				case UPLOAD_ERR_INI_SIZE:
					$ret = "Upload exceeded maximum file size on server";

					break;
				case UPLOAD_ERR_FORM_SIZE:
					$ret = "Upload exceeded maximum file size in browser";

					break;
				case UPLOAD_ERR_PARTIAL:
					$ret = "Upload didn't complete";

					break;
				case UPLOAD_ERR_NO_FILE:
					$ret = "No file was uploaded";

					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$ret = "Missing temporary folder on server";

					break;
				case UPLOAD_ERR_CANT_WRITE:
					$ret = "Failed to write upload to disk";

					break;
				case UPLOAD_ERR_EXTENSION:
					$ret = "A server extension stopped the upload";

					break;
				default:
					$ret = "Unknown error code during file upload";

					break;
			}

			return $ret;
		}

		/**
		 * Determines if the file was uploaded successfully.
		 *
		 * @return boolean
		 */
		public function isValid() {
			return $this->error == 0;
		}
	}
