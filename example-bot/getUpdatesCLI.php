#!/usr/bin/env php
<?php
/*
*	
*	Fileinfo: sendMessage.php
*	Version: 0.1 
*	Creator: Constantin Lotz (info@constey.de)
*			 www.constey.de
*	Comment: This File fetches the Chatmessages and Responses (Ack/DT) from Telegram
			 and puts everything in a mysql database so we can handle it seperately.
*			 It is based on the getUpdatesCLI.php from the PHP-TelegramBot.
*
*	Changelog:			 
*			   0.1 Initial
* 
*/

/**
 * README
 * This configuration file is intended to run the bot with the getUpdates method.
 * Uncommented parameters must be filled
 *
 * Bash script:
 * $ while true; do ./getUpdatesCLI.php; done
 */

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

// Add you bot's API key and name
$bot_api_key  = 'YOUR_API_KEY_HERE';
$bot_username = 'YOUR_BOT_USERNAME_WITHOUT@';

// Define all IDs of admin users in this array (leave as empty array if not used)
$admin_users = [
    652200566,
];

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
  __DIR__ . '/Commands/',
];

// Enter your MySQL database credentials
$mysql_credentials = [
    'host'     => 'localhost',
    'user'     => 'tg',
    'password' => 'tg',
    'database' => 'tg',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Add commands paths containing your custom commands
    $telegram->addCommandsPaths($commands_paths);

    // Enable admin users
    $telegram->enableAdmins($admin_users);

    // Enable MySQL
    $telegram->enableMySql($mysql_credentials);

    // Logging (Error, Debug and Raw Updates)
    //Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . "/{$bot_username}_error.log");
    //Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . "/{$bot_username}_debug.log");
    //Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . "/{$bot_username}_update.log");

    // If you are using a custom Monolog instance for logging, use this instead of the above
    //Longman\TelegramBot\TelegramLog::initialize($your_external_monolog_instance);

    // Set custom Upload and Download paths
    //$telegram->setDownloadPath(__DIR__ . '/Download');
    //$telegram->setUploadPath(__DIR__ . '/Upload');

    // Here you can set some command specific parameters
    // e.g. Google geocode/timezone api key for /date command
    //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

    // Botan.io integration
    //$telegram->enableBotan('your_botan_token');

    // Requests Limiter (tries to prevent reaching Telegram API limits)
    $telegram->enableLimiter();
	
    // Handle telegram getUpdates request
    $server_response = $telegram->handleGetUpdates();

    if ($server_response->isOk()) {
        $update_count = count($server_response->getResult());
        echo date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates' . PHP_EOL ;
    } else {
        echo date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL ;
        echo $server_response->printError();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e->getMessage();
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Catch log initialisation errors
    echo $e->getMessage();
}

// Handle Callbacks to CMK
require 'callbackCMK.php';