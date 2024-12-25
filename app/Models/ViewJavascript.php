<?php
declare(strict_types=1);

final class FreshRSS_ViewJavascript extends FreshRSS_View {

	/** @var list<FreshRSS_Category> */
	public array $categories;
	/** @var list<FreshRSS_Feed> */
	public array $feeds;
	/** @var list<FreshRSS_Tag> */
	public array $tags;

	public string $nonce;
	public string $salt1;
}
