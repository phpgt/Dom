<?php
namespace Gt\Dom;

use Gt\Dom\ClientSide\AudioTrackList;
use Gt\Dom\ClientSide\CSSStyleDeclaration;
use Gt\Dom\ClientSide\MediaController;
use Gt\Dom\ClientSide\MediaError;
use Gt\Dom\ClientSide\MediaStream;
use Gt\Dom\ClientSide\TextTrackList;
use Gt\Dom\ClientSide\TimeRanges;
use Gt\Dom\ClientSide\ValidityState;
use Gt\Dom\ClientSide\VideoTrackList;
use Gt\Dom\Exception\ClientSideOnlyFunctionalityException;
use Gt\Dom\Exception\IncorrectHTMLElementUsageException;

/**
 * The DOM object model is a strange design, made even stranger by the libxml
 * implementation used by PHP. In order for this library to take advantage of
 * the highly optimised speed of libxml, the classes registered as "node
 * classes" from Document::registerNodeClasses all have to extend the base
 * DOMNode classes, but cannot extend each other. Therefore, even though a
 * DOMElement extends a DOMNode, and a Gt\Dom\Element extends DOMElement and a
 * Gt\Dom\Node extends a DOMNode, it is in fact impossible for a Gt\Dom\Element
 * to extend a Gt\Dom\Node.
 *
 * This is all handled by the underlying implementation, so there is not really
 * any downside, apart from the hierarchy being confusing. What is limited
 * however is the lack of HTMLElement classes that specify the individual
 * functionality of each type of HTML Element - for example, a HTMLSelectElement
 * has a property "options" which contains a list of HTMLOptionElements - this
 * property doesn't make sense to be available to all Elements, so this trait
 * works as a compromise.
 *
 * The intention is to provide IDEs with well-typed autocompletion, but without
 * straying too far from the official specification. This trait contains all
 * functionality introduced by all HTMLElement subtypes, but before each
 * property or method is called, a list of "allowed" Elements is checked,
 * throwing a IncorrectHTMLElementUsageException if incorrectly used.
 *
 * @property string $hreflang Is a DOMString that reflects the hreflang HTML attribute, indicating the language of the linked resource.
 * @property string $text Is a DOMString being a synonym for the Node.textContent property.
 * @property string $type Is a DOMString that reflects the type HTML attribute, indicating the MIME type of the linked resource.
 * @property string $name Is a DOMString representing the name of the object when submitted with a form. If specified, it must not be the empty string.
 * @property bool $checked Returns / Sets the current state of the element when type is checkbox or radio.
 * @property string $href Is a USVString that is the result of parsing the href HTML attribute relative to the document, containing a valid URL of a linked resource.
 * @property string $download Is a DOMString indicating that the linked resource is intended to be downloaded rather than displayed in the browser. The value represent the proposed name of the file. If the name is not a valid filename of the underlying OS, browser will adapt it.
 * @property string $hash Is a USVString representing the fragment identifier, including the leading hash mark ('#'), if any, in the referenced URL.
 * @property string $host Is a USVString representing the hostname and port (if it's not the default port) in the referenced URL.
 * @property string $hostname Is a USVString representing the hostname in the referenced URL.
 * @property-read string $origin Returns a USVString containing the origin of the URL, that is its scheme, its domain and its port.
 * @property string $password Is a USVString containing the password specified before the domain name.
 * @property string $pathname Is a USVString containing an initial '/' followed by the path of the URL, not including the query string or fragment.
 * @property string $port Is a USVString representing the port component, if any, of the referenced URL.
 * @property string $protocol Is a USVString representing the protocol component, including trailing colon (':'), of the referenced URL.
 * @property string $referrerPolicy Is a DOMString that reflects the referrerpolicy HTML attribute indicating which referrer to use.
 * @property string $rel Is a DOMString that reflects the rel HTML attribute, specifying the relationship of the target object to the linked object.
 * @property-read DOMTokenList $relList Returns a DOMTokenList that reflects the rel HTML attribute, as a list of tokens.
 * @property string $search Is a USVString representing the search element, including leading question mark ('?'), if any, of the referenced URL.
 * @property string $target Is a DOMString that reflects the target HTML attribute, indicating where to display the linked resource.
 * @property string $username Is a USVString containing the username specified before the domain name.
 * @property string $alt Is a DOMString that reflects the alt HTML attribute, containing alternative text for the element.
 * @property string $coords Is a DOMString that reflects the coords HTML attribute, containing coordinates to define the hot-spot region.
 * @property string $shape Is a DOMString that reflects the shape HTML attribute, indicating the shape of the hot-spot, limited to known values.
 * @property-read AudioTrackList $audioTracks A AudioTrackList that lists the AudioTrack objects contained in the element.
 * @property bool $autoplay A Boolean that reflects the autoplay HTML attribute, indicating whether playback should automatically begin as soon as enough media is available to do so without interruption.
 * @property-read TimeRanges $buffered Returns a TimeRanges object that indicates the ranges of the media source that the browser has buffered (if any) at the moment the buffered property is accessed.
 * @property-read ?MediaController $controller Is a MediaController object that represents the media controller assigned to the element, or null if none is assigned.
 * @property bool $controls Is a Boolean that reflects the controls HTML attribute, indicating whether user interface items for controlling the resource should be displayed.
 * @property-read DOMTokenList $controlsList Returns a DOMTokenList that helps the user agent select what controls to show on the media element whenever the user agent shows its own set of controls. The DOMTokenList takes one or more of three possible values: nodownload, nofullscreen, and noremoteplayback.
 * @property string $crossOrigin A DOMString indicating the CORS setting for this media element.
 * @property-read string $currentSrc Returns a DOMString with the absolute URL of the chosen media resource.
 * @property float $currentTime A double-precision floating-point value indicating the current playback time in seconds; if the media has not started to play and has not been seeked, this value is the media's initial playback time. Setting this value seeks the media to the new time. The time is specified relative to the media's timeline.
 * @property bool $defaultMuted A Boolean that reflects the muted HTML attribute, which indicates whether the media element's audio output should be muted by default.
 * @property float $defaultPlaybackRate A double indicating the default playback rate for the media.
 * @property bool $disableRemotePlayback A Boolean that sets or returns the remote playback state, indicating whether the media element is allowed to have a remote playback UI.
 * @property-read ?float $duration A read-only double-precision floating-point value indicating the total duration of the media in seconds. If no media data is available, the returned value is NaN. If the media is of indefinite length (such as streamed live media, a WebRTC call's media, or similar), the value is +Infinity.
 * @property-read bool $ended Returns a Boolean that indicates whether the media element has finished playing.
 * @property-read ?MediaError $error Returns a MediaError object for the most recent error, or null if there has not been an error.
 * @property bool $loop A Boolean that reflects the loop HTML attribute, which indicates whether the media element should start over when it reaches the end.
 * @property string $mediaGroup A DOMString that reflects the mediagroup HTML attribute, which indicates the name of the group of elements it belongs to. A group of media elements shares a common MediaController.
 * @property bool $muted Is a Boolean that determines whether audio is muted. true if the audio is muted and false otherwise.
 * @property-read int $networkState Returns an unsigned short (enumeration) indicating the current state of fetching the media over the network.
 * @property-read bool $paused Returns a Boolean that indicates whether the media element is paused.
 * @property float $playbackRate Is a double that indicates the rate at which the media is being played back.
 * @property-read TimeRanges $played Returns a TimeRanges object that contains the ranges of the media source that the browser has played, if any.
 * @property string $preload Is a DOMString that reflects the preload HTML attribute, indicating what data should be preloaded, if any. Possible values are: none, metadata, auto.
 * @property-read int $readyState Returns an unsigned short (enumeration) indicating the readiness state of the media.
 * @property-read TimeRanges $seekable Returns a TimeRanges object that contains the time ranges that the user is able to seek to, if any.
 * @property-read bool $seeking Returns a Boolean that indicates whether the media is in the process of seeking to a new position.
 * @property-read string $sinkId Returns a DOMString that is the unique ID of the audio device delivering output, or an empty string if it is using the user agent default. This ID should be one of the MediaDeviceInfo.deviceid values returned from MediaDevices.enumerateDevices(), id-multimedia, or id-communications.
 * @property string $src Is a DOMString that reflects the src HTML attribute, which contains the URL of a media resource to use.
 * @property ?MediaStream $srcObject Is a MediaStream representing the media to play or that has played in the current HTMLMediaElement, or null if not assigned.
 * @property-read TextTrackList $textTracks Returns the list of TextTrack objects contained in the element.
 * @property-read VideoTrackList $videoTracks Returns the list of VideoTrack objects contained in the element.
 * @property float $volume Is a double indicating the audio volume, from 0.0 (silent) to 1.0 (loudest).
 * @property bool $autofocus Is a Boolean indicating whether the control should have input focus when the page loads, unless the user overrides it, for example by typing in a different control. Only one form-associated element in a document can have this attribute specified.
 * @property bool $disabled Is a Boolean indicating whether the control is disabled, meaning that it does not accept any clicks.
 * @property-read ?Element $form Is a HTMLFormElement reflecting the form that this element is associated with.
 * @property-read NodeList $labels Is a NodeList that represents a list of <label> elements that are labels for this HTMLUIElement.
 * @property bool $readOnly Returns / Sets the element's readonly attribute, indicating that the user cannot modify the value of the control.
 * @property bool $required Returns / Sets the element's required attribute, indicating that the user must fill in a value before submitting a form.
 * @property-read bool $willValidate Is a Boolean indicating whether the button is a candidate for constraint validation. It is false if any conditions bar it from constraint validation, including: its type property is reset or button; it has a <datalist> ancestor; or the disabled property is set to true.
 * @property-read string $validationMessage Is a DOMString representing the localized message that describes the validation constraints that the control does not satisfy (if any). This attribute is the empty string if the control is not a candidate for constraint validation (willValidate is false), or it satisfies its constraints.
 * @property-read ValidityState $validity Is a ValidityState representing the validity states that this button is in.
 * @property string $value Is a DOMString representing the current form control value of the HTMLUIElement.
 * @property-read ?Element $control Is a HTMLElement representing the control with which the label is associated.
 * @property string $htmlFor Is a string containing the ID of the labeled control. This reflects the for attribute.
 * @property int $height The height HTML attribute of the <canvas> element is a positive integer reflecting the number of logical pixels (or RGBA values) going down one column of the canvas. When the attribute is not specified, or if it is set to an invalid value, like a negative, the default value of 150 is used. If no [separate] CSS height is assigned to the <canvas>, then this value will also be used as the height of the canvas in the length-unit CSS Pixel.
 * @property int $width The width HTML attribute of the <canvas> element is a positive integer reflecting the number of logical pixels (or RGBA values) going across one row of the canvas. When the attribute is not specified, or if it is set to an invalid value, like a negative, the default value of 300 is used. If no [separate] CSS width is assigned to the <canvas>, then this value will also be used as the width of the canvas in the length-unit CSS Pixel.
 * @property-read HTMLCollection $options Is a HTMLCollection representing a collection of the contained option elements.
 * @property bool $open Is a boolean reflecting the open HTML attribute, indicating whether or not the element’s contents (not counting the <summary>) is to be shown to the user.
 * @property string $returnValue A DOMString that sets or returns the return value for the dialog.
 */
trait HTMLElement {
	private function allowTypes(ElementType...$typeList):void {
		if(!in_array($this->elementType, $typeList)) {
			$debug = debug_backtrace(limit: 2);
			$function = $debug[1]["function"];
			if(str_starts_with($function, "__prop")) {
				$funcProp = "Property";
				$funcPropName = substr($function, strlen("__prop_get_"));
			}
			else {
				$funcProp = "Function";
				$funcPropName = $function;
			}

			/** @var Element $object */
			$object = $debug[1]["object"];
			$actualType = $object->elementType->name;
			throw new IncorrectHTMLElementUsageException("$funcProp '$funcPropName' is not available on '$actualType'");
		}
	}

	public function __toString():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);

		if($this->elementType === ElementType::HTMLAnchorElement
		|| $this->elementType === ElementType::HTMLAreaElement) {
			return $this->href;
		}
		else {
			return "";
		}
	}

	/**
	 * Builds and returns a URL string from the existing href attribute
	 * value with the newly supplied overrides.
	 */
	private function buildUrl(
		string $scheme = null,
		string $user = null,
		string $pass = null,
		string $host = null,
		int $port = null,
		string $path = null,
		string $query = null,
		string $fragment = null,
	):string {
		$existing = parse_url($this->href);
		$new = [
			"scheme" => $scheme,
			"user" => $user,
			"pass" => $pass,
			"host" => $host,
			"port" => $port,
			"path" => $path,
			"query" => $query,
			"fragment" => $fragment,
		];
		// Remove null new parts.
		$new = array_filter($new);
		if(isset($new["query"])) {
			$new["query"] = ltrim($new["query"], "?");
		}
		if(isset($new["fragment"])) {
			$new["fragment"] = ltrim($new["fragment"], "#");
		}

		$url = "";
		if($addScheme = $new["scheme"] ?? $existing["scheme"] ?? null) {
			$url .= "$addScheme://";
		}
		if($addUser = $new["user"] ?? $existing["user"] ?? null) {
			$url .= $addUser;

			if($addPass = $new["pass"] ?? $existing["pass"] ?? null) {
				$url .= ":$addPass";
			}

			$url .= "@";
		}
		if($addHost = $new["host"] ?? $existing["host"] ?? null) {
			$url .= $addHost;
		}
		if($addPort = $new["port"] ?? $existing["port"] ?? null) {
			$url .= ":$addPort";
		}
		if($addPath = $new["path"] ?? $existing["path"] ?? null) {
			$url .= $addPath;
		}
		if($addQuery = $new["query"] ?? $existing["query"] ?? null) {
			$url .= "?$addQuery";
		}
		if($addFrag = $new["fragment"] ?? $existing["fragment"] ?? null) {
			$url .= "#$addFrag";
		}

		return $url;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/style */
	protected function __prop_get_style():CSSStyleDeclaration {
		return new CSSStyleDeclaration();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/style */
	protected function __prop_set_style(CSSStyleDeclaration $value):void {
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hreflang */
	protected function __prop_get_hreflang():string {
		$this->allowTypes(ElementType::HTMLAnchorElement);
		return $this->getAttribute("hreflang") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hreflang */
	protected function __prop_set_hreflang(string $value):void {
		$this->allowTypes(ElementType::HTMLAnchorElement);
		$this->setAttribute("hreflang", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/text */
	protected function __prop_get_text():string {
		$this->allowTypes(ElementType::HTMLAnchorElement);
		return $this->textContent;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/text */
	protected function __prop_set_text(string $value):void {
		$this->allowTypes(ElementType::HTMLAnchorElement);
		$this->textContent = $value;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/type */
	protected function __prop_get_type():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
		);
		return $this->getAttribute("type") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/type */
	protected function __prop_set_type(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
		);
		$this->setAttribute("type", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/name */
	protected function __prop_get_name():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
		);
		return $this->getAttribute("name") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/name */
	protected function __prop_set_name(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
		);
		$this->setAttribute("name", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/value
	 */
	protected function __prop_get_value():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLDataElement,
			ElementType::HTMLOptionElement,
		);
		$value = $this->getAttribute("value");
		if(!is_null($value)) {
			return $value;
		}

		if($this->elementType === ElementType::HTMLSelectElement) {
			if($this->selectedIndex === -1) {
				return "";
			}

			return $this->options[$this->selectedIndex]->value;
		}

		return $this->textContent;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/value
	 */
	protected function __prop_set_value(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLDataElement,
			ElementType::HTMLOptionElement,
		);
		$this->setAttribute("value", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#checked */
	protected function __prop_get_checked():bool {
		$this->allowTypes(ElementType::HTMLInputElement);
		return $this->hasAttribute("checked");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#checked */
	protected function __prop_set_checked(bool $value):void {
		$this->allowTypes(ElementType::HTMLInputElement);
		if($value) {
			$this->setAttribute("checked", "");
		}
		else {
			$this->removeAttribute("checked");
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement/href
	 */
	protected function __prop_get_href():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
		);
		return $this->getAttribute("href") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement/href
	 */
	protected function __prop_set_href(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
		);
		$this->setAttribute("href", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/download
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/download
	 */
	protected function __prop_get_download():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return $this->getAttribute("download") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/download
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/download
	 */
	protected function __prop_set_download(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->setAttribute("download", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hash
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/hash
	 */
	protected function __prop_get_hash():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		if($hash = parse_url($this->href, PHP_URL_FRAGMENT)) {
			return "#$hash";
		}

		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hash
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/hash
	 */
	protected function __prop_set_hash(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			fragment: $value
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/host
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/host
	 */
	protected function __prop_get_host():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		if($host = parse_url($this->href, PHP_URL_HOST)) {
			$port = parse_url($this->href, PHP_URL_PORT);
			if($port) {
				return "$host:$port";
			}

			return $host;
		}

		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/host
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/host
	 */
	protected function __prop_set_host(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$newHost = strtok($value, ":");
		$newPort = parse_url($value, PHP_URL_PORT);
		$this->href = $this->buildUrl(
			host: $newHost,
			port: $newPort
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hostname
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/hostname
	 * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
	 */
	protected function __prop_get_hostname():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return parse_url($this->href, PHP_URL_HOST);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hostname */
	protected function __prop_set_hostname(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			host: $value
		);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/origin */
	protected function __prop_get_origin():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$origin = "";
		if($scheme = parse_url($this->href, PHP_URL_SCHEME)) {
			$origin .= "$scheme://";
		}
		if($user = parse_url($this->href, PHP_URL_USER)) {
			$origin .= $user;

			if($pass = parse_url($this->href, PHP_URL_PASS)) {
				$origin .= ":$pass";
			}

			$origin .= "@";
		}
		if($host = parse_url($this->href, PHP_URL_HOST)) {
			$origin .= $host;
		}
		if($port = parse_url($this->href, PHP_URL_PORT)) {
			$origin .= ":$port";
		}

		return $origin;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/password */
	protected function __prop_get_password():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return parse_url($this->href, PHP_URL_PASS) ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/password */
	protected function __prop_set_password(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			pass: $value
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/pathname
	 * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
	 */
	protected function __prop_get_pathname():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return parse_url($this->href, PHP_URL_PATH);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/pathname
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/pathname
	 */
	protected function __prop_set_pathname(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			path: $value
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/port
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/port
	 */
	protected function __prop_get_port():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return parse_url($this->href, PHP_URL_PORT) ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/port
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/port
	 */
	protected function __prop_set_port(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			port: (int)$value
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/protocol
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/protocol
	 */
	protected function __prop_get_protocol():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		if($scheme = parse_url($this->href, PHP_URL_SCHEME)) {
			return "$scheme:";
		}

		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/protocol
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/protocol
	 */
	protected function __prop_set_protocol(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			scheme: $value
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/referrerPolicy
	 */
	protected function __prop_get_referrerPolicy():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return $this->getAttribute("referrerpolicy") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/referrerPolicy
	 */
	protected function __prop_set_referrerPolicy(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->setAttribute("referrerpolicy", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/rel
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/rel
	 */
	protected function __prop_get_rel():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return $this->getAttribute("rel") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/rel
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/rel
	 */
	protected function __prop_set_rel(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->setAttribute("rel", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/relList
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/relList
	 */
	protected function __prop_get_relList():DOMTokenList {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return DOMTokenListFactory::create(
			fn() => explode(" ", $this->rel),
			fn(string...$tokens) => $this->rel = implode(" ", $tokens)
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/search
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/search
	 */
	protected function __prop_get_search():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		if($query = parse_url($this->href, PHP_URL_QUERY)) {
			return "?$query";
		}

		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/search
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/search
	 */
	protected function __prop_set_search(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			query: $value
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement/target
	 */
	protected function __prop_get_target():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
		);
		return $this->getAttribute("target") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement/target
	 */
	protected function __prop_set_target(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
		);
		$this->setAttribute("target", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/username
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/username
	 */
	protected function __prop_get_username():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		return parse_url($this->href, PHP_URL_USER) ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/username
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/username
	 */
	protected function __prop_set_username(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
		);
		$this->href = $this->buildUrl(
			user: $value
		);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/alt */
	protected function __prop_get_alt():string {
		$this->allowTypes(ElementType::HTMLAreaElement);
		return $this->getAttribute("alt") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/alt */
	protected function __prop_set_alt(string $value):void {
		$this->allowTypes(ElementType::HTMLAreaElement);
		$this->setAttribute("alt", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/coords */
	protected function __prop_get_coords():string {
		$this->allowTypes(ElementType::HTMLAreaElement);
		return $this->getAttribute("coords") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/coords */
	protected function __prop_set_coords(string $value):void {
		$this->allowTypes(ElementType::HTMLAreaElement);
		$this->setAttribute("coords", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/shape */
	protected function __prop_get_shape():string {
		$this->allowTypes(ElementType::HTMLAreaElement);
		return $this->getAttribute("shape") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/shape */
	protected function __prop_set_shape(string $value):void {
		$this->allowTypes(ElementType::HTMLAreaElement);
		$this->setAttribute("shape", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/audioTracks */
	protected function __prop_get_audioTracks():AudioTrackList {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new AudioTrackList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/autoplay */
	protected function __prop_get_autoplay():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->hasAttribute("autoplay");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/autplay */
	protected function __prop_set_autoplay(bool $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		if($value) {
			$this->setAttribute("autoplay", "");
		}
		else {
			$this->removeAttribute("autoplay");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/buffered */
	protected function __prop_get_buffered():TimeRanges {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new TimeRanges();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controller */
	protected function __prop_get_controller():?MediaController {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controls */
	protected function __prop_get_controls():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->hasAttribute("controls");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controls */
	protected function __prop_set_controls(bool $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		if($value) {
			$this->setAttribute("controls", "");
		}
		else {
			$this->removeAttribute("controls");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controlsList */
	protected function __prop_get_controlsList():DOMTokenList {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return DOMTokenListFactory::create(
			fn() => explode(
				" ",
				$this->getAttribute("controlsList")
			),
			fn(string...$tokens) => $this->setAttribute(
				"controlsList",
				implode(" ", $tokens)
			)
		);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/crossOrigin */
	protected function __prop_get_crossOrigin():string {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->getAttribute("crossorigin") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/crossOrigin */
	protected function __prop_set_crossOrigin(string $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		$this->setAttribute("crossorigin", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentSrc */
	protected function __prop_get_currentSrc():string {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentTime */
	protected function __prop_get_currentTime():float {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentTime */
	protected function __prop_set_currentTime(float $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultMuted */
	protected function __prop_get_defaultMuted():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->hasAttribute("muted");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultMuted */
	protected function __prop_set_defaultMuted(bool $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		if($value) {
			$this->setAttribute("muted", "");
		}
		else {
			$this->removeAttribute("muted");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultPlaybackRate */
	protected function __prop_get_defaultPlaybackRate():float {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultPlaybackRate */
	protected function __prop_set_defaultPlaybackRate(float $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/disableRemotePlayback */
	protected function __prop_get_disableRemotePlayback():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/disableRemotePlayback */
	protected function __prop_set_disableRemotePlayback(bool $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/duration */
	protected function __prop_get_duration():?float {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/ended */
	protected function __prop_get_ended():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return false;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/error */
	protected function __prop_get_error():?MediaError {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/loop */
	protected function __prop_get_loop():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->hasAttribute("loop");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/loop */
	protected function __prop_set_loop(bool $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		if($value) {
			$this->setAttribute("loop", "");
		}
		else {
			$this->removeAttribute("loop");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/mediaGroup */
	protected function __prop_get_mediaGroup():string {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->getAttribute("mediagroup") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/mediaGroup */
	protected function __prop_set_mediaGroup(string $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		$this->setAttribute("mediagroup", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/muted */
	protected function __prop_get_muted():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException("Use defaultMuted for server-side use");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/muted */
	protected function __prop_set_muted(bool $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException("Use defaultMuted for server-side use");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/networkState */
	protected function __prop_get_networkState():int {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/paused */
	protected function __prop_get_paused():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/playbackRate */
	protected function __prop_get_playbackRate():float {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/playbackRate */
	protected function __prop_set_playbackRate(float $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/played */
	protected function __prop_get_played():TimeRanges {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new TimeRanges();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/preload */
	protected function __prop_get_preload():string {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->getAttribute("preload") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/preload */
	protected function __prop_set_preload(string $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		$this->setAttribute("preload", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/readyState */
	protected function __prop_get_readyState():int {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/seekable */
	protected function __prop_get_seekable():TimeRanges {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new TimeRanges();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/seeking */
	protected function __prop_get_seeking():bool {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return false;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/sinkId */
	protected function __prop_get_sinkId():string {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/src */
	protected function __prop_get_src():string {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return $this->getAttribute("src") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/src */
	protected function __prop_set_src(string $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		$this->setAttribute("src", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/srcObject */
	protected function __prop_get_srcObject():MediaStream {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new MediaStream();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/srcObject */
	protected function __prop_set_srcObject(MediaStream $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/textTracks */
	protected function __prop_get_textTracks():TextTrackList {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new TextTrackList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/videoTracks */
	protected function __prop_get_videoTracks():VideoTrackList {
		$this->allowTypes(ElementType::HTMLAudioElement);
		return new VideoTrackList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/volume */
	protected function __prop_get_volume():float {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/volume */
	protected function __prop_set_volume(float $value):void {
		$this->allowTypes(ElementType::HTMLAudioElement);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/autofocus */
	protected function __prop_get_autofocus():bool {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return $this->hasAttribute("autofocus");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/autofocus */
	protected function __prop_set_autofocus(bool $value):void {
		$this->allowTypes(ElementType::HTMLButtonElement);
		if($value) {
			$this->setAttribute("autofocus", "");
		}
		else {
			$this->removeAttribute("autofocus");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/disabled */
	protected function __prop_get_disabled():bool {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return $this->hasAttribute("disabled");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/disabled */
	protected function __prop_set_disabled(bool $value):void {
		$this->allowTypes(ElementType::HTMLButtonElement);
		if($value) {
			$this->setAttribute("disabled", "");
		}
		else {
			$this->removeAttribute("disabled");
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/form
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement/form
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/form
	 */
	protected function __prop_get_form():?Element {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLLabelElement,
			ElementType::HTMLInputElement,
		);
		$context = $this;
		while($context->parentElement) {
			$context = $context->parentElement;

			if($context->elementType === ElementType::HTMLFormElement) {
				return $context;
			}
		}

		if($this->elementType === ElementType::HTMLLabelElement) {
			if($input = $this->control) {
				return $input->form;
			}
		}

		return null;
	}

//	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/labels */
//	protected function __prop_get_labels():NodeList {
//		return NodeListFactory::createLive(function():array {
//			$labelsArray = [];
//			foreach($this->ownerDocument->getElementsByTagName("label") as $label) {
//				/** @var HTMLLabelElement $label */
//				if($label->htmlFor === $this->id) {
//					array_push($labelsArray, $label);
//				}
//			}
//
//			return $labelsArray;
//		});
//	}

	protected function __prop_get_readOnly():bool {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return $this->hasAttribute("readonly");
	}

	protected function __prop_set_readOnly(bool $value):void {
		$this->allowTypes(ElementType::HTMLButtonElement);
		if($value) {
			$this->setAttribute("readonly", "");
		}
		else {
			$this->removeAttribute("readonly");
		}
	}

	protected function __prop_get_required():bool {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return $this->hasAttribute("required");
	}

	protected function __prop_set_required(bool $value):void {
		$this->allowTypes(ElementType::HTMLButtonElement);
		if($value) {
			$this->setAttribute("required", "");
		}
		else {
			$this->removeAttribute("required");
		}
	}

	protected function __prop_get_willValidate():bool {
		$this->allowTypes(ElementType::HTMLButtonElement);
		if($this->elementType === ElementType::HTMLButtonElement) {
			return false;
		}

		if($this->disabled) {
			return false;
		}

		if(in_array($this->type, ["hidden", "reset", "button"])) {
			return false;
		}

		$context = $this;
		while($context->parentElement) {
			$context = $context->parentElement;
			if($context instanceof HTMLDataListElement) {
				return false;
			}
		}

		return true;
	}

	protected function __prop_get_validationMessage():string {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return "";
	}

	protected function __prop_get_validity():ValidityState {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return new ValidityState();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-labels */
	protected function __prop_get_labels():NodeList {
		$this->allowTypes(ElementType::HTMLButtonElement);
		$input = $this;
		return NodeListFactory::createLive(function() use($input) {
			$labelsArray = [];

			$context = $input;
			while($context = $context->parentElement) {
				if($context instanceof HTMLLabelElement) {
					array_push($labelsArray, $context);
					break;
				}
			}

			if($id = $input->id) {
				foreach($input->ownerDocument->querySelectorAll("label[for='$id']") as $label) {
					array_push($labelsArray, $label);
				}
			}

			return $labelsArray;
		});
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement/control */
	protected function __prop_get_control():?Element {
		$this->allowTypes(ElementType::HTMLLabelElement);
		if($for = $this->htmlFor) {
			if($input = $this->ownerDocument->getElementById($for)) {
				return $input;
			}
		}

		$inputList = $this->getElementsByTagName("input");
		return $inputList[0] ?? null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement/htmlFor */
	protected function __prop_get_htmlFor():string {
		$this->allowTypes(ElementType::HTMLLabelElement);
		return $this->getAttribute("for") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement/htmlFor */
	protected function __prop_set_htmlFor(string $value):void {
		$this->allowTypes(ElementType::HTMLLabelElement);
		$this->setAttribute("for", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/height */
	protected function __prop_get_height():int {
		$this->allowTypes(ElementType::HTMLCanvasElement);
		return $this->getAttribute("height") ?? 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/height */
	protected function __prop_set_height(int $value):void {
		$this->allowTypes(ElementType::HTMLCanvasElement);
		$this->setAttribute("height", (string)$value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/width */
	protected function __prop_get_width():int {
		$this->allowTypes(ElementType::HTMLCanvasElement);
		return $this->getAttribute("width") ?? 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/width */
	protected function __prop_set_width(int $value):void {
		$this->allowTypes(ElementType::HTMLCanvasElement);
		$this->setAttribute("width", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataListElement/options
	 */
	protected function __prop_get_options():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLDataListElement,
		);
		return $this->getElementsByTagName("option");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDetailsElement/open
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/open
	 */
	protected function __prop_get_open():bool {
		$this->allowTypes(
			ElementType::HTMLDetailsElement,
			ElementType::HTMLDialogElement,
		);
		return $this->hasAttribute("open");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDetailsElement/open
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/open
	 */
	protected function __prop_set_open(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLDetailsElement,
			ElementType::HTMLDialogElement,
		);

		if($value) {
			$this->setAttribute("open","");
		}
		else {
			$this->removeAttribute("open");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/returnValue */
	protected function __prop_get_returnValue():string {
		throw new ClientSideOnlyFunctionalityException("returnValue");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/returnValue */
	protected function __prop_set_returnValue(string $value):void {
		throw new ClientSideOnlyFunctionalityException("returnValue");
	}
}
