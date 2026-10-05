<?php
namespace GT\Dom\Test;

use GT\Dom\HTMLDocument;
use GT\Dom\MagicProp;
use PHPUnit\Framework\TestCase;

class MagicPropTest extends TestCase {
	public function testLegacyAccessorsRemainUsable():void {
		$subject = new class {
			use MagicProp;

			private string $storedValue = "before";

			protected function __prop_get_example():string {
				return $this->storedValue;
			}

			protected function __prop_set_example(string $value):void {
				$this->storedValue = $value;
			}
		};

		self::assertTrue(isset($subject->example));
		self::assertSame("before", $subject->example);
		$subject->example = "after";
		self::assertSame("after", $subject->example);
	}

	public function testCamelCaseAccessorsAreSupported():void {
		$subject = new class {
			use MagicProp;

			private string $storedValue = "before";

			protected function __propGetExample():string {
				return $this->storedValue;
			}

			protected function __propSetExample(string $value):void {
				$this->storedValue = $value;
			}
		};

		$subject->example = "after";
		self::assertSame("after", $subject->example);
	}

	public function testDocumentSubclassesKeepLegacyAccessors():void {
		$document = new class extends HTMLDocument {
			protected function __prop_get_example():string {
				return "subclass";
			}
		};

		self::assertSame("subclass", $document->example);
	}
}
