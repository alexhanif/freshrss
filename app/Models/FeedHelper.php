<?php
declare(strict_types=1);

class FeedHelper {
	public static function normalizeUrl(string $url): string {
		$url = trim($url);
		$parts = parse_url($url);

		if ($parts === false || !isset($parts['host'])) {
			return rtrim(strtolower($url), '/');
		}

		$scheme = strtolower($parts['scheme'] ?? 'http');
		$host = strtolower($parts['host']);
		$path = rtrim($parts['path'] ?? '', '/');
		$query = isset($parts['query']) ? '?' . $parts['query'] : '';

		return "$scheme://$host$path$query";
	}
}
