<?php
declare(strict_types=1);

const FAVICONS_DIR = DATA_PATH . '/favicons/';
const DEFAULT_FAVICON = PUBLIC_PATH . '/themes/icons/default_favicon.ico';

function isImgMime(string $content): bool {
	//Based on https://github.com/ArthurHoaro/favicon/blob/3a4f93da9bb24915b21771eb7873a21bde26f5d1/src/Favicon/Favicon.php#L311-L319
	if ($content == '') {
		return false;
	}
	if (!extension_loaded('fileinfo')) {
		return true;
	}
	$fInfo = finfo_open(FILEINFO_MIME_TYPE);
	if ($fInfo === false) {
		return true;
	}
	$content = finfo_buffer($fInfo, $content);
	$isImage = str_contains($content ?: '', 'image');
	finfo_close($fInfo);
	return $isImage;
}

/** @param array<int,int|bool|string> $curlOptions */
function downloadHttp(string &$url, array $curlOptions = []): string {
	if (($retryAfter = FreshRSS_http_Util::getRetryAfter($url)) > 0) {
		Minz_Log::warning('For that domain, will first retry favicon after ' . date('c', $retryAfter) . '. ' . \SimplePie\Misc::url_remove_credentials($url));
		return '';
	}

	syslog(LOG_INFO, 'FreshRSS Favicon GET ' . $url);
	$url2 = checkUrl($url);
	if ($url2 == false) {
		return '';
	}
	$url = $url2;

	$ch = curl_init($url);
	if ($ch === false) {
		return '';
	}
	curl_setopt_array($ch, [
			CURLOPT_HEADER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_USERAGENT => FRESHRSS_USERAGENT,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => '',	//Enable all encodings
			//CURLOPT_VERBOSE => 1,	// To debug sent HTTP headers
		]);

	FreshRSS_Context::initSystem();
	if (FreshRSS_Context::hasSystemConf()) {
		curl_setopt_array($ch, FreshRSS_Context::systemConf()->curl_options);
	}

	curl_setopt_array($ch, $curlOptions);

	$response = curl_exec($ch);
	$c_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$c_effective_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);

	$parser = new \SimplePie\HTTP\Parser(is_string($response) ? $response : '');
	if ($parser->parse()) {
		$headers = $parser->headers;
		$body = $parser->body;
	} else {
		$headers = [];
		$body = false;
	}

	if (in_array($c_status, [429, 503], true)) {
		$retryAfter = FreshRSS_http_Util::setRetryAfter($url, $headers['retry-after'] ?? '');
		if ($c_status === 429) {
			$errorMessage = 'HTTP 429 Too Many Requests! Searching favicon [' . \SimplePie\Misc::url_remove_credentials($url) . ']';
		} elseif ($c_status === 503) {
			$errorMessage = 'HTTP 503 Service Unavailable! Searching favicon [' . \SimplePie\Misc::url_remove_credentials($url) . ']';
		}
		if ($retryAfter > 0) {
			$errorMessage .= ' We may retry after ' . date('c', $retryAfter);
		}
	}

	$url2 = checkUrl($c_effective_url);
	if ($url2 != false) {
		$url = $url2;	//Possible redirect
	}

	return $c_status === 200 && is_string($body) ? $body : '';
}

function searchFavicon(string &$url): string {
	$dom = new DOMDocument();
	$html = downloadHttp($url);

	if ($html == '' || !@$dom->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
		return '';
	}

	$xpath = new DOMXPath($dom);
	$links = $xpath->query('//link[@href][translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="shortcut icon"'
		. ' or translate(@rel, "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz")="icon"]');
	if (!($links instanceof DOMNodeList)) {
		return '';
	}

	// Use the base element for relative paths, if there is one
	$baseElements = $xpath->query('//base[@href]');
	$baseElement = ($baseElements !== false && $baseElements->length > 0) ? $baseElements->item(0) : null;
	$baseUrl = ($baseElement instanceof DOMElement) ? $baseElement->getAttribute('href') : $url;

	foreach ($links as $link) {
		if (!$link instanceof DOMElement) {
			continue;
		}
		$href = trim($link->getAttribute('href'));
		$urlParts = parse_url($url);

		// Handle protocol-relative URLs by adding the current URL's scheme
		if (substr($href, 0, 2) === '//') {
			$href = ($urlParts['scheme'] ?? 'https') . ':' . $href;
		}

		$href = \SimplePie\IRI::absolutize($baseUrl, $href);
		if ($href == false) {
			return '';
		}

		$iri = $href->get_iri();
		if ($iri == false) {
			return '';
		}
		$favicon = downloadHttp($iri, [CURLOPT_REFERER => $url]);
		if (isImgMime($favicon)) {
			return $favicon;
		}
	}
	return '';
}

function download_favicon(string $url, string $dest): bool {
	$url = trim($url);
	$favicon = searchFavicon($url);
	if ($favicon == '') {
		$rootUrl = preg_replace('%^(https?://[^/]+).*$%i', '$1/', $url) ?? $url;
		if ($rootUrl != $url) {
			$url = $rootUrl;
			$favicon = searchFavicon($url);
		}
		if ($favicon == '') {
			$link = $rootUrl . 'favicon.ico';
			$favicon = downloadHttp($link, [CURLOPT_REFERER => $url]);
			if (!isImgMime($favicon)) {
				$favicon = '';
			}
		}
	}
	return ($favicon != '' && file_put_contents($dest, $favicon) > 0) ||
		@copy(DEFAULT_FAVICON, $dest);
}

function contentType(string $ico): string {
	$ico_content_type = 'image/x-icon';
	if (function_exists('mime_content_type')) {
		$ico_content_type = mime_content_type($ico) ?: $ico_content_type;
	}
	switch ($ico_content_type) {
		case 'image/svg':
			$ico_content_type = 'image/svg+xml';
			break;
	}
	return $ico_content_type;
}
