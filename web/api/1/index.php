<?php

	error_reporting(E_ALL);
	ini_set('display_errors', 'On');

	define('STOIC_CORE_PATH', '../../../');
	require(STOIC_CORE_PATH . 'inc/core.php');

	use Stoic\Web\Api\Stoic;

	global $Api, $Db, $Log, $Settings;

	/**
	 * @var \Stoic\Web\Api\Stoic $Api
	 * @var \PDO $Db
	 * @var \Stoic\Log\Logger $Log
	 * @var \AndyM84\Config\ConfigContainer $Settings
	 */

	$authorizer = new Zibings\ApiAuthorizer();
	
	$Api = Stoic::getInstance(STOIC_CORE_PATH);
	$Api->linkAuthorizationNode($authorizer);

	$endpoints = [];
	$loadedfiles = $Api->loadFilesByExtension('~/api/1', '.api.php');

	foreach (array_values($loadedfiles) as $file) {
		$f = str_replace('.api.php', '', basename($file));

		$cls = "\\Api1\\{$f}";
		$endpoints[] = new $cls($Api, $Db, $Log);
	}

	$Api->handle();
