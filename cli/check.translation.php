#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/_cli.php';
require_once __DIR__ . '/i18n/I18nCompletionValidator.php';
require_once __DIR__ . '/i18n/I18nData.php';
require_once __DIR__ . '/i18n/I18nFile.php';
require_once __DIR__ . '/i18n/I18nUsageValidator.php';
require_once __DIR__ . '/../constants.php';

$cliOptions = new class extends CliOptionsParser {
	/** @var array<int,string> $language */
	public array $language;
	public bool $displayResult;
	public bool $help;
	public bool $displayReport;
	public bool $generateReadme;

	public function __construct() {
		$this->addOption('language', (new CliOption('language', 'l'))->typeOfArrayOfString());
		$this->addOption('displayResult', (new CliOption('display-result', 'd'))->withValueNone());
		$this->addOption('help', (new CliOption('help', 'h'))->withValueNone());
		$this->addOption('displayReport', (new CliOption('display-report', 'r'))->withValueNone());
		$this->addOption('generateReadme', (new CliOption('generate-readme', 'g'))->withValueNone());
		parent::__construct();
	}
};

if (!empty($cliOptions->errors)) {
	fail('FreshRSS error: ' . array_shift($cliOptions->errors) . "\n" . $cliOptions->usage);
}
if ($cliOptions->help) {
	checkHelp();
}

$i18nFile = new I18nFile();
$i18nData = new I18nData($i18nFile->load());

if (isset($cliOptions->language)) {
	$languages = $cliOptions->language;
} else {
	$languages = $i18nData->getAvailableLanguages();
}

$isValidated = true;
$result = [];
$report = [];
$percentage = [];

foreach ($languages as $language) {
	if ($language === $i18nData::REFERENCE_LANGUAGE) {
		$i18nValidator = new I18nUsageValidator($i18nData->getReferenceLanguage(), findUsedTranslations());
	} else {
		$i18nValidator = new I18nCompletionValidator($i18nData->getReferenceLanguage(), $i18nData->getLanguage($language));
	}
	$isValidated = $i18nValidator->validate() && $isValidated;

	$report[$language] = sprintf('%-5s - %s', $language, $i18nValidator->displayReport());
	$percentage[$language] = $i18nValidator->displayReport(percentage_only: true);
	$result[$language] = $i18nValidator->displayResult();
}

if ($cliOptions->displayResult) {
	foreach ($result as $lang => $value) {
		echo 'Language: ', $lang, PHP_EOL;
		print_r($value);
		echo PHP_EOL;
	}
}

if ($cliOptions->displayReport) {
	foreach ($report as $value) {
		echo $value;
	}
}

function writeToReadme(string $readmePath, string $markdownImgStr): void {
	$readme = file_get_contents($readmePath);
	if ($readme === false) {
		echo 'Error: ' . $readmePath . ' not found', PHP_EOL;
		exit(1);
	}
	if (file_put_contents($readmePath, preg_replace('/<translations>(.*?)<\/translations>/s', <<<EOF
	<translations>

	$markdownImgStr

	</translations>
	EOF, $readme)) === false) {
		echo 'Error: Fail while writing to ' . $readmePath, PHP_EOL;
		exit(1);
	}
	echo 'Successfully written translation status into ' . $readmePath, PHP_EOL;
}

function embedSvg(string $contents): string {
	return preg_replace(
		'/<svg\s+(?:(?:[^>]*?)(xmlns=["\'][^"\']+["\']))?(?:(?:[^>]*?)(viewBox=["\'][^"\']+["\']))?(?:[^>]*?)>/i',
		'<svg \1 \2 width="16" height="16" x="5" y="2">',
		$contents
	) ?? '';
}

if ($cliOptions->generateReadme) {
	$markdownImgStr = '';
	foreach ($percentage as $lang => $value) {
		$percentageInt = intval(rtrim($value, '%'));
		$color = 'green';
		if ($percentageInt < 90) {
			$color = 'gold';
		}
		if ($percentageInt < 70) {
			$color = 'darkred';
		}
		$flag = glob(__DIR__ . '/flags/' . $lang . '.*');
		if ($flag === false || !isset($flag[0])) {
			echo 'Error: Unable to find flag for ' . $lang, PHP_EOL;
			exit(1);
		}
		$supported_formats = ['txt', 'svg'];
		$ext = pathinfo($flag[0], PATHINFO_EXTENSION);
		if (!in_array($ext, $supported_formats, true)) {
			echo 'Error: ' . $flag[0] . ' uses unsupported format .' . $ext, PHP_EOL;
			exit(1);
		}
		$contents = file_get_contents($flag[0]);
		if ($contents === false) {
			echo 'Error: Unable to open ' . $contents, PHP_EOL;
			exit(1);
		}
		$ghSearchUrl = 'https://github.com/search?q=' . urlencode("repo:FreshRSS/FreshRSS path:app/i18n/$lang /(TODO|DIRTY)$/");
		$genPath = __DIR__ . '/flags/gen/' . $lang . '.svg';
		$template = '';
		if ($ext === 'txt') {
			$value = trim($contents) . ' ' . $value;
			$template = <<<EOF
			<svg xmlns="http://www.w3.org/2000/svg" width="70" height="20">
				<g fill="white" font-size="12" font-family="Verdana" text-anchor="middle">
					<rect rx="3" width="70" height="20" fill="$color" />
					<text x="34" y="14">$value</text>
				</g>
			</svg>
			EOF;
		} else {
			$contents = embedSvg($contents);
			$template = <<<EOF
			<svg xmlns="http://www.w3.org/2000/svg" width="70" height="20">
				<g fill="white" font-size="12" font-family="Verdana" text-anchor="middle">
					<rect rx="3" width="70" height="20" fill="$color" />
					<!-- embedded SVG -->
					$contents
					<!-- end of embedded SVG -->
					<text x="45" y="14">$value</text>
				</g>
			</svg>
			EOF;
		}
		if (file_put_contents($genPath, $template) === false) {
			echo 'Error: Fail while generating flag for ' . $lang, PHP_EOL;
			exit(1);
		}
		$markdownImgStr .= "[![$lang](./cli/flags/gen/$lang.svg)]($ghSearchUrl) ";
	}
	// In case we're located in ./cli/
	if (!file_exists('constants.php')) {
		chdir('..');
	}
	foreach (array_merge(['README.md'], glob('README.*.md') ?: []) as $readmePath) {
		writeToReadme($readmePath, rtrim($markdownImgStr));
	}
	exit();
}

if (!$isValidated) {
	exit(1);
}

/**
 * Find used translation keys in the project
 *
 * Iterates through all php and phtml files in the whole project and extracts all
 * translation keys used.
 *
 * @return list<string>
 */
function findUsedTranslations(): array {
	$directory = new RecursiveDirectoryIterator(__DIR__ . '/..');
	$iterator = new RecursiveIteratorIterator($directory);
	$regex = new RegexIterator($iterator, '/^.+\.(php|phtml)$/i', RecursiveRegexIterator::GET_MATCH);
	$usedI18n = [];
	foreach (array_keys(iterator_to_array($regex)) as $file) {
		if (!is_string($file) || $file === '') {
			continue;
		}
		$fileContent = file_get_contents($file);
		if ($fileContent === false) {
			continue;
		}
		preg_match_all('/_t\([\'"](?P<strings>[^\'"]+)[\'"]/', $fileContent, $matches);
		$usedI18n = array_merge($usedI18n, $matches['strings']);
	}
	return $usedI18n;
}

/**
 * Output help message.
 */
function checkHelp(): never {
	$file = str_replace(__DIR__ . '/', '', __FILE__);

	echo <<<HELP
NAME
	$file

SYNOPSIS
	php $file [OPTION]...

DESCRIPTION
	Check if translation files have missing keys or missing translations.

	-d, --display-result	display results.
	-h, --help		display this help and exit.
	-l, --language=LANG	filter by LANG.
	-r, --display-report	display completion report.

HELP;
	exit();
}
