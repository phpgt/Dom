<?php
namespace GT\Dom\Test;

use GT\Dom\Document;
use GT\Dom\Exception\DocumentStreamIsClosedException;
use GT\Dom\Exception\DocumentStreamNotWritableException;
use GT\Dom\Exception\DocumentStreamSeekFailureException;
use GT\Dom\Exception\WriteOnNonHTMLDocumentException;
use GT\Dom\HTMLDocument;
use GT\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class DocumentStreamTest extends TestCase {
	public function testDetachBeforeOpen():void {
		$sut = new HTMLDocument();
		self::expectException(DocumentStreamIsClosedException::class);
		$sut->detach();
	}

	public function testDetach():void {
		$sut = new HTMLDocument();
		$sut->open();
		$stream = $sut->detach();
		self::assertIsResource($stream);
	}

	public function testDetachNonWritable():void {
		$sut = new HTMLDocument();
		$sut->open();
		$sut->detach();
		self::expectException(DocumentStreamNotWritableException::class);
		$sut->write("test");
	}

	public function testGetSize():void {
		$sut = new HTMLDocument();
		$sut->open();
		self::assertEquals(
			strlen("<!doctype html>\n<html><head></head><body></body></html>\n"),
			$sut->getSize()
		);
	}

	public function testGetSizeAfterWriting():void {
		$sut = new HTMLDocument();
		$sut->open();
		$sut->body->appendChild($sut->createElement("example"));
		self::assertEquals(strlen("<!doctype html>\n<html><head></head><body><example></example></body></html>\n"), $sut->getSize());
	}

	public function testTell():void {
		$sut = new HTMLDocument();
		$sut->open();
		self::assertEquals(0, $sut->tell());
	}

	public function testWritableBeforeOpen():void {
		$sut = new HTMLDocument();
		self::assertFalse($sut->isWritable());
	}

	public function testWritable():void {
		$sut = new HTMLDocument();
		$sut->open();
		self::assertTrue($sut->isWritable());
	}

	public function testEof():void {
		$sut = new HTMLDocument();
		$sut->open();
		$sut->body->appendChild($sut->createElement("example"));
		$bytes = "";
		while(!$sut->eof()) {
			$bytes .= $sut->read(10);
		}
		self::assertEquals("<!doctype html>\n<html><head></head><body><example></example></body></html>\n", $bytes);
	}

	public function testIsSeekableBeforeOpen():void {
		$sut = new HTMLDocument();
		self::assertFalse($sut->isSeekable());
	}

	public function testIsSeekable():void {
		$sut = new HTMLDocument();
		$sut->open();
		self::assertTrue($sut->isSeekable());
	}

	public function testSeekBeforeOpen():void {
		$sut = new HTMLDocument();
		self::expectException(DocumentStreamIsClosedException::class);
		$sut->seek(1);
	}

	public function testSeek():void {
		$sut = new HTMLDocument();
		$sut->body->appendChild($sut->createElement("example"));
		$sut->open();
		$sut->seek(10);
		self::assertEquals(10, $sut->tell());
	}

	public function testRewind():void {
		$sut = new HTMLDocument();
		$sut->body->appendChild($sut->createElement("example"));
		$sut->open();
		$sut->seek(10);
		$sut->rewind();
		self::assertEquals(0, $sut->tell());
	}

	public function testIsReadableBeforeOpen():void {
		$sut = new HTMLDocument();
		self::assertFalse($sut->isReadable());
	}

	public function testIsReadable():void {
		$sut = new HTMLDocument();
		$sut->open();
		self::assertTrue($sut->isReadable());
	}

	public function testGetContentsBeforeOpen():void {
		$sut = new HTMLDocument();
		self::expectException(DocumentStreamIsClosedException::class);
		$sut->getContents();
	}

	public function testGetContents():void {
		$sut = new HTMLDocument();
		$sut->body->appendChild($sut->createElement("example"));
		$sut->open();
		$contents = $sut->getContents();
		self::assertEquals("<!doctype html>\n<html><head></head><body><example></example></body></html>\n", $contents);
	}

	public function testGetMetaData():void {
		$sut = new HTMLDocument();
		$sut->open();
		$meta = $sut->getMetadata();
		self::assertArrayHasKey("timed_out", $meta);
		self::assertArrayHasKey("blocked", $meta);
		self::assertArrayHasKey("eof", $meta);
		self::assertArrayHasKey("unread_bytes", $meta);
		self::assertArrayHasKey("stream_type", $meta);
		self::assertArrayHasKey("wrapper_type", $meta);
		self::assertArrayHasKey("mode", $meta);
		self::assertArrayHasKey("seekable", $meta);
		self::assertArrayHasKey("uri", $meta);
	}

	public function testXMLDocumentNonWritable():void {
		$sut = new XMLDocument();
		self::expectException(DocumentStreamNotWritableException::class);
		$sut->write("test");
	}
}
