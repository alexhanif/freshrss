<?php
enum FreshRSS_HttpResponse: int {
	case HTTP_100_CONTINUE = 100;
	case HTTP_101_SWITCHING_PROTOCOLS = 101;
	case HTTP_102_PROCESSING = 102;
	case HTTP_103_EARLY_HINTS = 103;
	case HTTP_200_OK = 200;
	case HTTP_201_CREATED = 201;
	case HTTP_202_ACCEPTED = 202;
	case HTTP_203_NON_AUTHORITATIVE_INFORMATION = 203;
	case HTTP_204_NO_CONTENT = 204;
	case HTTP_205_RESET_CONTENT = 205;
	case HTTP_206_PARTIAL_CONTENT = 206;
	case HTTP_207_MULTI_STATUS = 207;
	case HTTP_208_ALREADY_REPORTED = 208;
	case HTTP_226_IM_USED = 226;
	case HTTP_300_MULTIPLE_CHOICES = 300;
	case HTTP_301_MOVED_PERMANENTLY = 301;
	case HTTP_302_FOUND = 302;
	case HTTP_303_SEE_OTHER = 303;
	case HTTP_304_NOT_MODIFIED = 304;
	case HTTP_305_USE_PROXY = 305;
	case HTTP_306_RESERVED = 306;
	case HTTP_307_TEMPORARY_REDIRECT = 307;
	case HTTP_308_PERMANENTLY_REDIRECT = 308;
	case HTTP_400_BAD_REQUEST = 400;
	case HTTP_401_UNAUTHORIZED = 401;
	case HTTP_402_PAYMENT_REQUIRED = 402;
	case HTTP_403_FORBIDDEN = 403;
	case HTTP_404_NOT_FOUND = 404;
	case HTTP_405_METHOD_NOT_ALLOWED = 405;
	case HTTP_406_NOT_ACCEPTABLE = 406;
	case HTTP_407_PROXY_AUTHENTICATION_REQUIRED = 407;
	case HTTP_408_REQUEST_TIMEOUT = 408;
	case HTTP_409_CONFLICT = 409;
	case HTTP_410_GONE = 410;
	case HTTP_411_LENGTH_REQUIRED = 411;
	case HTTP_412_PRECONDITION_FAILED = 412;
	case HTTP_413_REQUEST_ENTITY_TOO_LARGE = 413;
	case HTTP_414_REQUEST_URI_TOO_LONG = 414;
	case HTTP_415_UNSUPPORTED_MEDIA_TYPE = 415;
	case HTTP_416_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	case HTTP_417_EXPECTATION_FAILED = 417;
	case HTTP_418_I_AM_A_TEAPOT = 418;
	case HTTP_421_MISDIRECTED_REQUEST = 421;
	case HTTP_422_UNPROCESSABLE_ENTITY = 422;
	case HTTP_423_LOCKED = 423;
	case HTTP_424_FAILED_DEPENDENCY = 424;
	case HTTP_425_TOO_EARLY = 425;
	case HTTP_426_UPGRADE_REQUIRED = 426;
	case HTTP_428_PRECONDITION_REQUIRED = 428;
	case HTTP_429_TOO_MANY_REQUESTS = 429;
	case HTTP_431_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	case HTTP_451_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	case HTTP_500_INTERNAL_SERVER_ERROR = 500;
	case HTTP_501_NOT_IMPLEMENTED = 501;
	case HTTP_502_BAD_GATEWAY = 502;
	case HTTP_503_SERVICE_UNAVAILABLE = 503;
	case HTTP_504_GATEWAY_TIMEOUT = 504;
	case HTTP_505_VERSION_NOT_SUPPORTED = 505;
	case HTTP_506_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
	case HTTP_507_INSUFFICIENT_STORAGE = 507;
	case HTTP_508_LOOP_DETECTED = 508;
	case HTTP_510_NOT_EXTENDED = 510;
	case HTTP_511_NETWORK_AUTHENTICATION_REQUIRED = 511;

	public static function fromName(string $name): int {
		foreach (self::cases() as $status) {
			if ($name === $status->name) {
				return $status->value;
			}
		}
		throw new \DomainException("$name is not a valid backing value for enum " . self::class);
	}

	public static function fromValue(int $value): int {
		foreach (self::cases() as $status) {
			if ($value === $status->value) {
				return $status->value;
			}
		}
		throw new \DomainException("$value is not a valid backing value for enum " . self::class);
	}

	public static function description(FreshRSS_HttpResponse $enum): string {
		$returnedValue = ucwords(strtolower(str_replace("_", ' ', $enum->name)));
		return (string)preg_replace('/^Http /', 'HTTP/1.1 ', $returnedValue);
	}
}
