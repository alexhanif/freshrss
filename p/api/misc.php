<?php
declare(strict_types=1);

require(__DIR__ . '/../../constants.php');
require(LIB_PATH . '/lib_rss.php');	//Includes class autoloader

function serviceUnavailable(): void {
	header('HTTP/1.1 503 Service Unavailable');
	header('Content-Type: text/plain; charset=UTF-8');
	die('Service Unavailable!');
}

$extensionName = is_string($_GET['ext'] ?? null) ? $_GET['ext'] : null;

Minz_Session::init('FreshRSS', volatile: true);

FreshRSS_Context::initSystem();
if (!FreshRSS_Context::hasSystemConf() ||
	!FreshRSS_Context::systemConf()->api_enabled ||
	empty(FreshRSS_Context::systemConf()->extensions_enabled[$extensionName])) {
	serviceUnavailable();
}

// Only enable the extension that is being called
FreshRSS_Context::systemConf()->extensions_enabled = [ $extensionName => true ];
Minz_ExtensionManager::init();

Minz_Translate::init();

if (!Minz_ExtensionManager::callHookUnique('api_misc')) {
	serviceUnavailable();
}
