<?php
declare(strict_types=1);

/**
 * @property bool $allow_anonymous
 * @property bool $allow_anonymous_refresh
 * @property-read bool $allow_referrer
 * @property bool $allow_robots
 * @property bool $api_enabled
 * @property string $archiving
 * @property 'form'|'http_auth'|'none' $auth_type
 * @property-read string $auto_update_url
 * @property-read array<int,mixed> $curl_options
 * @property string $default_user
 * @property string $email_validation_token
 * @property bool $force_email_validation
 * @property-read bool $http_auth_auto_register
 * @property-read string $http_auth_auto_register_email_field
 * @property string $language
 * @property array<string,int> $limits
 * @property-read string $logo_html
 * @property-read string $meta_description
 * @property-read int $nb_parallel_refresh
 * @property-read bool $pubsubhubbub_enabled
 * @property-read string $salt
 * @property-read bool $simplepie_syslog_enabled
 * @property bool $unsafe_autologin_enabled
 * @property array<string> $trusted_sources
 * @property array<string,array<string,mixed>> $extensions
 */
final class FreshRSS_SystemConfiguration extends Minz_Configuration {

	/** @throws Minz_FileNotExistException */
	public static function init(string $config_filename, ?string $default_filename = null): FreshRSS_SystemConfiguration {
		parent::register('system', $config_filename, $default_filename);
		try {
			return parent::get('system');
		} catch (Minz_ConfigurationNamespaceException $ex) {
			FreshRSS::killApp($ex->getMessage());
		}
	}

	/**
	 * @param non-empty-string $key
	 * @return array<int|string,mixed>|null
	 */
	public function attributeArray(string $key): ?array {
		$a = parent::param($key, null);
		return is_array($a) ? $a : null;
	}

	/** @param non-empty-string $key */
	public function attributeBool(string $key): ?bool {
		$a = parent::param($key, null);
		return is_bool($a) ? $a : null;
	}

	/** @param non-empty-string $key */
	public function attributeInt(string $key): ?int {
		$a = parent::param($key, null);
		return is_numeric($a) ? (int)$a : null;
	}

	/** @param non-empty-string $key */
	public function attributeString(string $key): ?string {
		$a = parent::param($key, null);
		return is_string($a) ? $a : null;
	}

	/**
	 * @param non-empty-string $key
	 * @param array<string,mixed>|mixed|null $value Value, not HTML-encoded
	 */
	public function _attribute(string $key, $value = null): void {
		parent::_param($key, $value);
	}

}
