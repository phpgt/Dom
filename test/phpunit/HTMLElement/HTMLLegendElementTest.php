<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLLegendElementTest extends HTMLElementTestCase {
	public function testFormNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("legend");
		self::assertNull($sut->form);
	}

	public function testFormWithinForm():void {
// This should still return null, as HTMLLegendElement::form simply mimics its
// parent fieldset's form property value.
		$document = new HTMLDocument();
		$sut = $document->createElement("legend");
		$form = $sut->ownerDocument->createElement("form");
		$form->appendChild($sut);
		self::assertNull($sut->form);
	}

	public function testFormWithinFieldset():void {
// This should still return null, as HTMLLegendElement::form simply mimics its
// parent fieldset's form property value.
		$document = new HTMLDocument();
		$sut = $document->createElement("legend");
		$form = $sut->ownerDocument->createElement("form");
		$fieldset = $sut->ownerDocument->createElement("fieldset");
		$fieldset->appendChild($sut);
		$form->appendChild($fieldset);
		self::assertSame($form, $sut->form);
	}
}
