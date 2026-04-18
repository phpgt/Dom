<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\ElementType;
use GT\Dom\Exception\HierarchyRequestError;
use GT\Dom\Exception\IndexSizeException;
use GT\Dom\HTMLCollection;
use GT\Dom\HTMLDocument;
use TypeError;

class HTMLTableElementTest extends HTMLElementTestCase {
	public function testCreateTable():void {
		$document = new HTMLDocument();
		$table = $document->createElement("table");
		self::assertSame(ElementType::HTMLTableElement, $table->elementType);
	}

	public function testCreateTHead():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('thead');
		$table->createTHead();
		$thead = $table->createTHead();
		self::assertSame(ElementType::HTMLTableSectionElement, $thead->elementType);
		self::assertCount(1, $col);
	}

	public function testCreateTHeadPosition():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$table->createCaption();
		$colgroup = $table->ownerDocument->createElement('colgroup');
		$table->appendChild($colgroup);
		$table->createTBody();
		$table->createTFoot();
		$thead = $table->createTHead();
		self::assertSame($table->childNodes[2], $thead);
	}

	public function testDeleteTHead():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('thead');
		$table->createTHead();
		$table->deleteTHead();
		self::assertCount(0, $col);
	}

	public function testCreateTFoot():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tfoot');
		$table->createTFoot();
		$tfoot = $table->createTFoot();
		self::assertSame(ElementType::HTMLTableSectionElement, $tfoot->elementType);
		self::assertCount(1, $col);
	}

	public function testCreateTFootPosition():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$table->createCaption();
		$colgroup = $table->ownerDocument->createElement('colgroup');
		$table->appendChild($colgroup);
		$table->createTHead();
		$table->createTBody();
		$thead = $table->createTFoot();
		self::assertSame($table->childNodes[4], $thead);
	}

	public function testDeleteTFoot():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tfoot');
		$table->createTFoot();
		$table->deleteTFoot();
		self::assertCount(0, $col);
	}

	public function testCreateTBody():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tbody');
		$tbody = $table->createTBody();
		self::assertSame(ElementType::HTMLTableSectionElement, $tbody->elementType);
		$table->createTBody();
		self::assertCount(2, $col);
	}

	public function testCreateTBodyPosition():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$table->createCaption();
		$colgroup = $table->ownerDocument->createElement('colgroup');
		$table->appendChild($colgroup);
		$table->createTHead();
		$tbody = $table->createTBody();
		self::assertSame($table->lastElementChild, $tbody);
		$tbody = $table->createTBody();
		self::assertSame($table->lastElementChild, $tbody);
	}

	public function testCreateCaption():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('caption');
		$table->createCaption();
		$caption = $table->createCaption();
		self::assertSame(ElementType::HTMLTableCaptionElement, $caption->elementType);
		self::assertSame($table->firstElementChild, $caption);
		self::assertCount(1, $col);
	}

	public function testDeleteCaption():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('caption');
		$table->createCaption();
		$table->deleteCaption();
		self::assertCount(0, $col);
	}

	public function testInsertRow():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tr');
		$tr = $table->insertRow();
		self::assertSame(ElementType::HTMLTableRowElement, $tr->elementType);
		$table->insertRow();
		self::assertCount(2, $col);
	}

	public function testInsertRowRangeBelow():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(IndexSizeException::class);
		$table->insertRow();
		$table->insertRow(-3);
	}

	public function testInsertRowRangeAbove():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(IndexSizeException::class);
		$table->insertRow();
		$table->insertRow(3);
	}

	public function testInsertRowLast():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tr');
		$table->insertRow();
		$table->insertRow();
		$tr = $table->insertRow(-1);
		self::assertSame($col->item(2), $tr);
		$tr = $table->insertRow(3);
		self::assertSame($col->item(3), $tr);
	}

	public function testInsertRowBetween():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tr');
		$table->insertRow();
		$tr = $table->insertRow();
		$trBefore = $table->insertRow(1);
		self::assertSame($col->item(1), $trBefore);
		self::assertSame($col->item(2), $tr);
	}

	public function testInsertRowBodyLast():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$table->createTBody();
		$table->createTBody();
		$tr = $table->insertRow();
		self::assertSame($table->lastElementChild, $tr->parentNode);
	}

	public function testDeleteRow():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tr');
		$table->insertRow();
		$table->insertRow();
		$table->deleteRow(0);
		self::assertCount(1, $col);
	}

	public function testDeleteRowRangeBelow():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(IndexSizeException::class);
		$table->deleteRow(-3);
	}

	public function testDeleteRowRangeAbove():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(IndexSizeException::class);
		$table->insertRow();
		$table->insertRow();
		$table->insertRow();
		$table->deleteRow(3);
	}

	public function testDeleteRowLast():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tr');
		$table->insertRow();
		$table->insertRow();
		$table->insertRow();
		$table->deleteRow(-1);
		self::assertCount(2, $col);
	}

	public function testGetCaption():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		self::assertNull($table->caption);
		$table->createCaption();
		self::assertSame(ElementType::HTMLTableCaptionElement, $table->caption->elementType);
		self::assertSame($table, $table->caption->parentNode);
		self::assertSame($table->querySelector('caption:first-of-type'), $table->caption);
	}

	public function testSetCaption():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('caption');
		$document = $table->ownerDocument;
		$caption = $document->createElement('caption');
		$table->caption = $caption;
		self::assertSame($table->firstElementChild, $table->caption);
		self::assertCount(1, $col);
		$table->caption = null;
		self::assertCount(0, $col);
	}

	public function testSetCaptionWrongType():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(TypeError::class);
		/** @phpstan-ignore-next-line the type error is intended in this test */
		$table->caption = $table->ownerDocument->createElement('div');
	}

	public function testGetTHead():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		self::assertNull($table->tHead);
		$table->createTHead();
		self::assertSame(ElementType::HTMLTableSectionElement, $table->tHead->elementType);
		self::assertSame($table, $table->tHead->parentNode);
		self::assertSame($table->querySelector('thead:first-of-type'), $table->tHead);
	}

	public function testSetTHead():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$document = $table->ownerDocument;
		$col = $table->getElementsByTagName('thead');
		$thead = $document->createElement('thead');
		$table->tHead = $thead;
		self::assertSame($table->firstElementChild, $table->tHead);
		$table->tHead = null;
		self::assertCount(0, $col);
	}

	public function testSetTHeadPosition():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$document = $table->ownerDocument;
		$col = $table->getElementsByTagName('thead');
		$table->createCaption();
		$table->appendChild($document->createElement('colgroup'));
		$table->createTBody();
		$table->createTFoot();
		$thead = $document->createElement('thead');
		$table->tHead = $thead;
		self::assertSame($table->childNodes[2], $table->tHead);
		$table->tHead = null;
		self::assertCount(0, $col);
	}

	public function testSetTHeadWrongType():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(HierarchyRequestError::class);
		/** @phpstan-ignore-next-line the type error is intended here */
		$table->tHead = $table->ownerDocument->createElement('tbody');
	}

	public function testGetTFoot():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		self::assertNull($table->tFoot);
		$table->createTFoot();
		self::assertSame(ElementType::HTMLTableSectionElement, $table->tFoot->elementType);
		self::assertSame($table, $table->tFoot->parentNode);
		self::assertSame($table->querySelector('tfoot:first-of-type'), $table->tFoot);
	}

	public function testSetTfoot():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$col = $table->getElementsByTagName('tfoot');
		$tfoot = $table->ownerDocument->createElement('tfoot');
		$table->tFoot = $tfoot;
		self::assertSame($table->lastElementChild, $table->tFoot);
		$table->tFoot = null;
		self::assertCount(0, $col);
	}

	public function testSetTfootPosition():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$document = $table->ownerDocument;
		$table->createCaption();
		$colgroup = $document->createElement('colgroup');
		$table->appendChild($colgroup);
		$table->createTHead();
		$table->createTBody();
		$tfoot = $document->createElement('tfoot');
		$table->tFoot = $tfoot;
		self::assertSame($table->lastElementChild, $table->tFoot);
	}

	public function testSetTFootWrongType():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		$this->expectException(TypeError::class);
		$this->expectException(HierarchyRequestError::class);
		/** @phpstan-ignore-next-line the type error is intended here */
		$table->tFoot = $table->ownerDocument->createElement('thead');
	}

	public function testGetRowsOrder():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		self::assertInstanceOf(HTMLCollection::class, $table->rows);
		$table->createTBody();
		$row = $table->insertRow();
		$row->id = '3';
		$row = $table->insertRow();
		$row->id = '4';
		$el = $table->createTFoot();
		$row = $table->ownerDocument->createElement('tr');
		$row->id = '5';
		$el->appendChild($row);
		$el = $table->createTHead();
		$row = $table->ownerDocument->createElement('tr');
		$row->id = '1';
		$el->appendChild($row);
		$row = $table->ownerDocument->createElement('tr');
		$row->id = '2';
		$el->appendChild($row);
		$table->createTBody();
		$row = $table->insertRow();
		$row->id = '6';
		$i = 1;
		self::assertCount(6, $table->rows);
		foreach($table->rows as $row) {
			self::assertEquals((string)$i, $row->id);
			$i++;
		}
	}

	public function testGetRowsDirectChildrenOnly():void {
		$document = new HTMLDocument();
		$table1 = $document->createElement('table');
		$table2 = $document->createElement('table');
		$table1->createTBody();
		$tr1 = $table1->insertRow();
		$td1 = $table1->ownerDocument->createElement('td');
		$tr1->appendChild($td1);
		$table1->insertRow();
		$table2->createTBody();
		$table2->insertRow();
		$table2->insertRow();
		$td1->appendChild($table2);
		self::assertCount(2, $table1->rows);
	}

	public function testGetRowsWithNoBody():void {
		$document = new HTMLDocument();
		$table = $document->createElement("table");
		$row1 = $table->ownerDocument->createElement("tr");
		$row2 = $table->ownerDocument->createElement("tr");
		$table->append($row1, $row2);

		self::assertCount(2, $table->rows);
	}

	public function testGetTBodies():void {
		$document = new HTMLDocument();
		$table = $document->createElement('table');
		self::assertInstanceOf(HTMLCollection::class, $table->tBodies);
		$table->createTHead();
		$table->createTBody();
		$table->appendChild($table->ownerDocument->createElement('tr'));
		self::assertInstanceOf(HTMLCollection::class, $table->tBodies);
		foreach($table->tBodies as $tbody) {
			self::assertEquals('tbody', $tbody->nodeName);
			self::assertSame($table, $tbody->parentNode);
		}
	}

	public function testHeadBodyFootOrder():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("table");
		$thead = $sut->createTHead();
		$tfoot = $sut->createTFoot();
		$tbody = $sut->createTBody();

		$expectedOrder = [$thead, $tbody, $tfoot];

		foreach($sut->children as $i => $childElement) {
			self::assertSame($expectedOrder[$i], $childElement);
		}
	}

	public function testSetThead_nonThead():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("table");
		self::expectException(TypeError::class);
		self::expectExceptionMessage("tHead must be of type HTMLTableSectionElement");
		$sut->tHead = $document->createElement("head");
	}
}
