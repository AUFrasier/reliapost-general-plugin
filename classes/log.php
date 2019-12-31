<?php

namespace reliapost_registration;

class Log
{
	public static function addEntry($message)
	{
		$file = static::getLogFile();
		if (is_writable($file)) {
			$date = date("m-d-Y H:i:s e", time());
            $bt =  debug_backtrace();
            $filepath = basename($bt[0]['file']);

            $tag = $filepath . ' line  '. $bt[0]['line'];

			file_put_contents($file, $date . " ($tag) - " . $message . "\n", FILE_APPEND);
		}
	}
	
	public static function getLogMessages()
	{
		$file = static::getLogFile();
		if (static::doesLogFileExist() === false) {
			static::createLogFile();
		}
		
		if (static::doesLogFileExist() === true) {
			
			$errorLogContents = file_get_contents($file);
			return $errorLogContents === '' ? __('No Errors') : $errorLogContents;
			
		}
		
		return __('The error file at ' . $file . ' is inaccessible.');
	}

    public static function clearLog()
	{
		$logFilename = static::getLogFile();
		file_put_contents($logFilename, "");
	}
	
	private static function getLogFile()
	{
		return static::getLogsDirectory() . 'error_log';
	}
	
	public static function getLogsDirectory()
	{
		return __DIR__ . '/../logs/';
	}
	
	private static function doesLogFileExist()
	{
		return is_writable(static::getLogFile());
	}

	public static function setupLog() {
        if (static::doesLogFileExist() === false) {
            static::createLogFile();
        }
    }
	
	private static function createLogFile()
	{
	    $logDirectory = static::getLogsDirectory();
	    if (!file_exists($logDirectory)) {
	        mkdir($logDirectory, 0755, true);
        }
		$file = static::getLogFile();
		@chmod(static::getLogsDirectory(), 0755);
		@touch($file);
		@chmod($file, 0644);
	}
}
