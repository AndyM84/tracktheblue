<?php

	namespace Stoic\Web\Resources;

	/**
	 * Collection of index constants for the PHP $_SERVER global variable.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class ServerIndices {
		const AUTH_TYPE            = 'AUTH_TYPE';
		const DOCUMENT_ROOT        = 'DOCUMENT_ROOT';
		const GATEWAY_INTERFACE    = 'GATEWAY_INTERFACE';
		const HTTP_ACCEPT          = 'HTTP_ACCEPT';
		const HTTP_ACCEPT_CHARSET  = 'HTTP_ACCEPT_CHARSET';
		const HTTP_ACCEPT_ENCODING = 'HTTP_ACCEPT_ENCODING';
		const HTTP_ACCEPT_LANGUAGE = 'HTTP_ACCEPT_LANGUAGE';
		const HTTP_CONNECTION      = 'HTTP_CONNECTION';
		const HTTP_HOST            = 'HTTP_HOST';
		const HTTP_REFERER         = 'HTTP_REFERER';
		const HTTP_USER_AGENT      = 'HTTP_USER_AGENT';
		const HTTPS                = 'HTTPS';
		const ORIG_PATH_INFO       = 'ORIG_PATH_INFO';
		const PATH_INFO            = 'PATH_INFO';
		const PATH_TRANSLATED      = 'PATH_TRANSLATED';
		const PHP_AUTH_DIGEST      = 'PHP_AUTH_DIGEST';
		const PHP_AUTH_USER        = 'PHP_AUTH_USER';
		const PHP_AUTH_PW          = 'PHP_AUTH_PW';
		const PHP_SELF             = 'PHP_SELF';
		const QUERY_STRING         = 'QUERY_STRING';
		const REDIRECT_REMOTE_USER = 'REDIRECT_REMOTE_USER';
		const REMOTE_ADDR          = 'REMOTE_ADDR';
		const REMOTE_HOST          = 'REMOTE_HOST';
		const REMOTE_PORT          = 'REMOTE_PORT';
		const REQUEST_METHOD       = 'REQUEST_METHOD';
		const REQUEST_TIME         = 'REQUEST_TIME';
		const REQUEST_TIME_FLOAT   = 'REQUEST_TIME_FLOAT';
		const REQUEST_URI          = 'REQUEST_URI';
		const SERVER_ADDR          = 'SERVER_ADDR';
		const SERVER_ADMIN         = 'SERVER_ADMIN';
		const SERVER_NAME          = 'SERVER_NAME';
		const SERVER_PORT          = 'SERVER_PORT';
		const SERVER_PROTOCOL      = 'SERVER_PROTOCOL';
		const SERVER_SIGNATURE     = 'SERVER_SIGNATURE';
		const SERVER_SOFTWARE      = 'SERVER_SOFTWARE';
		const SCRIPT_FILENAME      = 'SCRIPT_FILENAME';
		const SCRIPT_NAME          = 'SCRIPT_NAME';
	}

	/**
	 * Collection of index constants used for initializing an
	 * ApiAuthorizationDispatch object.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class AuthorizationDispatchStrings {
		const INDEX_CONSUMABLE = 'consumable';
		const INDEX_INPUT      = 'input';
		const INDEX_ROLES      = 'roles';
	}

	/**
	 * Collection of various strings used for Stoic operation.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class StoicStrings {
		const SETTINGS_FILE_PATH = '~/siteSettings.json';
	}

	/**
	 * Collection of various settings strings for Stoic.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class SettingsStrings {
		const ASSETS_PATH       = 'assetsPath';
		const CLASSES_EXTENSION = 'classesExt';
		const CLASSES_PATH      = 'classesPath';
		const DB_DSN            = 'dbDsn';
		const DB_PASS           = 'dbPass';
		const DB_USER           = 'dbUser';
		const INCLUDE_PATH      = 'includePath';
		const MIGRATE_CFG_PATH  = 'migrateCfg';
		const MIGRATE_DB_PATH   = 'migrateDb';
		const REPOS_EXTENSION   = 'reposExt';
		const REPOS_PATH        = 'reposPath';
		const UTILITIES_EXT     = 'utilitiesExt';
		const UTILITIES_PATH    = 'utilitiesPath';
	}
