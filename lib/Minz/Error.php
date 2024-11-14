<?php
declare(strict_types=1);

/**
 * MINZ - Copyright 2011 Marien Fressinaud
 * Sous licence AGPL3 <http://www.gnu.org/licenses/>
*/

/**
 * The Minz_Error class logs and raises framework errors
 */
class Minz_Error {
	public function __construct() {}

	/**
	 * Launches an error
	 * @param FreshRSS_HttpResponseCode $code error type
	 * @param string|array<'error'|'warning'|'notice',array<string>> $logs error logs broken down by form
	 *      > $logs['error']
	 *      > $logs['warning']
	 *      > $logs['notice']
	 * @param bool $redirect indicates whether to force redirection (logs will not be transmitted)
	 * @throws Minz_ConfigurationException
	 */
	public static function error(FreshRSS_HttpResponseCode $code, string|array $logs = [], bool $redirect = true): void {
		$logs = self::processLogs($logs);
		$error_filename = APP_PATH . '/Controllers/errorController.php';

		if (file_exists($error_filename)) {
			Minz_Session::_params([
				'error_code' => $code->value,
				'error_logs' => $logs,
			]);

			Minz_Request::forward(['c' => 'error'], $redirect);
		} else {
			echo '<h1>An error occurred</h1>' . "\n";

			if (!empty($logs)) {
				echo '<ul>' . "\n";
				foreach ($logs as $log) {
					echo '<li>' . $log . '</li>' . "\n";
				}
				echo '</ul>' . "\n";
			}

			exit();
		}
	}

	/**
	 * Returns filtered logs
	 * @param string|array<'error'|'warning'|'notice',array<string>> $logs logs sorted by category (error, warning, notice)
	 * @return array<string> list of matching logs, without the category, according to environment preferences (production / development)
	 * @throws Minz_ConfigurationNamespaceException
	 */
	private static function processLogs(string|array $logs): array {
		if (is_string($logs)) {
			return [$logs];
		}

		$error = [];
		$warning = [];
		$notice = [];

		if (isset($logs['error']) && is_array($logs['error'])) {
			$error = $logs['error'];
		}
		if (isset($logs['warning']) && is_array($logs['warning'])) {
			$warning = $logs['warning'];
		}
		if (isset($logs['notice']) && is_array($logs['notice'])) {
			$notice = $logs['notice'];
		}

		switch (Minz_Configuration::get('system')->environment) {
			case 'development':
				return array_merge($error, $warning, $notice);
			case 'production':
			default:
				return $error;
		}
	}
}
