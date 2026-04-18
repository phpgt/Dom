<?php
// phpcs:ignoreFile
namespace GT\Dom;

if (version_compare(PHP_VERSION, '8.4', '>=')) {
	class Node extends Node84 {
	}
}
else {
	class Node extends Node83 {
	}
}
