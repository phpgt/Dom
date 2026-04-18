<?php
namespace GT\Dom;

use Gt\PropFunc\MagicProp;

class XMLDocument extends Document {
	use MagicProp;

	public function __construct(
		string $xml = "<?xml ?>",
		string $characterSet = "UTF-8",
	) {
		parent::__construct(
			$characterSet,
			"application/xml",
		);

		$this->loadXML($xml, LIBXML_SCHEMA_CREATE | LIBXML_COMPACT);
		if(is_null($this->documentElement)) {
			$this->appendChild($this->createElement("xml"));
		}
	}
}
