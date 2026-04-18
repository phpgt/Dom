<?php
namespace GT\Dom\ClientSide;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;

abstract class ClientSideOnly {
	public function __construct() {
		throw new ClientSideOnlyFunctionalityException();
	}
}
