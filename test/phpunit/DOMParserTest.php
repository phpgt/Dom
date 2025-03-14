<?php
namespace Gt\Dom\Test;

use Gt\Dom\DOMParser;
use Gt\Dom\Exception\MimeTypeNotSupportedException;
use Gt\Dom\HTMLDocument;
use Gt\Dom\Test\TestFactory\DocumentTestFactory;
use Gt\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class DOMParserTest extends TestCase {
	public function testParseFromStringUnknownType():void {
		$sut = new DOMParser();
		self::expectException(MimeTypeNotSupportedException::class);
		$sut->parseFromString("", "text/unknown");
	}

	public function testParseFromStringEmptyHTML():void {
		$sut = new DOMParser();
		$doc = $sut->parseFromString("<!doctype html>", "text/html");
		self::assertInstanceOf(HTMLDocument::class, $doc);
	}

	public function testParseFromStringEmptyXML():void {
		$sut = new DOMParser();
		$doc1 = $sut->parseFromString(DocumentTestFactory::XML_BOOK, "text/xml");
		$doc2 = $sut->parseFromString(DocumentTestFactory::XML_BOOK, "application/xml");
		$doc3 = $sut->parseFromString(DocumentTestFactory::XML_BOOK, "application/xhtml+xml");
		self::assertInstanceOf(XMLDocument::class, $doc1);
		self::assertInstanceOf(XMLDocument::class, $doc2);
		self::assertInstanceOf(XMLDocument::class, $doc3);
	}

	public function testParseFromStringEmptySVG():void {
		$sut = new DOMParser();
		$doc = $sut->parseFromString(DocumentTestFactory::XML_SHAPE, "image/svg+xml");
		self::assertInstanceOf(XMLDocument::class, $doc);
	}

	public function testParseFromStringUTF8():void {
		/** @noinspection HtmlRequiredLangAttribute */
		$html = <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test!</title>
</head>
<body>
    <h1>☆ Hello ☆ World ☆</h1>
</body>
</html>
HTML;

		$sut = new DOMParser();
		$document = $sut->parseFromString($html, "text/html");
		self::assertStringContainsString(
			"<h1>☆ Hello ☆ World ☆</h1>",
			$document
		);
	}

	public function testParseStringWithStyle(): void {
		/** @noinspection HtmlRequiredLangAttribute */
		$html = <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test!</title>
</head>
<body>
    <style>
    </style>
    <h1>☆ Hello ☆ World ☆</h1>
</body>
</html>
HTML;
		$sut = new DOMParser();
		$document = $sut->parseFromString($html, "text/html");
		self::assertEquals(
			'☆ Hello ☆ World ☆',
			trim($document->querySelector('body')->innerText)
		);
	}
}
