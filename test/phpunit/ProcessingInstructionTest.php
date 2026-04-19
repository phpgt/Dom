<?php
namespace GT\Dom\Test;

use GT\Dom\ProcessingInstruction;
use GT\Dom\Test\TestFactory\DocumentTestFactory;
use GT\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class ProcessingInstructionTest extends TestCase {
	public function testIsEqualNode():void {
		$document = new XMLDocument();
		$sut = $document->createProcessingInstruction("target", "data");
		$other = $sut->cloneNode();
		self::assertTrue($sut->isEqualNode($other));
	}

	public function testIsEqualNodeNotEqual():void {
		$document = new XMLDocument();
		$sut = $document->createProcessingInstruction("target", "data");
		/** @var ProcessingInstruction $other */
		$other = $sut->cloneNode();
		$other->data = "other-data";
		self::assertFalse($sut->isEqualNode($other));
	}
}
