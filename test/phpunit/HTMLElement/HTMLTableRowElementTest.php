<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\IndexSizeException;
use GT\Dom\HTMLDocument;

class HTMLTableRowElementTest extends HTMLElementTestCase {
	public function testDeleteCellEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		self::expectException(IndexSizeException::class);
		$sut->deleteCell(0);
	}

	public function testDeleteCell():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$tdArray = [];
		for($i = 0; $i < 10; $i++) {
			$td = $sut->ownerDocument->createElement("td");
			$sut->appendChild($td);
			array_push($tdArray, $td);
		}

		$sut->deleteCell(4);

		foreach($tdArray as $i => $td) {
			$actualIndex = $i;

			if($i === 4) {
				continue;
			}
			if($i > 4) {
				$actualIndex = $i - 1;
			}

			self::assertSame($td, $sut->getElementsByTagName("td")->item($actualIndex));
		}
	}

	public function testInsertCell():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");

		$td1 = $sut->insertCell();
		$td2 = $sut->insertCell();
		$td3 = $sut->insertCell();

		self::assertSame($td1, $sut->getElementsByTagName("td")->item(0));
		self::assertSame($td2, $sut->getElementsByTagName("td")->item(1));
		self::assertSame($td3, $sut->getElementsByTagName("td")->item(2));
	}

	public function testInsertCellIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");

		$td1 = $sut->insertCell();
		$td1->id = "td1";
		$td2 = $sut->insertCell();
		$td2->id = "td2";
		$td3 = $sut->insertCell();
		$td3->id = "td3";

		$tdNew = $sut->insertCell(1);
		$tdNew->id = "tdNew";

		self::assertSame($tdNew, $sut->getElementsByTagName("td")->item(1));
		self::assertSame($td1, $sut->getElementsByTagName("td")->item(0));
		self::assertSame($td2, $sut->getElementsByTagName("td")->item(2));
		self::assertSame($td3, $sut->getElementsByTagName("td")->item(3));
	}

	public function testInsertCellOutOfBounds():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		self::expectException(IndexSizeException::class);
		$sut->insertCell(-1);
	}

	public function testCells():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$cells = $sut->cells;

		self::assertCount(0, $cells);
		$sut->insertCell();
		self::assertCount(1, $cells);
		$sut->deleteCell(0);
		self::assertCount(0, $cells);
	}

	public function testCells_returnsTdAndTh():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$cells = $sut->cells;
		$sut->innerHTML = "<td>One</td><td>Two</td><td>Three</td>";
		self::assertCount(3, $cells);
		$sut->innerHTML = "";
		self::assertCount(0, $cells);
		$sut->innerHTML = "<th>One</th><th>Two</th><th>Three</th>";
		self::assertCount(3, $cells);
	}

	public function testRowIndexNotInTable():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		self::assertSame(-1, $sut->rowIndex);
	}

	public function testRowIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$table = $sut->ownerDocument->createElement("table");
		$table->appendChild($sut);
		self::assertSame(0, $sut->rowIndex);

		$table->prepend($sut->ownerDocument->createElement("td"));
		$table->prepend($sut->ownerDocument->createElement("td"));
		$table->prepend($sut->ownerDocument->createElement("td"));

		self::assertSame(3, $sut->rowIndex);
	}

	public function testRowIndexSectionsBody():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$table = $sut->ownerDocument->createElement("table");
		$thead = $table->createTHead();
		$thead->insertRow();
		$thead->insertRow();
		$thead->insertRow();
		$tbody = $table->createTBody();
		$tbody->insertRow();
		$tbody->appendChild($sut);
		$tfoot = $table->createTFoot();
		$tfoot->insertRow();
		$tfoot->insertRow();
		$tfoot->insertRow();
// Head, body and foot are out of order! This should not affect the row index.
		$table->append($tfoot, $thead, $tbody);
		self::assertSame(4, $sut->rowIndex);
	}

	public function testRowIndexSectionsHead():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$table = $sut->ownerDocument->createElement("table");
		$thead = $table->createTHead();
		$thead->insertRow();
		$thead->appendChild($sut);
		$tbody = $table->createTBody();
		$tbody->insertRow();
		$tbody->insertRow();
		$tbody->insertRow();
		$tbody->insertRow();
		$tbody->insertRow();
		$tfoot = $table->createTFoot();
		$tfoot->insertRow();
		$tfoot->insertRow();
		$tfoot->insertRow();
		$tfoot->insertRow();
// Head, body and foot are out of order! This should not affect the row index.
		$table->append($tbody, $tfoot, $thead);
		self::assertSame(1, $sut->rowIndex);
	}

	public function testRowIndexSectionsFoot():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$table = $sut->ownerDocument->createElement("table");
		$thead = $table->createTHead();
		$thead->insertRow();
		$thead->insertRow();
		$tbody = $table->createTBody();
		$tbody->insertRow();
		$tbody->insertRow();
		$tbody->insertRow();
		$tbody->insertRow();
		$tbody->insertRow();
		$tfoot = $table->createTFoot();
		$tfoot->insertRow();
		$tfoot->insertRow();
		$tfoot->insertRow();
		$tfoot->insertRow();
		$tfoot->appendChild($sut);
// Head, body and foot are out of order! This should not affect the row index.
		$table->append($tbody, $tfoot, $thead);
		self::assertSame(11, $sut->rowIndex);
	}

	public function testRowIndexSectionsFootWithSectionsInOrder():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$table = $sut->ownerDocument->createElement("table");
		$thead = $table->createTHead();
		$thead->insertRow();
		$tbody = $table->createTBody();
		$tbody->insertRow();
		$tfoot = $table->createTFoot();
		$tfoot->insertRow();
		$tfoot->appendChild($sut);
		$table->append($thead, $tbody, $tfoot);
		self::assertSame(3, $sut->rowIndex);
	}

	public function testRowIndexSectionsFootWithEmptySections():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$table = $sut->ownerDocument->createElement("table");
		$thead = $table->createTHead();
		$tbody = $table->createTBody();
		$tfoot = $table->createTFoot();
		$tfoot->appendChild($sut);
		$table->append($thead, $tbody, $tfoot);
		self::assertSame(0, $sut->rowIndex);
	}

	public function testSectionRowIndexEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		self::assertSame(-1, $sut->sectionRowIndex);
	}

	public function testSectionRowIndexFirst():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$tbody = $sut->ownerDocument->createElement("tbody");
		$tbody->appendChild($sut);
		self::assertSame(0, $sut->sectionRowIndex);
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut->cloneNode());
		self::assertSame(0, $sut->sectionRowIndex);
	}

	public function testSectionRowIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tr");
		$tbody = $sut->ownerDocument->createElement("tbody");
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut->cloneNode());
		$tbody->appendChild($sut);
		self::assertSame(4, $sut->sectionRowIndex);
	}
}
