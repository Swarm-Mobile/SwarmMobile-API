<?php

/**
 * Override default cake error handler
 */
App::uses('CakeLog', 'Log');

class AppError
{

	public static function handleError ($code, $description, $file = null, $line = null, $context = null)
	{
		if (extension_loaded('newrelic')) {
			newrelic_notice_error($code);
		}

		return ErrorHandler::handleError(
			$code, $description, $file, $line, $context
		);
	}

	public static function handleException (Exception $error)
	{
		if (extension_loaded('newrelic')) {
			newrelic_notice_error($error->getMessage(), $error);
		}

		ErrorHandler::handleException(
			$error
		);
	}

}
