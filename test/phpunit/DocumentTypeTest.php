<?php
namespace GT\Dom\Test;

use GT\Dom\Document;
use GT\Dom\HTMLDocument;
use GT\Dom\Test\TestFactory\DocumentTestFactory;
use GT\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class DocumentTypeTest extends TestCase {
	public function testName_HTML():void {
		$document = new HTMLDocument();
		$sut = $document->doctype;
		self::assertSame("html", $sut->name);
	}

	public function testName_XML():void {
		$document = new XMLDocument(DocumentTestFactory::XML_BOOK);
		$sut = $document->doctype;
		self::assertSame("book", $sut->name);
	}

	public function testIsEqualNode():void {
		$htmlDocument = new HTMLDocument(DocumentTestFactory::HTML_PAGE);
		$sut = $htmlDocument->doctype;
		$xmlDocument = new XMLDocument(DocumentTestFactory::XML_SHAPE);
		$xmlType = $xmlDocument->doctype;
		self::assertFalse($sut->isEqualNode($xmlType));
	}

	public function testPublicId():void {
		$sut = (new HTMLDocument())->doctype;
		self::assertEquals("", $sut->publicId);
	}

	public function testSystemId():void {
		$sut = (new HTMLDocument())->doctype;
		self::assertEquals("", $sut->systemId);
	}
}
