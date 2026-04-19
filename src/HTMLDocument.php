<?php
namespace GT\Dom;

use Gt\PropFunc\MagicProp;

/**
 * @property-read ?Element $body The Document.body property represents the <body> or <frameset> node of the current document, or null if no such element exists.
 * @property-read ?Element $head Returns the <head> element of the current document.
 * @property-read HTMLCollection $embeds Returns a list of the embedded <embed> elements within the current document.
 * @property-read HTMLCollection $forms Returns a list of the <form> elements within the current document.
 * @property-read HTMLCollection $images Returns a list of the images in the current document.
 * @property-read HTMLCollection $links Returns a list of all the hyperlinks in the document.
 * @property-read HTMLCollection $scripts Returns all the script elements on the document.
 * @property string $title Sets or gets the title of the current document.
 *
 * @method getElementsByTagName(string $tagName)
 */
// phpcs:disable Generic.Metrics.CyclomaticComplexity
class HTMLDocument extends Document {
	use MagicProp;

	public function __construct(
		string $html = "<!doctype html>",
		string $characterSet = "UTF-8"
	) {
		parent::__construct(
			$characterSet,
			"text/html",
		);

// Workaround for handling UTF-8 encoding correctly.
// @link https://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly
		$html = '<?xml encoding="'
			. strtolower($this->encoding)
			. '" ?>'
			. $html;
		$this->loadHTML($html, LIBXML_SCHEMA_CREATE | LIBXML_COMPACT);
		foreach($this->childNodes as $child) {
// For the workaround noted above, the functionality changes slightly after PHP 8.4.
			if(version_compare(PHP_VERSION, "8.4") >= 0) {
				if(str_contains($child->nodeValue ?? "", 'encoding="utf-8" ?')) {
					$this->removeChild($child);
				}
			}
			else {
				if($child instanceof ProcessingInstruction) {
					$this->removeChild($child);
				}
			}
		}

		/** @var array<Node> $nonElementChildNodes */
		$nonElementChildNodes = [];
		foreach($this->childNodes as $child) {
			if($child instanceof DocumentType
			|| $child instanceof Element) {
				continue;
			}
			array_push($nonElementChildNodes, $child);
		}

		if(is_null($this->documentElement)) {
			$this->appendChild($this->createElement("html"));
		}
		if(is_null($this->head)) {
			$this->documentElement->prepend($this->createElement("head"));
		}
		if(is_null($this->body)) {
			$this->documentElement->append($this->createElement("body"));
		}

		if($nonElementChildNodes) {
			call_user_func(
				$this->documentElement->prepend(...),
				...$nonElementChildNodes,
			);
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/body */
	public function __prop_get_body():null|Element {
		return $this->getElementsByTagName("body")->item(0);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/head */
	public function __prop_get_head():null|Element {
		return $this->getElementsByTagName("head")->item(0);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/embeds */
	public function __prop_get_embeds():HTMLCollection {
		return $this->getElementsByTagName("embed");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/forms */
	public function __prop_get_forms():HTMLCollection {
		return $this->getElementsByTagName("form");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/images */
	public function __prop_get_images():HTMLCollection {
		return $this->getElementsByTagName("img");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/links */
	public function __prop_get_links():HTMLCollection {
		return HTMLCollectionFactory::create(function() {
			$elementList = [];

			$areaList = $this->getElementsByTagName("area");
			for($i = 0, $len = $areaList->length; $i < $len; $i++) {
				array_push($elementList, $areaList->item($i));
			}
			$aList = $this->getElementsByTagName("a");
			for($i = 0, $len = $aList->length; $i < $len; $i++) {
				$domNode = $aList->item($i);
				$hrefAttr = $domNode->attributes->getNamedItem(
					"href"
				);
				if(!$hrefAttr) {
					continue;
				}
				array_push($elementList, $domNode);
			}

			return NodeListFactory::create(...$elementList);
		});
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/scripts */
	public function __prop_get_scripts():HTMLCollection {
		return $this->getElementsByTagName("script");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/title */
	protected function __prop_get_title():string {
		$titleElement = $this->head?->getElementsByTagName("title")?->item(0);
		return $titleElement?->text ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Document/title */
	protected function __prop_set_title(string $value):void {
		if(!$titleElement = $this->head?->getElementsByTagName("title")?->item(0)) {
			$titleElement = $this->createElement("title");
			$this->head->appendChild($titleElement);
		}

		$titleElement->text = $value;
	}
}
