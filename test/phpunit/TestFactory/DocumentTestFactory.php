<?php /** @noinspection JSUnresolvedLibraryURL */
namespace GT\Dom\Test\TestFactory;

use GT\Dom\DOMParser;
use GT\Dom\HTMLDocument;
use GT\Dom\XMLDocument;

class DocumentTestFactory {
	const HTML_DEFAULT = <<<HTML
<!doctype html>
<h1>Hello, PHP.Gt!</h1>
HTML;
	const HTML_DEFAULT_UTF8 = <<<HTML
<!doctype html>
<meta charset="utf-8" />
<h1>Hello, PHP.Gt!</h1>
HTML;

	const HTML_EMBED = <<<HTML
<!doctype html>
<h1>Here is an embedded video</h1>
<embed type="video/webm"
       src="/media/cc0-videos/flower.mp4"
       width="250"
       height="200" />
HTML;

	const HTML_COMMENT = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Comment test</title>
</head>
<body>
	<h1>Hello, PHP.Gt!</h1>
	<!--this is a comment-->
	<h2>There is a comment in this document</h2>
</body>
</html>
HTML;

	const HTML_COMMENT_FIRST_CHILD = <<<HTML
<!--this is a comment-->
<h1>Hello, PHP.Gt!</h1>
HTML;

	const HTML_COMMENT_MULTILINE = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Multi-line comment test</title>
</head>
<body>
	<h1>Hello, PHP.Gt!</h1>
	<!--
	this is a comment
	it spans multiple lines
	thank you, have a nice day
	-->
	<h2>There is a multi-line comment in this document</h2>
</body>
</html>
HTML;

	const HTML_COMMENT_NESTED = <<<HTML
<!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Comment test</title>
</head>
<body>
	<h1>Hello, PHP.Gt!</h1>
	<!--this is a comment-->
	<h2>There are three comments in this document</h2>
	<div>
		<h3>This DIV contains another comment.</h3>
		<!--this is another comment-->	
		<!--and another!-->	
	</div>
</body>
</html>
HTML;

	const HTML_FORMS = <<<HTML
<!doctype html>
<main>
	<form>
		<h1>This is a GET form</h1>
		
		<label>
			<span>Search query</span>
			<input name="q" required />		
		</label>
		
		<button>Search!</button>
	</form>
	
	<form method="post">
		<h1>This is a POST form</h1>
		
		<label>
			<span>Your name</span>
			<input name="name" required />
		</label>
		<label>
			<span>Your email</span>
			<input name="email" type="email" required />
		</label>
		<label>
			<span>Where do you live?</span>
			<input name="continent" value="africa" /> Africa <br />
			<input name="continent" value="america" /> America <br />
			<input name="continent" value="antarctica" /> Antarctica <br />
			<input name="continent" value="asia" /> Asia <br />
			<input name="continent" value="australia" /> Australia <br />
			<input name="continent" value="europe" /> Europe <br />
		</label>
		
		<button>Submit</button>
	</form>
</main>
HTML;

	const HTML_RADIO_BUTTONS = <<<HTML
<!doctype html>
<form method="post">
	<h1>What is your favourite bean?</h1>
	
	<label>
		<input type="radio" name="bean" value="black-eyed-pea" />
		<span>Black eyed pea</span>
	</label>
	<label>
		<input type="radio" name="bean" value="cannellini" />
		<span>Cannellini</span>
	</label>
	<label>
		<input type="radio" name="bean" value="kidney" />
		<span>Kidney</span>
	</label>
	<label>
		<input type="radio" name="bean" value="fava" />
		<span>Fava</span>
	</label>
	<label>
		<input type="radio" name="bean" value="edamame" />
		<span>Edamame</span>
	</label>
</form>
HTML;


	const HTML_IMAGES = <<<HTML
<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>My Photos</title>
	</head>
<body>
	<h1>Take a look at these amazing photos!</h1>

	<ul>
		<li><img src="/photo/cat.jpg" alt="My cat" /></li>
		<li><img src="/photo/tree.jpg" alt="My cat in a tree" /></li>
		<li>
			<img src="/photo/bed.jpg" alt="My cat in bed" />
		</li>
		<li><img src="/photo/backflip.jpg" alt="My cat doing a backflip" /></li>	
	</ul>
</body>
</html>
HTML;

	const HTML_PAGE = <<<HTML
<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>My Website!</title>
		<script src="https://invasive-tracker.weownyou.com/analytics.js"></script>
	</head>
<body>
	<a href="#top-of-page" class="hidden">Skip to content</a>
	
	<header>
		<a href="/">
			<h1 class="big-text">My Website!</h1>
		</a>
		
		<nav>
			<ul>
				<li class="icon icon-home"><a href="/">Home</a></li>
				<li class="icon icon-about"><a href="/about">About me</a></li>
				<li class="icon icon-projects"><a href="/projects">My projects</a></li>
				<li class="icon icon-contact"><a href="/contact">Contact me</a></li>
			</ul>
		</nav>
	</header>
	
	<main>
		<h1>
			<a id="top-of-page" class="big-text green">Welcome to my site!</a>
		</h1>
		
		<article>
			<h1>This is my example website.</h1>
			<p>Thank you for visiting.</p>
			<p>If you want, take a look at <a href="/projects" class="icon icon-projects">my projects</a>. They are really interesting to me.</p>
			<p>Do you want to talk? <a href="/contact" class="icon icon-contact">Give me a buzz!</a></p>
		</article>
	</main>
	
	<footer>
		<span>Copyright &copy; forever, me.</span>	
		<span>I ❤️ 🐢</span>
	</footer>
	
	<script src="/my-script.js"></script>
</body>
</html>
HTML;

	const HTML_AREA = <<<HTML
<map name="infographic">
    <area shape="rect" coords="184,6,253,27"
          href="https://mozilla.org"
          target="_blank" alt="Mozilla" />
    <area shape="circle" coords="130,136,60"
          href="https://developer.mozilla.org/"
          target="_blank" alt="MDN" />
    <area shape="poly" coords="130,6,253,96,223,106,130,39"
          href="https://developer.mozilla.org/docs/Web/Guide/Graphics"
          target="_blank" alt="Graphics" />
    <area shape="poly" coords="253,96,207,241,189,217,223,103"
          href="https://developer.mozilla.org/docs/Web/HTML"
          target="_blank" alt="HTML" />
    <area shape="poly" coords="207,241,54,241,72,217,189,217"
          href="https://developer.mozilla.org/docs/Web/JavaScript"
          target="_blank" alt="JavaScript" />
    <area shape="poly" coords="54,241,6,97,36,107,72,217"
          href="https://developer.mozilla.org/docs/Web/API"
          target="_blank" alt="Web APIs" />
    <area shape="poly" coords="6,97,130,6,130,39,36,107"
          href="https://developer.mozilla.org/docs/Web/CSS"
          target="_blank" alt="CSS" />
</map>
<img usemap="#infographic" src="/media/examples/mdn-info.png" alt="MDN infographic" />
<p>This example was lifted directly from <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/area">Mozilla Developer Network</a>.</p>
HTML;


	const XML_DEFAULT = <<<XML
<?xml version="1.0" ?>
<example/>
XML;
	const XML_BREAKFAST_MENU = <<<XML
<?xml version="1.0" ?>
<menu>
<food>
    <name>Belgian Waffles</name>
    <price>$5.95</price>
    <description>Two of our famous Belgian Waffles 
      with plenty of real maple syrup.</description>
    <calories>650</calories>
  </food>
  <food>
    <name>Strawberry Belgian Waffles</name>
    <price>$7.95</price>
    <description>Light Belgian waffles covered with 
     strawberries and whipped cream.</description>
    <calories>900</calories>
  </food>
  <food>
    <name>Berry-Berry Belgian Waffles</name>
    <price>$8.95</price>
    <description>Light Belgian waffles covered 
      with an assortment of fresh berries 
      and whipped cream.</description>
    <calories>900</calories>
  </food>
  <food>
    <name>French Toast</name>
    <price>$4.50</price>
    <description>Thick slices made from our homemade 
     sourdough bread.</description>
    <calories>600</calories>
  </food>
  <food>
    <name>Homestyle Breakfast</name>
    <price>$6.95</price>
    <description>Two eggs, bacon or sausage, toast, 
      and our ever-popular hash browns.</description>
    <calories>950</calories>
  </food>
</menu>
XML;
	const XML_BOOK = <<<XML
<!DOCTYPE book [<!ENTITY h 'hardcover'>]>
<book genre='novel' ISBN='1-861001-57-5'>
	<title>Pride And Prejudice</title>
	<style>&h;</style>
</book>
XML;
	const XML_ANIMAL_PARTS = <<<XML
<?xml version="1.0" ?>
<animal>
	<head>
		<ears>2</ears>
		<eyes>2</eyes>
		<mouth>1</mouth>
	</head>
	<body>
		<legs>4</legs>
		<coat>Fur</coat>
	</body>
	<tail/>
</animal>
XML;

	const XML_SHAPE = <<<XML
<?xml version="1.0"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" 
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg xmlns="http://www.w3.org/2000/svg"
    xmlns:test="http://www.example.com/2014/test" width="40" height="40">
  <circle id="target" cx="12" cy="12" r="10" stroke="#444" stroke-width="2"
      fill="none" test:foo="Foo value"/>
  <test:rect id="namespaced" x="10" y="10" width="12" height="16" />
</svg>

XML;
	const HTML_INLINE_SCRIPT_WITH_TAGS = <<<HTML
<!doctype html>
<body>
	<h1>Hello, JavaScript!</h1>
	<script>
	{
	    const btn = document.querySelector("#test3");
	    btn.addEventListener("click", e => {
		document.querySelector("#test3div").innerHTML = `
		    <div class="module">
			<h2>lorem ipsum</h2>
			<p>lorem <strong>lorem ipsum</strong></p>
			<button class="button">click</button>
		    </div>
		`;
	    });
	}
	</script>
</body>
HTML;

	public static function createHTMLDocument(
		string $html = self::HTML_DEFAULT
	):HTMLDocument {
		$parser = new DOMParser();
		return $parser->parseFromString($html, "text/html");
	}

	public static function createXMLDocument(
		string $xml = self::XML_DEFAULT
	):XMLDocument {
		$parser = new DOMParser();
		return $parser->parseFromString($xml, "text/xml");
	}

	public static function createDOMImplementation():DOMImplementation {
		$document = self::createHTMLDocument();
		return $document->implementation;
	}
}
