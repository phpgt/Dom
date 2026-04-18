<?php
namespace GT\Dom\Test\HTMLElement;

use DateTime;
use GT\Dom\ClientSide\FileList;
use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLInputElementTest extends HTMLElementTestCase {
	public function testChecked():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateBool($sut, "checked");
	}

	public function testDefaultChecked():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateBool($sut, "checked", "defaultChecked");
	}

	public function testIndeterminateGet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->indeterminate;
	}

	public function testIndeterminateSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->indeterminate = true;
	}

	public function testAlt():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "alt");
	}

	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int", "height");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int", "width");
	}

	public function testAccept():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "accept");
	}

	public function testFilesGet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->files;
	}

	public function testFilesSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$files = self::createMock(FileList::class);
		$sut->files = $files;
	}

	public function testFormActionDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "formaction", "formAction");
	}

	public function testFormActionWithinForm():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		/** @var HTMLFormElement $form */
		$form = $sut->ownerDocument->createElement("form");
		$form->action = "/example";
		$form->appendChild($sut);
		self::assertEquals("/example", $sut->formAction);
	}

	public function testFormEncTypeDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "formenctype", "formEncType");
	}

	public function testFormEncTypeWithinForm():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		/** @var HTMLFormElement $form */
		$form = $sut->ownerDocument->createElement("form");
		$form->enctype = "test/example";
		$form->appendChild($sut);
		self::assertEquals("test/example", $sut->formEncType);
	}

	public function testFormMethodDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "formmethod");
	}

	public function testFormMethodWithinForm():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		/** @var HTMLFormElement $form */
		$form = $sut->ownerDocument->createElement("form");
		$form->method = "EXAMPLE";
		$form->appendChild($sut);
		self::assertEquals("EXAMPLE", $sut->formMethod);
	}

	public function testFormNoValidate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateBool($sut, "formnovalidate", "formNoValidate");
	}

	public function testFormTargetDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "formtarget", "formTarget");
	}

	public function testFormTargetWithinForm():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		/** @var HTMLFormElement $form */
		$form = $sut->ownerDocument->createElement("form");
		$form->target = "/example";
		$form->appendChild($sut);
		self::assertEquals("/example", $sut->formTarget);
	}

	public function testMax():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "max");
	}

	public function testMaxLength():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:-1", "maxlength", "maxLength");
	}

	public function testMin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "min");
	}

	public function testMinLength():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:-1", "minlength", "minLength");
	}

	public function testPattern():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "pattern");
	}

	public function testPlaceholder():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "placeholder");
	}

	public function testReadOnly():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateBool($sut, "readonly", "readOnly");
	}

	public function testSize():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int","size");
	}

	public function testMultiple():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelateBool($sut, "multiple");
	}

	public function testLabelsNested():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$label = $sut->ownerDocument->createElement("label");
		$label->appendChild($sut);
		self::assertSame($label, $sut->labels[0]);
	}

	public function testLabelsFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->id = "test-input";
		/** @var HTMLLabelElement $label */
		$label = $sut->ownerDocument->createElement("label");
		$label->htmlFor = "test-input";

		$sut->ownerDocument->body->append($sut);
		$sut->ownerDocument->body->append($label);

		self::assertSame($label, $sut->labels[0]);
	}

	public function testLabelsMixedNestedFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->id = "test-input";
		/** @var HTMLLabelElement $label1 */
		$label1 = $sut->ownerDocument->createElement("label");
		$label1->htmlFor = "test-input";
		$label2 = $sut->ownerDocument->createElement("label");
		$label2->htmlFor = "test-input";
		$labelParent = $sut->ownerDocument->createElement("label");

		$sut->ownerDocument->body->append($labelParent);
		$labelParent->appendChild($sut);
		$sut->ownerDocument->body->append($label1);
		$sut->ownerDocument->body->append($label2);

		$labelsArray = iterator_to_array($sut->labels->entries());
		self::assertCount(3, $labelsArray);
		foreach([$label1, $label2, $labelParent] as $l) {
			self::assertContains($l, $labelsArray);
		}
	}

	public function testStep():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "step");
	}

	public function testValueAsDateEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertNull($sut->valueAsDate);
	}

	public function testValueAsDateInvalid():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->value = "abc";
		self::assertNull($sut->valueAsDate);
		$sut->value = "completely invalid date format";
		self::assertNull($sut->valueAsDate);
	}

	public function testValueAsDate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$dateString = "1988-05-04T17:21:05";
		$sut->value = $dateString;
		$dateTime = $sut->valueAsDate;
		self::assertNotNull($dateTime);
		self::assertEquals($dateString, $dateTime->format("Y-m-d\TH:i:s"));
	}

	public function testValueAsDateNumeric():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$dateString = "1988-05-04T17:21:05";
		$dateTime = new DateTime($dateString);
		$sut->value = (string)$dateTime->getTimestamp();
		self::assertEquals($dateTime, $sut->valueAsDate);
	}

	public function testValueAsDateSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$dateString = "1988-05-04T17:21:05";
		$dateTime = new DateTime($dateString);
		$sut->valueAsDate = $dateTime;
		self::assertEquals($dateString, $sut->value);
	}

	public function testValueAsNumberEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertNull($sut->valueAsNumber);
	}

	public function testValueAsNumberText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->value = "not a number";
		self::assertNull($sut->valueAsNumber);
	}

	public function testValueAsNumberTextWithNumber():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->value = "     10.5 ";
		self::assertSame(10.5, $sut->valueAsNumber);
	}

	public function testValueAsNumberTimestamp():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$dateString = "1988-05-04T17:21:05";
		$sut->type = "datetime-local";
		$sut->value = $dateString;
		$number = $sut->valueAsNumber;
		self::assertEquals(
			(new DateTime($dateString))->getTimestamp(),
			$number
		);
	}

	public function testValueAsNumberSetTimestamp():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->type = "datetime-local";
		$dateString = "1988-05-04T17:21:05";
		$dateTime = new DateTime($dateString);
		$sut->valueAsNumber = $dateTime->getTimestamp();
		self::assertEquals($dateTime, $sut->valueAsDate);
	}

	public function testValueAsNumberDateInvalid():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->type = "datetime-local";
		$sut->value = "invalid datetime";
		self::assertNull($sut->valueAsNumber);
	}

	public function testValueAsNumberSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->valueAsNumber = 12.34;
		self::assertSame("12.34", $sut->value);
	}

	public function testAutoCapitalize():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "autocapitalize");
	}

	public function testInputMode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		self::assertPropertyAttributeCorrelate($sut, "inputmode");
	}
}
