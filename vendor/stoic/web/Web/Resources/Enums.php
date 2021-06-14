<?php

	namespace Stoic\Web\Resources;

	use Stoic\Utilities\EnumBase;

	/**
	 * Enumerated HTTP request verbs.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class RequestType extends EnumBase {
		const DELETE  = 1;
		const ERROR   = 2;
		const GET     = 3;
		const HEAD    = 4;
		const OPTIONS = 5;
		const POST    = 6;
		const PUT     = 7;
	}

	/**
	 * Enumerated HTTP status codes.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class HttpStatusCodes extends EnumBase {
		const CONTINU                          = 100;
		const PROTO_SWITCH                     = 101;
		const PROCESSING                       = 102;
		const OK                               = 200;
		const CREATED                          = 201;
		const ACCEPTED                         = 202;
		const NON_AUTH_INFO                    = 203;
		const NO_CONTENT                       = 204;
		const RESET_CONTENT                    = 205;
		const PARTIAL_CONTENT                  = 206;
		const MULTI_STATUS                     = 207;
		const ALREADY_REPORTED                 = 208;
		const IM_USED                          = 226;
		const MULTIPLE_CHOICES                 = 300;
		const MOVED_PERMANENTLY                = 301;
		const FOUND                            = 302;
		const SEE_OTHER                        = 303;
		const NOT_MODIFIED                     = 304;
		const USE_PROXY                        = 305;
		const SWITCH_PROXY                     = 306;
		const TEMPORARY_REDIRECT               = 307;
		const PERMANENT_REDIRECT               = 308;
		const BAD_REQUEST                      = 400;
		const UNAUTHORIZED                     = 401;
		const PAYMENT_REQUIRED                 = 402;
		const FORBIDDEN                        = 403;
		const NOT_FOUND                        = 404;
		const METHOD_NOT_ALLOWED               = 405;
		const NOT_ACCEPTABLE                   = 406;
		const PROXY_AUTH_REQUIRED              = 407;
		const REQUEST_TIMEOUT                  = 408;
		const CONFLICT                         = 409;
		const GONE                             = 410;
		const LENGTH_REQUIRED                  = 411;
		const PRECONDITION_FAILED              = 412;
		const PAYLOAD_TOO_LARGE                = 413;
		const URI_TOO_LONG                     = 414;
		const UNSUPPORTED_MEDIA_TYPE           = 415;
		const RANGE_NOT_SATISFIABLE            = 416;
		const EXPECTATION_FAILED               = 417;
		const IM_A_TEAPOT                      = 418;
		const MISDIRECTED_REQUEST              = 421;
		const UNPROCESSABLE_ENTITY             = 422;
		const LOCKED                           = 423;
		const FAILED_DEPENDENCY                = 424;
		const UPGRADE_REQUIRED                 = 426;
		const PRECONDITION_REQUIRED            = 428;
		const TOO_MANY_REQUESTS                = 429;
		const REQUEST_HEADER_FIELDS_TOO_LARGE  = 431;
		const UNAVAILABLE_FOR_LEGAL_REASONS    = 451;
		const INTERNAL_SERVER_ERROR            = 500;
		const NOT_IMPLEMENTED                  = 501;
		const BAD_GATEWAY                      = 502;
		const SERVICE_UNAVAILABLE              = 503;
		const GATEWAY_TIMEOUT                  = 504;
		const HTTP_VERSION_NOT_SUPPORTED       = 505;
		const VARIANT_ALSO_NEGOTIATES          = 506;
		const INSUFFICIENT_STORAGE             = 507;
		const LOOP_DETECTED                    = 508;
		const NOT_EXTENDED                     = 510;
		const NETWORK_AUTHENTICATEION_REQUIRED = 511;


		/**
		 * Internal static collection of friendly descriptions for status codes.
		 *
		 * @var array
		 */
		protected static $_descriptions = [
			self::CONTINU                          => 'Continue',
			self::PROTO_SWITCH                     => 'Switching Protocols',
			self::PROCESSING                       => 'Processing',
			self::OK                               => 'OK',
			self::CREATED                          => 'Created',
			self::ACCEPTED                         => 'Accepted',
			self::NON_AUTH_INFO                    => 'Non-Authoritative Information',
			self::NO_CONTENT                       => 'No Content',
			self::RESET_CONTENT                    => 'Reset Content',
			self::PARTIAL_CONTENT                  => 'Partial Content',
			self::MULTI_STATUS                     => 'Multi-Status',
			self::ALREADY_REPORTED                 => 'Already Reported',
			self::IM_USED                          => 'IM Used',
			self::MULTIPLE_CHOICES                 => 'Multiple Choices',
			self::MOVED_PERMANENTLY                => 'Moved Permanently',
			self::FOUND                            => 'Found',
			self::SEE_OTHER                        => 'See Other',
			self::NOT_MODIFIED                     => 'Not Modified',
			self::USE_PROXY                        => 'Use Proxy',
			self::SWITCH_PROXY                     => 'Switch Proxy',
			self::TEMPORARY_REDIRECT               => 'Temporary Redirect',
			self::PERMANENT_REDIRECT               => 'Permanent Redirect',
			self::BAD_REQUEST                      => 'Bad Request',
			self::UNAUTHORIZED                     => 'Unauthorized',
			self::PAYMENT_REQUIRED                 => 'Payment Required',
			self::FORBIDDEN                        => 'Forbidden',
			self::NOT_FOUND                        => 'Not Found',
			self::METHOD_NOT_ALLOWED               => 'Method Not Allowed',
			self::NOT_ACCEPTABLE                   => 'Not Acceptable',
			self::PROXY_AUTH_REQUIRED              => 'Proxy Authentication Required',
			self::REQUEST_TIMEOUT                  => 'Request Timeout',
			self::CONFLICT                         => 'Conflict',
			self::GONE                             => 'Gone',
			self::LENGTH_REQUIRED                  => 'Length Required',
			self::PRECONDITION_FAILED              => 'Precondition Failed',
			self::PAYLOAD_TOO_LARGE                => 'Payload Too Large',
			self::URI_TOO_LONG                     => 'URI Too Long',
			self::UNSUPPORTED_MEDIA_TYPE           => 'Unsupported Media Type',
			self::RANGE_NOT_SATISFIABLE            => 'Range Not Satisfiable',
			self::EXPECTATION_FAILED               => 'Expectation Failed',
			self::IM_A_TEAPOT                      => "I'm a teapot",
			self::MISDIRECTED_REQUEST              => 'Misdirected Requested',
			self::UNPROCESSABLE_ENTITY             => 'Unprocessable Entity',
			self::LOCKED                           => 'Locked',
			self::FAILED_DEPENDENCY                => 'Failed Dependency',
			self::UPGRADE_REQUIRED                 => 'Upgrade Required',
			self::PRECONDITION_REQUIRED            => 'Precondition Required',
			self::TOO_MANY_REQUESTS                => 'Too Many Requests',
			self::REQUEST_HEADER_FIELDS_TOO_LARGE  => 'Request Header Fields Too Large',
			self::UNAVAILABLE_FOR_LEGAL_REASONS    => 'Unavailable For Legal Reasons',
			self::INTERNAL_SERVER_ERROR            => 'Internal Server Error',
			self::NOT_IMPLEMENTED                  => 'Not Implemented',
			self::BAD_GATEWAY                      => 'Bad Gateway',
			self::SERVICE_UNAVAILABLE              => 'Service Unavailable',
			self::GATEWAY_TIMEOUT                  => 'Gateway Timeout',
			self::HTTP_VERSION_NOT_SUPPORTED       => 'HTTP Version Not Supported',
			self::VARIANT_ALSO_NEGOTIATES          => 'Variant Also Negotiates',
			self::INSUFFICIENT_STORAGE             => 'Insufficient Storage',
			self::LOOP_DETECTED                    => 'Loop Detected',
			self::NOT_EXTENDED                     => 'Not Extended',
			self::NETWORK_AUTHENTICATEION_REQUIRED => 'Network Authentication Required'
		];


		/**
		 * Retrieves the friendly description text for the currently set status
		 * code.
		 *
		 * @return string
		 */
		public function getDescription() : string {
			if ($this->value !== null && array_key_exists($this->value, static::$_descriptions) !== false) {
				return static::$_descriptions[$this->value];
			}

			return 'Unknown Status Code';
		}
	}
