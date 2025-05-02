#!/usr/bin/env php
<?php
declare(strict_types=1);
require(__DIR__ . '/_cli.php');

performRequirementCheck(FreshRSS_Context::systemConf()->db['type'] ?? '');

$cliOptions = new class extends CliOptionsParser {
	public string $user = '';
	public bool $allUsers;

	public function __construct() {
		$this->addOption('user', new CliOption('user'));
		$this->addOption('allUsers', (new CliOption('all-users'))->withValueNone());
		parent::__construct();
	}
};

if (!empty($cliOptions->errors)) {
	fail('FreshRSS error: ' . array_shift($cliOptions->errors) . "\n" . $cliOptions->usage);
}

$username = $cliOptions->user;

if ($username === '' && !$cliOptions->allUsers) {
    fail($cliOptions->usage);
}

function clearCacheForUser(string $username) {
    echo "Clearing cache for user $username.\n";
    
    $feedDAO = FreshRSS_Factory::createFeedDao($username);
    $ids = $feedDAO->listFeedsIds();

	if (count($ids) === 0) {
		echo "No feeds found.\n";
		return;
	}

    $maxId = max($ids);
    
    foreach ($ids as $feedId) {
        echo "\rfor feed ID: $feedId/$maxId";
            
        $feed = $feedDAO->searchById($feedId);
        if ($feed === null) {
            echo "\nFeed with ID $feedId not found. Continuing.\n";
            continue;
        }
            
        $feed->clearCache();
    }
        
    invalidateHttpCache($username);
    echo "\nDone.\n";
}

if ($cliOptions->allUsers) {
    $users = listUsers();
    sort($users);
    if (FreshRSS_Context::systemConf()->default_user !== ''
    	&& in_array(FreshRSS_Context::systemConf()->default_user, $users, true)) {
    	array_unshift($users, FreshRSS_Context::systemConf()->default_user);
    	$users = array_unique($users);
    }
    
    foreach ($users as $username) {
        clearCacheForUser($username);
    }

    die();
}

if (!FreshRSS_user_Controller::checkUsername($username)) {
	fail('FreshRSS error: invalid username: ' . $username . "\n");
}
if (!FreshRSS_user_Controller::userExists($username)) {
	fail('FreshRSS error: user not found: ' . $username . "\n");
}

clearCacheForUser($username);
