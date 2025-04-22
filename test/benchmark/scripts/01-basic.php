<?php /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
use Dom\HTMLDocument as NativeHTMLDocument;
use Gt\Dom\HTMLDocument;

require __DIR__ . "/../../../vendor/autoload.php";

$timeArray = [];

foreach([HTMLDocument::class, NativeHTMLDocument::class] as $domClassName) {
	$timeStart = microtime(true);
	echo "Using $domClassName", PHP_EOL;
	$document = match($domClassName) {
		HTMLDocument::class => new HTMLDocument(),
		NativeHTMLDocument::class => NativeHTMLDocument::createFromString(HTMLDocument::DOCTYPE),
	};

	$element = $document->createElement("example-element");
	$document->body->appendChild($element);

	echo str_replace("\n", "", $document->saveHTML()), PHP_EOL;
	$timeEnd = microtime(true);
	$timeArray[$domClassName] = $timeEnd - $timeStart;
}

echo PHP_EOL;

foreach($timeArray as $className => $time) {
	echo "$className: " . number_format($time, 5), PHP_EOL;
}

$percentDifference = abs(
	($timeArray[array_keys($timeArray)[0]] - $timeArray[array_keys($timeArray)[1]])
	/ max($timeArray) * 100
);
echo "Speed increase: " . number_format($percentDifference, 0) . "%", PHP_EOL;
