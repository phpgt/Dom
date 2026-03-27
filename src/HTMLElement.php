<?php
namespace Gt\Dom;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Gt\Dom\ClientSide\AudioTrackList;
use Gt\Dom\ClientSide\CSSStyleDeclaration;
use Gt\Dom\ClientSide\FileList;
use Gt\Dom\ClientSide\MediaController;
use Gt\Dom\ClientSide\MediaError;
use Gt\Dom\ClientSide\MediaStream;
use Gt\Dom\ClientSide\StyleSheet;
use Gt\Dom\ClientSide\TextTrack;
use Gt\Dom\ClientSide\TextTrackList;
use Gt\Dom\ClientSide\TimeRanges;
use Gt\Dom\ClientSide\ValidityState;
use Gt\Dom\ClientSide\VideoTrackList;
use Gt\Dom\Exception\ArrayAccessReadOnlyException;
use Gt\Dom\Exception\ClientSideOnlyFunctionalityException;
use Gt\Dom\Exception\EnumeratedValueException;
use Gt\Dom\Exception\HierarchyRequestError;
use Gt\Dom\Exception\IncorrectHTMLElementUsageException;
use Gt\Dom\Exception\IndexSizeException;
use TypeError;

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
 * @property-read ?Element $form Is a HTMLFormElement reflecting the form that this element is associated with. If the element is a <legend>, if the legend has a fieldset element as its parent, then this attribute returns the same value as the form attribute on the parent fieldset element. Otherwise, it returns null.
 * @property-read NodeList $labels Is a NodeList that represents a list of <label> elements that are labels for this HTMLUIElement.
 * @property bool $readOnly Returns / Sets the element's readonly attribute, indicating that the user cannot modify the value of the control.
 * @property bool $required Returns / Sets the element's required attribute, indicating that the user must fill in a value before submitting a form.
 * @property-read bool $willValidate Is a Boolean indicating whether the button is a candidate for constraint validation. It is false if any conditions bar it from constraint validation, including: its type property is reset or button; it has a <datalist> ancestor; or the disabled property is set to true.
 * @property-read string $validationMessage Is a DOMString representing the localized message that describes the validation constraints that the control does not satisfy (if any). This attribute is the empty string if the control is not a candidate for constraint validation (willValidate is false), or it satisfies its constraints.
 * @property-read ValidityState $validity Is a ValidityState representing the validity states that this button is in.
 * @property string $value Is a DOMString representing the current form control value of the HTMLUIElement.
 * @property-read ?Element $control Is a HTMLElement representing the control with which the label is associated.
 * @property string|DOMTokenList $htmlFor Is a string containing the ID of the labeled control. This reflects the for attribute.
 * @property int $height The height HTML attribute of the <canvas> element is a positive integer reflecting the number of logical pixels (or RGBA values) going down one column of the canvas. When the attribute is not specified, or if it is set to an invalid value, like a negative, the default value of 150 is used. If no [separate] CSS height is assigned to the <canvas>, then this value will also be used as the height of the canvas in the length-unit CSS Pixel.
 * @property int $width The width HTML attribute of the <canvas> element is a positive integer reflecting the number of logical pixels (or RGBA values) going across one row of the canvas. When the attribute is not specified, or if it is set to an invalid value, like a negative, the default value of 300 is used. If no [separate] CSS width is assigned to the <canvas>, then this value will also be used as the width of the canvas in the length-unit CSS Pixel.
 * @property-read HTMLOptionsCollection $options Is a HTMLCollection representing a collection of the contained option elements.
 * @property bool $open Is a boolean reflecting the open HTML attribute, indicating whether the element’s contents (not counting the <summary>) is to be shown to the user.
 * @property string $returnValue A DOMString that sets or returns the return value for the dialog.
 * @property string $accessKey Is a DOMString representing the access key assigned to the element.
 * @property-read string $accessKeyLabel Returns a DOMString containing the element's assigned access key.
 * @property string $contentEditable Is a DOMString, where a value of true means the element is editable and a value of false means it isn't.
 * @property-read bool $isContentEditable Returns a Boolean that indicates whether the content of the element can be edited.
 * @property string $dir Is a DOMString, reflecting the dir global attribute, representing the directionality of the element. Possible values are "ltr", "rtl", and "auto".
 * @property bool $draggable Is a Boolean indicating if the element can be dragged.
 * @property string $enterKeyHint Is a DOMString defining what action label (or icon) to present for the enter key on virtual keyboards.
 * @property bool $hidden Is a Boolean indicating if the element is hidden or not.
 * @property bool $inert Is a Boolean indicating whether the user agent must act as though the given node is absent for the purposes of user interaction events, in-page text searches ("find in page"), and text selection.
 * @property string $innerText Represents the "rendered" text content of a node and its descendants. As a getter, it approximates the text the user would get if they highlighted the contents of the element with the cursor and then copied it to the clipboard.
 * @property string $lang Is a DOMString representing the language of an element's attributes, text, and element contents.
 * @property int $tabIndex Is a long that represents this element's position in the tabbing order.
 * @property string $title Is a DOMString containing the text that appears in a popup box when mouse is over the element.
 * @property CSSStyleDeclaration $style Is a CSSStyleDeclaration, an object representing the declarations of an element's style attributes.
 * @property-read HTMLCollection|HTMLFormControlsCollection $elements The elements belonging to this parent.
 * @property-read int $length A long reflecting the number of controls in the form.
 * @property string $method A DOMString reflecting the value of the form's method HTML attribute, indicating the HTTP method used to submit the form. Only specified values can be set.
 * @property string $action A DOMString reflecting the value of the form's action HTML attribute, containing the URI of a program that processes the information submitted by the form.
 * @property string $encoding A DOMString reflecting the value of the form's enctype HTML attribute, indicating the type of content that is used to transmit the form to the server. Only specified values can be set. The two properties are synonyms.
 * @property string $enctype A DOMString reflecting the value of the form's enctype HTML attribute, indicating the type of content that is used to transmit the form to the server. Only specified values can be set. The two properties are synonyms.
 * @property string $acceptCharset A DOMString reflecting the value of the form's accept-charset HTML attribute, representing the character encoding that the server accepts.
 * @property string $autocomplete A DOMString reflecting the value of the form's autocomplete HTML attribute, indicating whether the controls in this form can have their values automatically populated by the browser.
 * @property bool $noValidate A Boolean reflecting the value of the form's novalidate HTML attribute, indicating whether the form should not be validated.
 * @property string $autocapitalize Returns / Sets the element's capitalization behavior for user input. Valid values are: none, off, characters, words, sentences.
 * @property int $cols Returns / Sets the element's cols attribute, indicating the visible width of the text area.
 * @property string $defaultValue Returns / Sets the control's default value, which behaves like the Node.textContent property.
 * @property int $maxLength Returns / Sets the element's maxlength attribute, indicating the maximum number of characters the user can enter. This constraint is evaluated only when the value changes.
 * @property int $minLength Returns / Sets the element's minlength attribute, indicating the minimum number of characters the user can enter. This constraint is evaluated only when the value changes.
 * @property int|HTMLCollection $rows Returns / Sets the element's rows attribute, indicating the number of visible text lines for the control, or if a table, returns an HTMLCollection of all <tr> elements.
 * @property string $wrap Returns / Sets the wrap HTML attribute, indicating how the control wraps text.
 * @property-read Document $contentDocument Returns a Document, the active document in the inline frame's nested browsing context.
 * @property-read Node $contentWindow Returns a WindowProxy, the window proxy for the nested browsing context.
 * @property string $srcdoc Is a DOMString that represents the content to display in the frame.
 * @property-read bool $complete Returns a Boolean that is true if the browser has finished fetching the image, whether successful or not. That means this value is also true if the image has no src value indicating an image to load.
 * @property string $decoding An optional DOMString representing a hint given to the browser on how it should decode the image. If this value is provided, it must be one of the possible permitted values: sync to decode the image synchronously, async to decode it asynchronously, or auto to indicate no preference (which is the default). Read the decoding page for details on the implications of this property's values.
 * @property bool $isMap A Boolean that reflects the ismap HTML attribute, indicating that the image is part of a server-side image map. This is different from a client-side image map, specified using an <img> element and a corresponding <map> which contains <area> elements indicating the clickable areas in the image. The image must be contained within an <a> element; see the ismap page for details.
 * @property string $loading A DOMString providing a hint to the browser used to optimize loading the document by determining whether to load the image immediately (eager) or on an as-needed basis (lazy).
 * @property-read int $naturalHeight Returns an integer value representing the intrinsic height of the image in CSS pixels, if it is available; else, it shows 0. This is the height the image would be if it were rendered at its natural full size.
 * @property-read int $naturalWidth An integer value representing the intrinsic width of the image in CSS pixels, if it is available; otherwise, it will show 0. This is the width the image would be if it were rendered at its natural full size.
 * @property string $sizes A DOMString reflecting the sizes HTML attribute. This string specifies a list of comma-separated conditional sizes for the image; that is, for a given viewport size, a particular image size is to be used. Read the documentation on the sizes page for details on the format of this string.
 * @property string $srcset A USVString reflecting the srcset HTML attribute. This specifies a list of candidate images, separated by commas (',', U+002C COMMA). Each candidate image is a URL followed by a space, followed by a specially-formatted string indicating the size of the image. The size may be specified either the width or a size multiple. Read the srcset page for specifics on the format of the size substring.
 * @property string $useMap A DOMString reflecting the usemap HTML attribute, containing the page-local URL of the <map> element describing the image map to use. The page-local URL is a pound (hash) symbol (#) followed by the ID of the <map> element, such as #my-map-element. The <map> in turn contains <area> elements indicating the clickable areas in the image.
 * @property-read int $x An integer indicating the horizontal offset of the left border edge of the image's CSS layout box relative to the origin of the <html> element's containing block.
 * @property-read int $y The integer vertical offset of the top border edge of the image's CSS layout box relative to the origin of the <html> element's containing block.
 * @property bool $defaultChecked Returns / Sets the default state of a radio button or checkbox as originally specified in HTML that created this object.
 * @property bool $indeterminate Returns whether the checkbox or radio button is in indeterminate state. For checkboxes, the effect is that the appearance of the checkbox is obscured/greyed in some way as to indicate its state is indeterminate (not checked but not unchecked). Does not affect the value of the checked attribute, and clicking the checkbox will set the value to false.
 * Properties that apply only to elements of type `file`:
 * @property string $accept Returns / Sets the element's accept attribute, containing comma-separated list of file types accepted by the server when type is file.
 * @property FileList $files Returns/accepts a FileList object, which contains a list of File objects representing the files selected for upload.
 * @property string $formAction Is a DOMString reflecting the URI of a resource that processes information submitted by the HTMLUIElement. If specified, this attribute overrides the action attribute of the <form> element that owns this element.
 * @property string $formEncType Is a DOMString reflecting the type of content that is used to submit the form to the server. If specified, this attribute overrides the enctype attribute of the <form> element that owns this element.
 * @property string $formMethod Is a DOMString reflecting the HTTP method that the browser uses to submit the form. If specified, this attribute overrides the method attribute of the <form> element that owns this element.
 * @property bool $formNoValidate Is a Boolean indicating that the form is not to be validated when it is submitted. If specified, this attribute overrides the novalidate attribute of the <form> element that owns this element.
 * @property string $formTarget Is a DOMString reflecting a name or keyword indicating where to display the response that is received after submitting the form. If specified, this attribute overrides the target attribute of the <form> element that owns this element.
 * @property string $max Returns / Sets the element's max attribute, containing the maximum (numeric or date-time) value for this item, which must not be less than its minimum (min attribute) value.
 * @property string $min Returns / Sets the element's min attribute, containing the minimum (numeric or date-time) value for this item, which must not be greater than its maximum (max attribute) value.
 * @property string $pattern Returns / Sets the element's pattern attribute, containing a regular expression that the control's value is checked against. Use the title attribute to describe the pattern to help the user. This attribute applies when the value of the type attribute is text, search, tel, url or email; otherwise it is ignored.
 * @property string $placeholder Returns / Sets the element's placeholder attribute, containing a hint to the user of what can be entered in the control. The placeholder text must not contain carriage returns or line-feeds. This attribute applies when the value of the type attribute is text, search, tel, url or email; otherwise it is ignored.
 * @property ?int $size Returns / Sets the element's size attribute, containing visual size of the control. This value is in pixels unless the value of type is text or password, in which case, it is an integer number of characters. Applies only when type is set to text, search, tel, url, email, or password; otherwise it is ignored.
 * @property bool $multiple Returns / Sets the element's multiple attribute, indicating whether more than one value is possible (e.g., multiple files).
 * @property string $step Returns / Sets the element's step attribute, which works with min and max to limit the increments at which a numeric or date-time value can be set. It can be the string any or a positive floating point number. If this is not set to any, the control accepts only values at multiples of the step value greater than the minimum.
 * @property ?DateTimeInterface $valueAsDate Returns / Sets the value of the element, interpreted as a date, or null if conversion is not possible.
 * @property int|float|null $valueAsNumber Returns the value of the element, interpreted as one of the following, in order: A time value, A number, NaN if conversion is impossible.
 * @property string $inputMode Provides a hint to browsers as to the type of virtual keyboard configuration to use when editing this element or its contents.
 * @property string $as Is a DOMString representing the type of content being loaded by the HTML link.
 * @property string $media Is a DOMString representing a list of one or more media formats to which the resource applies.
 * @property-read StyleSheet $sheet Returns the StyleSheet object associated with the given element, or null if there is none.
 * @property-read HTMLCollection $areas Is a live HTMLCollection representing the <area> elements associated to this <map>.
 * @property string|DocumentFragment $content Gets or sets the value of meta-data property, or returns the content of a <template> in a DocumentFragment.
 * @property string $httpEquiv Gets or sets the name of an HTTP response header to define for a document.
 * @property ?float $high A double representing the value of the high boundary, reflecting the high attribute.
 * @property ?float $low A double representing the value of the low boundary, reflecting the lowattribute.
 * @property ?float $optimum A double representing the optimum, reflecting the optimum attribute.
 * @property string $cite Is a DOMString reflecting the cite HTML attribute, containing a URI of a resource explaining the change.
 * @property string $dateTime Is a DOMString reflecting the datetime HTML attribute, containing a date-and-time string representing a timestamp for the change.
 * @property string $data Returns a DOMString that reflects the data HTML attribute, specifying the address of a resource's data.
 * @property bool $typeMustMatch Is a Boolean that reflects the typemustmatch HTML attribute, indicating if the resource specified by data must only be played if it matches the type attribute.
 * @property bool $reversed Is a Boolean value reflecting the reversed and defining if the numbering is descending, that is its value is true, or ascending (false).
 * @property int $start Is a long value reflecting the start and defining the value of the first number of the first element of the list.
 * @property string $label Is a DOMString representing the label for the group.
 * @property bool $defaultSelected Is a Boolean that contains the initial value of the selected HTML attribute, indicating whether the option is selected by default or not.
 * @property-read int $index Is a long representing the position of the option within the list of options it belongs to, in tree-order. If the option is not part of a list of options, like when it is part of the <datalist> element, the value is 0.
 * @property bool $selected Is a Boolean that indicates whether the option is currently selected.
 * @property-read DOMStringMap $dataset The dataset read-only property of the HTMLOrForeignElement mixin provides read/write access to custom data attributes (data-*) on elements.
 * @property-read float $position Returns a double value returning the result of dividing the current value (value) by the maximum value (max); if the progress bar is an indeterminate progress bar, it returns -1.
 * @property bool $async The async and defer attributes are Boolean attributes that control how the script should be executed. The defer and async attributes must not be specified if the src attribute is absent.
 * @property bool $defer The async and defer attributes are Boolean attributes that control how the script should be executed. The defer and async attributes must not be specified if the src attribute is absent.
 * @property bool $noModule Is a Boolean that if true, stops the script's execution in browsers that support ES2015 modules — used to run fallback scripts in older browsers that do not support JavaScript modules.
 * @property int $selectedIndex A long reflecting the index of the first selected <option> element. The value -1 indicates no element is selected.
 * @property-read HTMLCollection $selectedOptions An HTMLCollection representing the set of <option> elements that are selected.
 * @property string $abbr A DOMString which can be used on <th> elements (not on <td>), specifying an alternative label for the header cell. This alternate label can be used in other contexts, such as when describing the headers that apply to a data cell. This is used to offer a shorter term for use by screen readers in particular, and is a valuable accessibility tool. Usually the value of abbr is an abbreviation or acronym, but can be any text that's appropriate contextually.
 * @property-read int $cellIndex A long integer representing the cell's position in the cells collection of the <tr> the cell is contained within. If the cell doesn't belong to a <tr>, it returns -1.
 * @property int $colSpan An unsigned long integer indicating the number of columns this cell must span; this lets the cell occupy space across multiple columns of the table. It reflects the colspan attribute.
 * @property string $headers Is a DOMSettableTokenList describing a list of id of <th> elements that represents headers associated with the cell. It reflects the headers attribute.
 * @property int $rowSpan An unsigned long integer indicating the number of rows this cell must span; this lets a cell occupy space across multiple rows of the table. It reflects the rowspan attribute.
 * @property string $scope A DOMString indicating the scope of a <th> cell. Header cells can be configured, using the scope property, the apply to a specified row or column, or to the not-yet-scoped cells within the current row group (that is, the same ancestor <thead>, <tbody>, or <tfoot> element). If no value is specified for scope, the header is not associated directly with cells in this way.
 * @property int $span Is an unsigned long that reflects the span HTML attribute, indicating the number of columns to apply this object's attributes to. Must be a positive integer.
 * @property ?Element $caption Is a HTMLTableCaptionElement representing the first <caption> that is a child of the element, or null if none is found. When set, if the object doesn't represent a <caption>, a DOMException with the HierarchyRequestError name is thrown. If a correct object is given, it is inserted in the tree as the first child of this element and the first <caption> that is a child of this element is removed from the tree, if any.
 * @property ?Element $tHead Is a HTMLTableSectionElement representing the first <thead> that is a child of the element, or null if none is found. When set, if the object doesn't represent a <thead>, a DOMException with the HierarchyRequestError name is thrown. If a correct object is given, it is inserted in the tree immediately before the first element that is neither a <caption>, nor a <colgroup>, or as the last child if there is no such element, and the first <thead> that is a child of this element is removed from the tree, if any.
 * @property ?Element $tFoot Is a HTMLTableSectionElement representing the first <tfoot> that is a child of the element, or null if none is found. When set, if the object doesn't represent a <tfoot>, a DOMException with the HierarchyRequestError name is thrown. If a correct object is given, it is inserted in the tree immediately before the first element that is neither a <caption>, a <colgroup>, nor a <thead>, or as the last child if there is no such element, and the first <tfoot> that is a child of this element is removed from the tree, if any.
 * @property-read HTMLCollection $tBodies Returns a live HTMLCollection containing all the <tbody> of the element. The HTMLCollection is live and is automatically updated when the HTMLTableElement changes.
 * @property-read HTMLCollection $cells Returns a live HTMLCollection containing the cells in the row. The HTMLCollection is live and is automatically updated when cells are added or removed.
 * @property-read int $rowIndex Returns a long value which gives the logical position of the row within the entire table. If the row is not part of a table, returns -1.
 * @property-read int $sectionRowIndex Returns a long value which gives the logical position of the row within the table section it belongs to. If the row is not part of a section, returns -1.
 * @property string $kind Is a DOMString that reflects the kind HTML attribute, indicating how the text track is meant to be used. Possible values are: subtitles, captions, descriptions, chapters, or metadata.
 * @property string $srclang Is a DOMString that reflects the srclang HTML attribute, indicating the language of the text track data.
 * @property bool $default A Boolean reflecting the default  attribute, indicating that the track is to be enabled if the user's preferences do not indicate that another track would be more appropriate.
 * @property-read TextTrack $track Returns TextTrack is the track element's text track data.
 * @property string $poster Is a DOMString that reflects the poster HTML attribute, which specifies an image to show while no video data is available.
 * @property-read int $videoHeight Returns an unsigned integer value indicating the intrinsic height of the resource in CSS pixels, or 0 if no media is available yet.
 * @property-read int $videoWidth Returns an unsigned integer value indicating the intrinsic width of the resource in CSS pixels, or 0 if no media is available yet.
 */
trait HTMLElement {
	private function allowTypes(ElementType...$typeList):void {
		if(!in_array($this->elementType, $typeList)) {
			$debug = debug_backtrace(limit: 2);
			$function = $debug[1]["function"];
			$propName = substr($function, strlen("__prop_get_"));

			/** @var Element $object */
			$object = $debug[1]["object"];
			$actualType = $object->elementType->name;
			throw new IncorrectHTMLElementUsageException("Property '$propName' is not available on '$actualType'");
		}
	}

	public function __toString():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLUnknownElement,
		);

		if($this->elementType === ElementType::HTMLAnchorElement
		|| $this->elementType === ElementType::HTMLAreaElement) {
			return $this->href;
		}
		else {
			return "";
		}
	}

// ArrayAccess functions:
	public function offsetExists(mixed $offset):bool {
		$this->allowTypes(ElementType::HTMLFormElement);
		$match = $this->elements->namedItem($offset);
		return !is_null($match);
	}

	public function offsetGet(mixed $offset):null|Element|RadioNodeList {
		$this->allowTypes(ElementType::HTMLFormElement);
		return $this->elements->namedItem($offset);
	}

	public function offsetSet(mixed $offset, mixed $value):void {
		$this->allowTypes(ElementType::HTMLFormElement);
		throw new ArrayAccessReadOnlyException();
	}

	public function offsetUnset(mixed $offset):void {
		$this->allowTypes(ElementType::HTMLFormElement);
		throw new ArrayAccessReadOnlyException();
	}
// End ArrayAccess functions.

// Countable functions:
	public function count():int {
		return $this->length;
	}
// End Countable functions.

	/**
	 * Builds and returns a URL string from the existing href attribute
	 * value with the newly supplied overrides.
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity
	private function buildUrl(
		?string $scheme = null,
		?string $user = null,
		?string $pass = null,
		?string $host = null,
		?int $port = null,
		?string $path = null,
		?string $query = null,
		?string $fragment = null,
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

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/accessKey */
	protected function __prop_get_accessKey():string {
		return $this->getAttribute("accesskey") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/accessKey */
	protected function __prop_set_accessKey(string $value):void {
		$this->setAttribute("accesskey", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/accessKeyLabel */
	protected function __prop_get_accessKeyLabel():string {
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/contentEditable */
	protected function __prop_get_contentEditable():string {
		$attr = $this->getAttribute("contenteditable");
		return $attr ?: "inherit";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/contentEditable */
	protected function __prop_set_contentEditable(string $value):void {
		switch($value) {
		case "true":
		case "false":
		case "inherit":
			$this->setAttribute("contenteditable", $value);
			break;
		default:
			throw new EnumeratedValueException($value);
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOrForeignElement/dataset */
	protected function __prop_get_dataset():DOMStringMap {
		return DOMStringMapFactory::createDataset($this);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/isContentEditable */
	protected function __prop_get_isContentEditable():bool {
		$attr = $this->getAttribute("contenteditable");
		if(!$attr || $attr === "false") {
			return false;
		}

		if($attr === "true") {
			return true;
		}

		$context = $this;
		while($parent = $context->parentElement) {
			$parentAttr = $parent->getAttribute("contenteditable");
			if($parentAttr === "true") {
				return true;
			}
			if($parentAttr === "false") {
				return false;
			}

			$context = $parent;
		}

		return false;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/dir */
	protected function __prop_get_dir():string {
		return $this->getAttribute("dir") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/dir */
	protected function __prop_set_dir(string $value):void {
		$this->setAttribute("dir", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/draggable */
	protected function __prop_get_draggable():bool {
		$attr = $this->getAttribute("draggable");
		return !is_null($attr) && $attr !== "false";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/draggable */
	protected function __prop_set_draggable(bool $value):void {
		$strValue = $value ? "true" : "false";
		$this->setAttribute("draggable", $strValue);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/enterKeyHint */
	protected function __prop_get_enterKeyHint():string {
		return $this->getAttribute("enterkeyhint") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/enterKeyHint */
	protected function __prop_set_enterKeyHint(string $value):void {
		switch($value) {
		case "enter":
		case "done":
		case "go":
		case "next":
		case "previous":
		case "search":
		case "send":
			$this->setAttribute("enterkeyhint", $value);
			break;

		default:
			throw new EnumeratedValueException($value);
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/hidden */
	protected function __prop_get_hidden():bool {
		return $this->hasAttribute("hidden");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/hidden */
	protected function __prop_set_hidden(bool $value):void {
		if($value) {
			$this->setAttribute("hidden", "");
		}
		else {
			$this->removeAttribute("hidden");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/inert */
	protected function __prop_get_inert():bool {
		return $this->hasAttribute("inert");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/inert */
	protected function __prop_set_inert(bool $value):void {
		if($value) {
			$this->setAttribute("inert", "");
		}
		else {
			$this->removeAttribute("inert");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/innerText */
	protected function __prop_get_innerText():string {
		$treeWalker = $this->ownerDocument->createTreeWalker(
			$this,
			NodeFilter::SHOW_TEXT
		);

		$textArray = [];

		foreach($treeWalker as $i => $node) {
			if($i === 0) {
				// Skip the root node.
				continue;
			}

			/** @var null|Element $parentElement */
			$parentElement = $node->parentNode;
			$closestHidden = $parentElement?->closest("[hidden]");
			if($parentElement
			&& $closestHidden) {
				continue;
			}

			array_push($textArray, $node->textContent);
		}

		return implode("", $textArray);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/innerText */
	protected function __prop_set_innerText(string $value):void {
		$this->textContent = $value;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/lang */
	protected function __prop_get_lang():string {
		return $this->getAttribute("lang") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/lang */
	protected function __prop_set_lang(string $value):void {
		$this->setAttribute("lang", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/title */
	protected function __prop_get_title():string {
		return $this->getAttribute("title") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/title */
	protected function __prop_set_title(string $value):void {
		$this->setAttribute("title", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOrForeignElement/tabIndex */
	protected function __prop_get_tabIndex():int {
		if($this->hasAttribute("tabindex")) {
			return (int)$this->getAttribute("tabindex");
		}

		return -1;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOrForeignElement/tabIndex */
	protected function __prop_set_tabIndex(int $tabIndex):void {
		$this->setAttribute("tabindex", (string)$tabIndex);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/style */
	protected function __prop_get_style():CSSStyleDeclaration {
		return new CSSStyleDeclaration();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/style */
	protected function __prop_set_style(CSSStyleDeclaration $value):void {
		throw new ClientSideOnlyFunctionalityException();
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hreflang
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/hreflang
	 */
	protected function __prop_get_hreflang():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLLinkElement,
		);
		return $this->getAttribute("hreflang") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/hreflang
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/hreflang
	 */
	protected function __prop_set_hreflang(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLLinkElement,
		);
		$this->setAttribute("hreflang", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/text
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/text
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/text
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTitleElement/text
	 */
	protected function __prop_get_text():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLTitleElement,
		);
		return $this->textContent;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/text
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/text
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/text
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTitleElement/text
	 */
	protected function __prop_set_text(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLTitleElement,
		);
		$this->textContent = $value;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/type
	 */
	protected function __prop_get_type():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLFieldSetElement,
			ElementType::HTMLLinkElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLOListElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLSourceElement,
			ElementType::HTMLStyleElement,
		);

		if($this->elementType === ElementType::HTMLFieldSetElement) {
			return "fieldset";
		}

		return $this->getAttribute("type") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement/type
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/type
	 */
	protected function __prop_set_type(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLFieldSetElement,
			ElementType::HTMLLinkElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLOListElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLSourceElement,
			ElementType::HTMLStyleElement,
		);
		$this->setAttribute("type", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLParamElement/name
	 */
	protected function __prop_get_name():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLFormElement,
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLMapElement,
			ElementType::HTMLMetaElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLParamElement,
		);
		return $this->getAttribute("name") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/name
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLParamElement/name
	 */
	protected function __prop_set_name(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLFormElement,
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLMapElement,
			ElementType::HTMLMetaElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLParamElement,
		);
		$this->setAttribute("name", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLiElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLParamElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLProgressElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/value
	 */
	protected function __prop_get_value():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLDataElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLLiElement,
			ElementType::HTMLMeterElement,
			ElementType::HTMLOutputElement,
			ElementType::HTMLParamElement,
			ElementType::HTMLProgressElement,
			ElementType::HTMLSelectElement,
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
		elseif($this->elementType === ElementType::HTMLTextAreaElement) {
			return $this->nodeValue;
		}

		return $this->textContent;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLiElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLParamElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLProgressElement/value
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/value
	 */
	protected function __prop_set_value(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLButtonElement,
			ElementType::HTMLDataElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLLiElement,
			ElementType::HTMLMeterElement,
			ElementType::HTMLOutputElement,
			ElementType::HTMLParamElement,
			ElementType::HTMLProgressElement,
			ElementType::HTMLSelectElement,
		);

		if($this->elementType === ElementType::HTMLSelectElement) {
			foreach($this->options as $option) {
				if($option->value === $value) {
					$option->selected = true;
				}
				else {
					$option->selected = false;
				}
			}
		}
		elseif($this->elementType === ElementType::HTMLTextAreaElement) {
			$this->nodeValue = $value;
		}
		else {
			$this->setAttribute("value", $value);
		}
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
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/href
	 */
	protected function __prop_get_href():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
			ElementType::HTMLLinkElement,
		);
		return $this->getAttribute("href") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement/href
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/href
	 */
	protected function __prop_set_href(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
			ElementType::HTMLLinkElement,
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
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/referrerPolicy
	 */
	protected function __prop_get_referrerPolicy():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLLinkElement,
			ElementType::HTMLScriptElement,
		);
		return $this->getAttribute("referrerpolicy") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/referrerPolicy
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/referrerPolicy
	 */
	protected function __prop_set_referrerPolicy(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLLinkElement,
			ElementType::HTMLScriptElement,
		);
		$this->setAttribute("referrerpolicy", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/rel
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/rel
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/rel
	 */
	protected function __prop_get_rel():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLLinkElement,
		);
		return $this->getAttribute("rel") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/rel
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/rel
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/rel
	 */
	protected function __prop_set_rel(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLLinkElement,
		);
		$this->setAttribute("rel", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/relList
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/relList
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/relList
	 */
	protected function __prop_get_relList():DOMTokenList {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLLinkElement,
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
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/target
	 */
	protected function __prop_get_target():string {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
			ElementType::HTMLFormElement,
		);
		return $this->getAttribute("target") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement/target
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/target
	 */
	protected function __prop_set_target(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAnchorElement,
			ElementType::HTMLAreaElement,
			ElementType::HTMLBaseElement,
			ElementType::HTMLFormElement,
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

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/alt
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/alt
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/alt
	 */
	protected function __prop_get_alt():string {
		$this->allowTypes(
			ElementType::HTMLAreaElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
		);
		return $this->getAttribute("alt") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement/alt
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/alt
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/alt
	 */
	protected function __prop_set_alt(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAreaElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
		);
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

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/audioTracks
	 */
	protected function __prop_get_audioTracks():AudioTrackList {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new AudioTrackList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/autoplay */
	protected function __prop_get_autoplay():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return $this->hasAttribute("autoplay");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/autplay */
	protected function __prop_set_autoplay(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		if($value) {
			$this->setAttribute("autoplay", "");
		}
		else {
			$this->removeAttribute("autoplay");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/buffered */
	protected function __prop_get_buffered():TimeRanges {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new TimeRanges();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controller */
	protected function __prop_get_controller():?MediaController {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controls */
	protected function __prop_get_controls():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return $this->hasAttribute("controls");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controls */
	protected function __prop_set_controls(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		if($value) {
			$this->setAttribute("controls", "");
		}
		else {
			$this->removeAttribute("controls");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/controlsList */
	protected function __prop_get_controlsList():DOMTokenList {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return DOMTokenListFactory::create(
			fn() => explode(
				" ",
				$this->getAttribute("controlsList") ?? ""
			),
			fn(string...$tokens) => $this->setAttribute(
				"controlsList",
				implode(" ", $tokens)
			)
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/crossOrigin
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/crossOrigin
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/crossOrigin
	 */
	protected function __prop_get_crossOrigin():string {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLVideoElement,
		);
		return $this->getAttribute("crossorigin") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/crossOrigin
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/crossOrigin
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/crossOrigin
	 */
	protected function __prop_set_crossOrigin(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("crossorigin", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentSrc
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/currentSrc
	 */
	protected function __prop_get_currentSrc():string {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLVideoElement,
		);
		return "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentTime */
	protected function __prop_get_currentTime():float {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentTime */
	protected function __prop_set_currentTime(float $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultMuted */
	protected function __prop_get_defaultMuted():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return $this->hasAttribute("muted");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultMuted */
	protected function __prop_set_defaultMuted(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		if($value) {
			$this->setAttribute("muted", "");
		}
		else {
			$this->removeAttribute("muted");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultPlaybackRate */
	protected function __prop_get_defaultPlaybackRate():float {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/defaultPlaybackRate */
	protected function __prop_set_defaultPlaybackRate(float $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/disableRemotePlayback */
	protected function __prop_get_disableRemotePlayback():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/disableRemotePlayback */
	protected function __prop_set_disableRemotePlayback(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/duration */
	protected function __prop_get_duration():?float {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/ended */
	protected function __prop_get_ended():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return false;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/error */
	protected function __prop_get_error():?MediaError {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/loop */
	protected function __prop_get_loop():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return $this->hasAttribute("loop");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/loop */
	protected function __prop_set_loop(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		if($value) {
			$this->setAttribute("loop", "");
		}
		else {
			$this->removeAttribute("loop");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/mediaGroup */
	protected function __prop_get_mediaGroup():string {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return $this->getAttribute("mediagroup") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/mediaGroup */
	protected function __prop_set_mediaGroup(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("mediagroup", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/muted */
	protected function __prop_get_muted():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException("Use defaultMuted for server-side use");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/muted */
	protected function __prop_set_muted(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException("Use defaultMuted for server-side use");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/networkState */
	protected function __prop_get_networkState():int {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/paused */
	protected function __prop_get_paused():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/playbackRate */
	protected function __prop_get_playbackRate():float {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/playbackRate */
	protected function __prop_set_playbackRate(float $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/played */
	protected function __prop_get_played():TimeRanges {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new TimeRanges();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/preload */
	protected function __prop_get_preload():string {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return $this->getAttribute("preload") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/preload */
	protected function __prop_set_preload(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("preload", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/readyState
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTrackElement/readyState
	 */
	protected function __prop_get_readyState():int {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLTrackElement,
			ElementType::HTMLVideoElement,
		);
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/seekable */
	protected function __prop_get_seekable():TimeRanges {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new TimeRanges();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/seeking */
	protected function __prop_get_seeking():bool {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return false;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/sinkId */
	protected function __prop_get_sinkId():string {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTrackElement/src
	 */
	protected function __prop_get_src():string {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLSourceElement,
			ElementType::HTMLTrackElement,
			ElementType::HTMLVideoElement,
		);
		return $this->getAttribute("src") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement/src
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTrackElement/src
	 */
	protected function __prop_set_src(string $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLScriptElement,
			ElementType::HTMLSourceElement,
			ElementType::HTMLTrackElement,
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("src", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/srcObject */
	protected function __prop_get_srcObject():MediaStream {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new MediaStream();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/srcObject */
	protected function __prop_set_srcObject(MediaStream $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/textTracks */
	protected function __prop_get_textTracks():TextTrackList {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new TextTrackList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/videoTracks */
	protected function __prop_get_videoTracks():VideoTrackList {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		return new VideoTrackList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/volume */
	protected function __prop_get_volume():float {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/volume */
	protected function __prop_set_volume(float $value):void {
		$this->allowTypes(
			ElementType::HTMLAudioElement,
			ElementType::HTMLVideoElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/autofocus */
	protected function __prop_get_autofocus():bool {
		$this->allowTypes(ElementType::HTMLButtonElement);
		return $this->hasAttribute("autofocus");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/autofocus */
	protected function __prop_set_autofocus(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLInputElement,
		);
		if($value) {
			$this->setAttribute("autofocus", "");
		}
		else {
			$this->removeAttribute("autofocus");
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptGroupElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElemnet/disabled
	 */
	protected function __prop_get_disabled():bool {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLFieldSetElement,
			ElementType::HTMLLinkElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLOptGroupElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLStyleElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
		);
		return $this->hasAttribute("disabled");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptGroupElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElemnet/disabled
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElemnet/disabled
	 */
	protected function __prop_set_disabled(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLFieldSetElement,
			ElementType::HTMLLinkElement,
			ElementType::HTMLOptGroupElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLStyleElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
		);
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
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLegendElement/form
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement/form
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/form
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/form
	 */
	protected function __prop_get_form():?Element {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLLabelElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLLegendElement,
			ElementType::HTMLFieldSetElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLOptionElement,
		);
		$context = $this;
		while($context->parentElement) {
			$context = $context->parentElement;

			if($this->elementType === ElementType::HTMLLegendElement) {
				if($context->elementType === ElementType::HTMLFieldSetElement) {
					return $context->form;
				}
			}
			else {
				if($context->elementType === ElementType::HTMLFormElement) {
					return $context;
				}
			}
		}

		if($this->elementType === ElementType::HTMLLabelElement) {
			if($input = $this->control) {
				return $input->form;
			}
		}

		return null;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/readOnly
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/readOnly
	 */
	protected function __prop_get_readOnly():bool {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLInputElement,
		);
		return $this->hasAttribute("readonly");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/readOnly
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/readOnly
	 */
	protected function __prop_set_readOnly(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLInputElement,
		);
		if($value) {
			$this->setAttribute("readonly", "");
		}
		else {
			$this->removeAttribute("readonly");
		}
	}

	protected function __prop_get_required():bool {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
			ElementType::HTMLTextAreaElement,
		);
		return $this->hasAttribute("required");
	}

	protected function __prop_set_required(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
			ElementType::HTMLTextAreaElement,
		);
		if($value) {
			$this->setAttribute("required", "");
		}
		else {
			$this->removeAttribute("required");
		}
	}

	protected function __prop_get_willValidate():bool {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLInputElement,
		);
		return false;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/validationMessage
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/validationMessage
	 */
	protected function __prop_get_validationMessage():string {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLObjectElement,
		);
		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/validity
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/validity
	 */
	protected function __prop_get_validity():ValidityState {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLObjectElement,
		);
		return new ValidityState();
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement/labels
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/labels
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/labels
	 */
	protected function __prop_get_labels():NodeList {
		$this->allowTypes(
			ElementType::HTMLButtonElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLMeterElement,
		);
		$input = $this;
		return NodeListFactory::createLive(function() use($input) {
			$labelsArray = [];

			$context = $input;
			while($context = $context->parentElement) {
				if($context->elementType === ElementType::HTMLLabelElement) {
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

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement/htmlFor
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOutputElement/htmlFor
	 */
	protected function __prop_get_htmlFor():string|DOMTokenList {
		$this->allowTypes(
			ElementType::HTMLLabelElement,
			ElementType::HTMLOutputElement,
		);

		if($this->elementType === ElementType::HTMLOutputElement) {
			return DOMTokenListFactory::create(
				fn() => explode(" ", $this->getAttribute("for") ?? ""),
				fn(string...$forValues) => $this->setAttribute("for", implode(" ", $forValues)),
			);
		}

		return $this->getAttribute("for") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement/htmlFor
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOutputElement/htmlFor
	 */
	protected function __prop_set_htmlFor(string $value):void {
		$this->allowTypes(
			ElementType::HTMLLabelElement,
			ElementType::HTMLOutputElement,
		);
		$this->setAttribute("for", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/height
	 */
	protected function __prop_get_height():int {
		$this->allowTypes(
			ElementType::HTMLCanvasElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLVideoElement,
		);
		return (int)$this->getAttribute("height");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/height
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/height
	 */
	protected function __prop_set_height(int $value):void {
		$this->allowTypes(
			ElementType::HTMLCanvasElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("height", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/width
	 */
	protected function __prop_get_width():int {
		$this->allowTypes(
			ElementType::HTMLCanvasElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLVideoElement,
		);
		return (int)$this->getAttribute("width");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/width
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/width
	 */
	protected function __prop_set_width(int $value):void {
		$this->allowTypes(
			ElementType::HTMLCanvasElement,
			ElementType::HTMLEmbedElement,
			ElementType::HTMLIFrameElement,
			ElementType::HTMLImageElement,
			ElementType::HTMLInputElement,
			ElementType::HTMLObjectElement,
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("width", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataListElement/options
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/options
	 */
	protected function __prop_get_options():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLDataListElement,
			ElementType::HTMLSelectElement,
		);
		if($this->elementType === ElementType::HTMLSelectElement) {
			return $this->getSelectOptionsCollection();
		}

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
			$this->setAttribute("open", "");
		}
		else {
			$this->removeAttribute("open");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/returnValue */
	protected function __prop_get_returnValue():string {
		$this->allowTypes(ElementType::HTMLDialogElement);
		throw new ClientSideOnlyFunctionalityException("returnValue");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/returnValue */
	protected function __prop_set_returnValue(string $value):void {
		$this->allowTypes(ElementType::HTMLDialogElement);
		throw new ClientSideOnlyFunctionalityException("returnValue");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement/elements
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/elements
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/elements
	 */
	protected function __prop_get_elements():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLFieldSetElement,
			ElementType::HTMLFormElement,
			ElementType::HTMLSelectElement,
		);

		if($this->elementType === ElementType::HTMLSelectElement) {
			return $this->getSelectOptionsCollection();
		}

		return HTMLCollectionFactory::createHTMLFormControlsCollection(
// List of elements from: https://html.spec.whatwg.org/multipage/forms.html#category-listed
			fn() => $this->querySelectorAll("button, fieldset, input, object, output, select, textarea, [name], [disabled]")
		);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/length */
	protected function __prop_get_length():int {
		return count($this->elements);
	}

	private function getSelectOptionsCollection():HTMLOptionsCollection {
		return HTMLCollectionFactory::createHTMLOptionsCollection(
			fn() => $this->querySelectorAll("option")
		);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/method */
	protected function __prop_get_method():string {
		return $this->getAttribute("method") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/method */
	protected function __prop_set_method(string $value):void {
		$this->setAttribute("method", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/action */
	protected function __prop_get_action():string {
		return $this->getAttribute("action") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/action */
	protected function __prop_set_action(string $value):void {
		$this->setAttribute("action", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/encoding */
	protected function __prop_get_encoding():string {
		return $this->getAttribute("enctype") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/encoding */
	protected function __prop_set_encoding(string $value):void {
		$this->setAttribute("enctype", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/enctype */
	protected function __prop_get_enctype():string {
		return $this->getAttribute("enctype") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/enctype */
	protected function __prop_set_enctype(string $value):void {
		$this->setAttribute("enctype", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/acceptCharset */
	protected function __prop_get_acceptCharset():string {
		return $this->getAttribute("accept-charset") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/acceptCharset */
	protected function __prop_set_acceptCharset(string $value):void {
		$this->setAttribute("accept-charset", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/autocomplete */
	protected function __prop_get_autocomplete():string {
		return $this->getAttribute("autocomplete") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/autocomplete */
	protected function __prop_set_autocomplete(string $value):void {
		$this->setAttribute("autocomplete", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/noValidate */
	protected function __prop_get_noValidate():bool {
		return $this->hasAttribute("novalidate");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/noValidate */
	protected function __prop_set_noValidate(bool $value):void {
		if($value) {
			$this->setAttribute("novalidate", "");
		}
		else {
			$this->removeAttribute("novalidate");
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/autocapitalize
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/autocapitalize
	 */
	protected function __prop_get_autocapitalize():string {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLInputElement,
		);
		return $this->getAttribute("autocapitalize") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/autocapitalize
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/autocapitalize
	 */
	protected function __prop_set_autocapitalize(string $value):void {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("autocapitalize", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/cols */
	protected function __prop_get_cols():int {
		$this->allowTypes(ElementType::HTMLTextAreaElement);
		if($this->hasAttribute("cols")) {
			return (int)$this->getAttribute("cols");
		}
		return 20;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/cols */
	protected function __prop_set_cols(int $value):void {
		$this->allowTypes(ElementType::HTMLTextAreaElement);
		$this->setAttribute("cols", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/defaultValue
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOutputElement/defaultValue
	 */
	protected function __prop_get_defaultValue():string {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLOutputElement,
		);
		return $this->value;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/defaultValue
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOutputElement/defaultValue
	 */
	protected function __prop_set_defaultValue(string $value):void {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLOutputElement,
		);
		$this->value = $value;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/maxLength
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/maxLength
	 */
	protected function __prop_get_maxLength():int {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLInputElement,
		);
		if($this->hasAttribute("maxlength")) {
			return (int)$this->getAttribute("maxlength");
		}
		return -1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/maxLength
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/maxLength
	 */
	protected function __prop_set_maxLength(int $value):void {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("maxlength", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/minLength
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/minLength
	 */
	protected function __prop_get_minLength():int {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLInputElement,
		);
		if($this->hasAttribute("minlength")) {
			return (int)$this->getAttribute("minlength");
		}
		return -1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/minLength
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/minLength
	 */
	protected function __prop_set_minLength(int $value):void {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("minlength", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/rows
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/rows
	 */
	protected function __prop_get_rows():int|HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLTextAreaElement,
			ElementType::HTMLTableElement,
			ElementType::HTMLTableSectionElement,
		);

		if($this->elementType === ElementType::HTMLTableElement
		|| $this->elementType === ElementType::HTMLTableSectionElement) {
			return HTMLCollectionFactory::create(function() {
				$rowsHead = [];
				$rowsBody = [];
				$rowsFoot = [];
				$rows = [];
				$trCollection = $this->getElementsByTagName('tr');
				/** @var Element $row */
				foreach($trCollection as $row) {
					$closestTable = $row->parentNode;
					while($closestTable->elementType !== $this->elementType) {
						$closestTable = $closestTable->parentNode;
					}
					if($closestTable !== $this) {
						continue;
					}

					switch(strtolower($row->parentNode->nodeName)) {
					case 'thead':
						array_push($rowsHead, $row);
						break;
					case 'table':
					case 'tbody':
						array_push($rowsBody, $row);
						break;
					case 'tfoot':
						array_push($rowsFoot, $row);
						break;
					}
				}

				array_push($rows, ...$rowsHead);
				array_push($rows, ...$rowsBody);
				array_push($rows, ...$rowsFoot);

				return NodeListFactory::create(...$rows);
			});
		}

		if($this->hasAttribute("rows")) {
			return (int)$this->getAttribute("rows");
		}
		return 2;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/rows */
	protected function __prop_set_rows(int $value):void {
		$this->allowTypes(ElementType::HTMLTextAreaElement);
		$this->setAttribute("rows", (string)$value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/wrap */
	protected function __prop_get_wrap():string {
		$this->allowTypes(ElementType::HTMLTextAreaElement);
		return $this->getAttribute("wrap") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement/wrap */
	protected function __prop_set_wrap(string $value):void {
		$this->allowTypes(ElementType::HTMLTextAreaElement);
		$this->setAttribute("wrap", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/contentDocument
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/contentDocument
	 */
	protected function __prop_get_contentDocument():Document {
		$this->allowTypes(
			ElementType::HTMLIFrameElement,
			ElementType::HTMLObjectElement,
		);
		throw new ClientSideOnlyFunctionalityException("contentDocument");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/contentWindow
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/contentWindow
	 */
	protected function __prop_get_contentWindow():Node {
		$this->allowTypes(
			ElementType::HTMLIFrameElement,
			ElementType::HTMLObjectElement,
		);
		throw new ClientSideOnlyFunctionalityException("contentWindow");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/srcdoc */
	protected function __prop_get_srcdoc():string {
		$this->allowTypes(ElementType::HTMLIFrameElement);
		return $this->getAttribute("srcdoc") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement/srcdoc */
	protected function __prop_set_srcdoc(string $value):void {
		$this->allowTypes(ElementType::HTMLIFrameElement);
		$this->setAttribute("srcdoc", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/complete */
	protected function __prop_get_complete():bool {
		return false;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/decoding */
	protected function __prop_get_decoding():string {
		return $this->getAttribute("decoding") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/decoding */
	protected function __prop_set_decoding(string $value):void {
		$this->setAttribute("decoding", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/isMap */
	protected function __prop_get_isMap():bool {
		return $this->hasAttribute("ismap");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/isMap */
	protected function __prop_set_isMap(bool $value):void {
		if($value) {
			$this->setAttribute("ismap", "");
		}
		else {
			$this->removeAttribute("ismap");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/loading */
	protected function __prop_get_loading():string {
		return $this->getAttribute("loading") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/loading */
	protected function __prop_set_loading(string $value):void {
		$this->setAttribute("loading", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/naturalHeight */
	protected function __prop_get_naturalHeight():int {
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/naturalWidth */
	protected function __prop_get_naturalWidth():int {
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/sizes */
	protected function __prop_get_sizes():string {
		return $this->getAttribute("sizes") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/sizes */
	protected function __prop_set_sizes(string $value):void {
		$this->setAttribute("sizes", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/srcset */
	protected function __prop_get_srcset():string {
		return $this->getAttribute("srcset") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/srcset */
	protected function __prop_set_srcset(string $value):void {
		$this->setAttribute("srcset", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/useMap */
	protected function __prop_get_useMap():string {
		return $this->getAttribute("usemap") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/useMap */
	protected function __prop_set_useMap(string $value):void {
		$this->setAttribute("usemap", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/x */
	protected function __prop_get_x():int {
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/y */
	protected function __prop_get_y():int {
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#defaultChecked */
	protected function __prop_get_defaultChecked():bool {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return $this->checked;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#defaultChecked */
	protected function __prop_set_defaultChecked(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->checked = $value;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#indeterminate */
	protected function __prop_get_indeterminate():bool {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		throw new ClientSideOnlyFunctionalityException("indeterminate");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#indeterminate */
	protected function __prop_set_indeterminate(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		throw new ClientSideOnlyFunctionalityException("indeterminate");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#accept */
	protected function __prop_get_accept():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return $this->getAttribute("accept") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#accept */
	protected function __prop_set_accept(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("accept", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#files */
	protected function __prop_get_files():FileList {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return new FileList();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#files */
	protected function __prop_set_files(FileList $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		throw new ClientSideOnlyFunctionalityException();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formaction */
	protected function __prop_get_formAction():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if($this->hasAttribute("formaction")) {
			return $this->getAttribute("formaction");
		}

		while($parent = $this->parentElement) {
			if($parent->elementType === ElementType::HTMLFormElement) {
				break;
			}
		}

		if(!$parent) {
			return "";
		}

		return $parent->getAttribute("action") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/formAction */
	protected function __prop_set_formAction(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("formaction", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype */
	protected function __prop_get_formEncType():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if($this->hasAttribute("formenctype")) {
			return $this->getAttribute("formenctype");
		}

		while($parent = $this->parentElement) {
			if($parent->elementType === ElementType::HTMLFormElement) {
				break;
			}
		}

		if(!$parent) {
			return "";
		}

		return $parent->getAttribute("enctype") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formenctype */
	protected function __prop_set_formEncType(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("formenctype", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod */
	protected function __prop_get_formMethod():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if($this->hasAttribute("formmethod")) {
			return $this->getAttribute("formmethod");
		}

		while($parent = $this->parentElement) {
			if($parent->elementType === ElementType::HTMLFormElement) {
				break;
			}
		}

		if(!$parent) {
			return "";
		}

		return $parent->getAttribute("method") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formmethod */
	protected function __prop_set_formMethod(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("formmethod", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate */
	protected function __prop_get_formNoValidate():bool {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return $this->hasAttribute("formnovalidate");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formnovalidate */
	protected function __prop_set_formNoValidate(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if($value) {
			$this->setAttribute("formnovalidate", "");
		}
		else {
			$this->removeAttribute("formnovalidate");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget */
	protected function __prop_get_formTarget():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if($this->hasAttribute("formtarget")) {
			return $this->getAttribute("formtarget");
		}

		while($parent = $this->parentElement) {
			if($parent->elementType === ElementType::HTMLFormElement) {
				break;
			}
		}

		if(!$parent) {
			return "";
		}

		return $parent->getAttribute("target") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-formtarget */
	protected function __prop_set_formTarget(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("formtarget", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-max
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/max
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLProgressElement/max
	 */
	protected function __prop_get_max():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLMeterElement,
			ElementType::HTMLProgressElement,
		);
		return $this->getAttribute("max") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-max
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/max
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLProgressElement/max
	 */
	protected function __prop_set_max(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLMeterElement,
			ElementType::HTMLProgressElement,
		);
		$this->setAttribute("max", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-min
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/min
	 */
	protected function __prop_get_min():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLMeterElement,
		);
		return $this->getAttribute("min") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-min
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/min
	 */
	protected function __prop_set_min(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLMeterElement,
		);
		$this->setAttribute("min", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-pattern */
	protected function __prop_get_pattern():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return $this->getAttribute("pattern") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-pattern */
	protected function __prop_set_pattern(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("pattern", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-placeholder */
	protected function __prop_get_placeholder():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLTextAreaElement,
		);
		return $this->getAttribute("placeholder") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-placeholder */
	protected function __prop_set_placeholder(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLTextAreaElement,
		);
		$this->setAttribute("placeholder", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-size
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/size
	 */
	protected function __prop_get_size():?int {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
		);
		if($this->hasAttribute("size")) {
			return (int)$this->getAttribute("size");
		}

		if($this->elementType === ElementType::HTMLSelectElement) {
			return $this->multiple ? 4 : 1;
		}

		return null;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-size
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/size
	 */
	protected function __prop_set_size(int $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
		);
		$this->setAttribute("size", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/multiple
	 */
	protected function __prop_get_multiple():bool {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
		);
		return $this->hasAttribute("multiple");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/multiple
	 */
	protected function __prop_set_multiple(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
			ElementType::HTMLSelectElement,
		);
		if($value) {
			$this->setAttribute("multiple", "");
		}
		else {
			$this->removeAttribute("multiple");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-step */
	protected function __prop_get_step():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return $this->getAttribute("step") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-step */
	protected function __prop_set_step(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("step", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#property-valueAsDate */
	protected function __prop_get_valueAsDate():?DateTimeInterface {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if(empty($this->value)) {
			return null;
		}

		if(is_numeric($this->value)) {
			$dateTime = new DateTimeImmutable();
			return $dateTime->setTimestamp((int)$this->value);
		}

		try {
			$dateTime = new DateTimeImmutable($this->value);
		}
		catch(Exception) {
			return null;
		}

		return $dateTime;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#property-valueAsDate */
	protected function __prop_set_valueAsDate(DateTimeInterface $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
// See here for why we're using this format:
// https://developer.mozilla.org/en-US/docs/Web/HTML/Date_and_time_formats#local_date_and_time_strings
		$this->value = $value->format("Y-m-d\TH:i:s");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#property-valueAsNumber */
	protected function __prop_get_valueAsNumber():int|float|null {
		if(str_starts_with($this->type, "date")) {
			$dateTime = $this->valueAsDate;
			return $dateTime?->getTimestamp();

		}

		if(is_numeric($this->value)) {
			return (float)$this->value;
		}

		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#property-valueAsNumber */
	protected function __prop_set_valueAsNumber(int|float $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		if(str_starts_with($this->type, "date")) {
			$dateTime = new DateTimeImmutable();
			$this->valueAsDate = $dateTime->setTimestamp($value);
		}
		else {
			$this->value = (string)$value;
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#attr-inputmode */
	protected function __prop_get_inputMode():string {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		return $this->getAttribute("inputmode") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement#attr-inputmode */
	protected function __prop_set_inputMode(string $value):void {
		$this->allowTypes(
			ElementType::HTMLInputElement,
		);
		$this->setAttribute("inputmode", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/as */
	protected function __prop_get_as():string {
		$this->allowTypes(
			ElementType::HTMLLinkElement,
		);
		return $this->getAttribute("as") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/as */
	protected function __prop_set_as(string $value):void {
		$this->allowTypes(
			ElementType::HTMLLinkElement,
		);
		$this->setAttribute("as", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/media
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement/media
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/media
	 */
	protected function __prop_get_media():string {
		$this->allowTypes(
			ElementType::HTMLLinkElement,
			ElementType::HTMLSourceElement,
			ElementType::HTMLStyleElement,
		);
		return $this->getAttribute("media") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/media
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement/media
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/media
	 */
	protected function __prop_set_media(string $value):void {
		$this->allowTypes(
			ElementType::HTMLLinkElement,
			ElementType::HTMLSourceElement,
			ElementType::HTMLStyleElement,
		);
		$this->setAttribute("media", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement/sheet
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement/sheet
	 */
	protected function __prop_get_sheet():StyleSheet {
		$this->allowTypes(
			ElementType::HTMLLinkElement,
			ElementType::HTMLStyleElement,
		);
		return new StyleSheet();
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/areas
	 */
	protected function __prop_get_areas():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLMapElement,
		);
		return HTMLCollectionFactory::create(
			fn() => $this->getElementsByTagName("area")
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/content
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement/content
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTemplateElement/content
	 */
	protected function __prop_get_content():string|DocumentFragment {
		$this->allowTypes(
			ElementType::HTMLMapElement,
			ElementType::HTMLMetaElement,
			ElementType::HTMLTemplateElement,
		);

		if($this->elementType === ElementType::HTMLTemplateElement) {
			$fragment = $this->ownerDocument->createDocumentFragment();
			foreach($this->childNodes as $childNode) {
				$fragment->appendChild($childNode->cloneNode(true));
			}

			return $fragment;
		}

		return $this->getAttribute("content") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/content
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement/content
	 */
	protected function __prop_set_content(string $value):void {
		$this->allowTypes(
			ElementType::HTMLMapElement,
			ElementType::HTMLMetaElement,
		);
		$this->setAttribute("content", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/httpEquiv
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement/httpEquiv
	 */
	protected function __prop_get_httpEquiv():string {
		$this->allowTypes(
			ElementType::HTMLMapElement,
			ElementType::HTMLMetaElement,
		);
		return $this->getAttribute("http-equiv") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement/httpEquiv
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement/httpEquiv
	 */
	protected function __prop_set_httpEquiv(string $value):void {
		$this->allowTypes(
			ElementType::HTMLMapElement,
			ElementType::HTMLMetaElement,
		);
		$this->setAttribute("http-equiv", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/high */
	protected function __prop_get_high():?float {
		$this->allowTypes(
			ElementType::HTMLMeterElement,
		);
		if($this->hasAttribute("high")) {
			return (float)$this->getAttribute("high");
		}

		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/high */
	protected function __prop_set_high(float $value):void {
		$this->allowTypes(
			ElementType::HTMLMeterElement,
		);
		$this->setAttribute("high", (string)$value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/low */
	protected function __prop_get_low():?float {
		$this->allowTypes(
			ElementType::HTMLMeterElement,
		);
		if($this->hasAttribute("low")) {
			return (float)$this->getAttribute("low");
		}

		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/low */
	protected function __prop_set_low(float $value):void {
		$this->allowTypes(
			ElementType::HTMLMeterElement,
		);
		$this->setAttribute("low", (string)$value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/optimum */
	protected function __prop_get_optimum():?float {
		$this->allowTypes(
			ElementType::HTMLMeterElement,
		);
		if($this->hasAttribute("optimum")) {
			return (float)$this->getAttribute("optimum");
		}

		return null;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement/optimum */
	protected function __prop_set_optimum(float $value):void {
		$this->allowTypes(
			ElementType::HTMLMeterElement,
		);
		$this->setAttribute("optimum", (string)$value);
	}

	/**
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLModElement/cite
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLQuoteElement/cite
	 */
	public function __prop_get_cite():string {
		$this->allowTypes(
			ElementType::HTMLModElement,
			ElementType::HTMLQuoteElement,
		);
		return $this->getAttribute("cite") ?? "";
	}

	/**
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLModElement/cite
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLQuoteElement/cite
	 */
	public function __prop_set_cite(string $value):void {
		$this->allowTypes(
			ElementType::HTMLModElement,
			ElementType::HTMLQuoteElement,
		);
		$this->setAttribute("cite", $value);
	}

	/**
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLModElement/dateTime
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLTimeElement/dateTime
	 */
	public function __prop_get_dateTime():string {
		$this->allowTypes(
			ElementType::HTMLModElement,
			ElementType::HTMLTimeElement,
		);
		return $this->getAttribute("datetime") ?? "";
	}

	/**
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLModElement/dateTime
	 * https://developer.mozilla.org/en-US/docs/Web/API/HTMLTimeElement/dateTime
	 */
	public function __prop_set_dateTime(string $value):void {
		$this->allowTypes(
			ElementType::HTMLModElement,
			ElementType::HTMLTimeElement,
		);
		$this->setAttribute("datetime", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/data */
	protected function __prop_get_data():string {
		$this->allowTypes(
			ElementType::HTMLObjectElement,
		);
		return $this->getAttribute("data") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/data */
	protected function __prop_set_data(string $value):void {
		$this->allowTypes(
			ElementType::HTMLObjectElement,
		);
		$this->setAttribute("data", $value);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/typeMustMatch */
	protected function __prop_get_typeMustMatch():bool {
		$this->allowTypes(
			ElementType::HTMLObjectElement,
		);
		return $this->hasAttribute("typemustmatch");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement/typeMustMatch */
	protected function __prop_set_typeMustMatch(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLObjectElement,
		);
		if($value) {
			$this->setAttribute("typemustmatch", "");
		}
		else {
			$this->removeAttribute("typemustmatch");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement/reversed */
	protected function __prop_get_reversed():bool {
		$this->allowTypes(
			ElementType::HTMLOListElement,
		);
		return $this->hasAttribute("reversed");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement/reversed */
	protected function __prop_set_reversed(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLOListElement,
		);
		if($value) {
			$this->setAttribute("reversed", "");
		}
		else {
			$this->removeAttribute("reversed");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement/start */
	protected function __prop_get_start():int {
		$this->allowTypes(
			ElementType::HTMLOListElement,
		);
		if($this->hasAttribute("start")) {
			return (int)$this->getAttribute("start");
		}

		return 1;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement/start */
	protected function __prop_set_start(int $value):void {
		$this->allowTypes(
			ElementType::HTMLOListElement,
		);
		$this->setAttribute("start", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptGroupElement/label
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/label
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTrackElement/label
	 */
	protected function __prop_get_label():string {
		$this->allowTypes(
			ElementType::HTMLOptGroupElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLTrackElement,
		);
		if($label = $this->getAttribute("label")) {
			return $label;
		}

		if($this->elementType === ElementType::HTMLOptionElement) {
			return $this->textContent;
		}

		return "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptGroupElement/label
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/label
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTrackElement/label
	 */
	protected function __prop_set_label(string $value):void {
		$this->allowTypes(
			ElementType::HTMLOptGroupElement,
			ElementType::HTMLOptionElement,
			ElementType::HTMLTrackElement,
		);
		$this->setAttribute("label", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/defaultSelected
	 */
	protected function __prop_get_defaultSelected():bool {
		$this->allowTypes(
			ElementType::HTMLOptionElement,
		);
		return $this->selected;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/defaultSelected
	 */
	protected function __prop_set_defaultSelected(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLOptionElement,
		);
		$this->selected = $value;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/index
	 */
	protected function __prop_get_index():int {
		$this->allowTypes(
			ElementType::HTMLOptionElement,
		);
		$parent = $this->parentElement;
		if($parent && $parent->elementType === ElementType::HTMLSelectElement) {
			foreach($parent->children as $i => $childElement) {
				if($childElement === $this) {
					return $i;
				}
			}
		}

		return 0;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/selected
	 */
	protected function __prop_get_selected():bool {
		$this->allowTypes(
			ElementType::HTMLOptionElement,
		);
		return $this->hasAttribute("selected");
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement/selected
	 */
	protected function __prop_set_selected(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLOptionElement,
		);
		if($value) {
			$context = $this;
			while($context = $context->parentElement) {
				if($context->elementType === ElementType::HTMLSelectElement
				&& !$context->multiple) {
					foreach($context->options as $option) {
						$option->removeAttribute("selected");
					}
				}
			}

			$this->setAttribute("selected", "");
		}
		else {
			$this->removeAttribute("selected");
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLProgressElement/position
	 */
	protected function __prop_get_position():float {
		$this->allowTypes(
			ElementType::HTMLProgressElement,
		);
		if(!$this->max) {
			return -1;
		}

		return min((float)$this->value / (float)$this->max, 1);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/async */
	protected function __prop_get_async():bool {
		$this->allowTypes(
			ElementType::HTMLScriptElement,
		);
		return $this->hasAttribute("async");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/async */
	protected function __prop_set_async(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLScriptElement,
		);
		if($value) {
			$this->setAttribute("async", "");
		}
		else {
			$this->removeAttribute("async");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/defer */
	protected function __prop_get_defer():bool {
		$this->allowTypes(
			ElementType::HTMLScriptElement,
		);
		return $this->hasAttribute("defer");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/defer */
	protected function __prop_set_defer(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLScriptElement,
		);
		if($value) {
			$this->setAttribute("defer", "");
		}
		else {
			$this->removeAttribute("defer");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/noModule */
	protected function __prop_get_noModule():bool {
		$this->allowTypes(
			ElementType::HTMLScriptElement,
		);
		return $this->hasAttribute("nomodule");
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement/noModule */
	protected function __prop_set_noModule(bool $value):void {
		$this->allowTypes(
			ElementType::HTMLScriptElement,
		);
		if($value) {
			$this->setAttribute("nomodule", "");
		}
		else {
			$this->removeAttribute("nomodule");
		}
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/selectedIndex */
	protected function __prop_get_selectedIndex():int {
		$this->allowTypes(
			ElementType::HTMLSelectElement,
		);
		foreach($this->options as $i => $option) {
			if(!$option->selected) {
				continue;
			}

			return $i;
		}

		return -1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/selectedIndex
	 */
	protected function __prop_set_selectedIndex(int $value):void {
		$this->allowTypes(
			ElementType::HTMLSelectElement,
		);
		foreach($this->options as $i => $option) {
			$option->selected = false;

			if($i === $value) {
				$option->selected = true;
			}
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement/selectedOptions
	 */
	protected function __prop_get_selectedOptions():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLSelectElement,
		);
		if($this->multiple) {
			return HTMLCollectionFactory::create(
				fn() => $this->querySelectorAll("option[selected]")
			);
		}
		else {
			return HTMLCollectionFactory::create(
				fn() => ($this->selectedIndex === -1)
					? []
					: [$this->options[$this->selectedIndex]]
			);
		}
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_get_abbr():string {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		return $this->getAttribute("abbr") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_set_abbr(string $value):void {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		$this->setAttribute("abbr", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_get_cellIndex():int {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		if($this->parentElement?->elementType !== ElementType::HTMLTableRowElement) {
			return -1;
		}

		$i = 0;
		foreach($this->parentElement->children as $i => $child) {
			if($child === $this) {
				break;
			}
		}

		return $i;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_get_colSpan():int {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		if($this->hasAttribute("colspan")) {
			return (int)$this->getAttribute("colspan");
		}

		return 1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_set_colSpan(int $value):void {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		$this->setAttribute("colspan", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_get_headers():string {
// Note that even though the documentation states DOMSettableTokenList, this
// function does indeed return a standard string, in all modern browsers.
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		return $this->getAttribute("headers") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	public function __prop_set_headers(string $value):void {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		$this->setAttribute("headers", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_get_rowSpan():int {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		if($this->hasAttribute("rowspan")) {
			return (int)$this->getAttribute("rowspan");
		}

		return 1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_set_rowSpan(int $value):void {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		$this->setAttribute("rowspan", (string)$value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_get_scope():string {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		return $this->getAttribute("scope") ?? "";
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement/abbr
	 */
	protected function __prop_set_scope(string $value):void {
		$this->allowTypes(
			ElementType::HTMLTableCellElement,
		);
		$this->setAttribute("scope", $value);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableColElement/abbr
	 */
	protected function __prop_get_span():int {
		$this->allowTypes(
			ElementType::HTMLTableColElement,
		);
		if($this->hasAttribute("span")) {
			return (int)$this->getAttribute("span");
		}

		return 1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableColElement/abbr
	 */
	protected function __prop_set_span(int $value):void {
		$this->allowTypes(
			ElementType::HTMLTableColElement,
		);
		$this->setAttribute("span", (string)$value);
	}

	protected function __prop_get_caption():?Element {
		/**
		 * The caption IDL attribute must return, on getting, the first <caption> element child
		 * of the <table> element, if any, or null otherwise.
		 * @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-caption
		 */
		return $this->hasChildFirst('caption');
	}

	protected function __prop_set_caption(?Element $value):void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		if($value && $value->elementType !== ElementType::HTMLTableCaptionElement) {
			throw new TypeError("Element::caption must be of type HTMLTableCaptionElement");
		}
		/**
		 * On setting, the first <caption> element child of the <table> element, if any, must be removed,
		 * and the new value, if not null, must be inserted as the first node of the <table> element.
		 * @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-caption
		 */
		$this->delChild('caption');
		$this->placeCaption($value);
	}

	protected function __prop_get_tHead():?Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		/**
		 * The tHead IDL attribute must return, on getting, the first <thead> element child of the <table> element,
		 * if any, or null otherwise.
		 * @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-thead
		 */
		return $this->hasChildFirst('thead');
	}

	protected function __prop_set_tHead(?Element $value):void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		if($value && $value->elementType !== ElementType::HTMLTableSectionElement) {
			throw new TypeError("Element::tHead must be of type HTMLTableSectionElement");
		}
		/**
		 * On setting, if the new value is null or a <thead> element,
		 * the first <thead> element child of the <table> element, if any, must be removed, and the new value,
		 * if not null, must be inserted immediately before the first element in the <table> element
		 * that is neither a <caption> element nor a <colgroup> element, if any,
		 * or at the end of the <table> if there are no such elements.
		 * If the new value is neither null nor a <thead> element, then a HierarchyRequestError DOM exception
		 * must be thrown instead.
		 * @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-thead
		 */
		if($value && $value->tagName !== "thead") {
			throw new HierarchyRequestError("Element::thead must be an HTMLTableSectionElement of type <thead>");
		}

		$this->delChild('thead');
		$this->placeThead($value);
	}

	protected function __prop_get_tFoot():?Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		/**
		 * The tFoot IDL attribute must return, on getting, the first tfoot element child of the table element,
		 * if any, or null otherwise.
		 * @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-tfoot
		 */
		return $this->hasChildFirst('tfoot');
	}

	protected function __prop_set_tFoot(?Element $value):void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		/**
		 * On setting, if the new value is null or a tfoot element, the first tfoot element child of the table element,
		 * if any, must be removed, and the new value, if not null, must be inserted at the end of the table.
		 * If the new value is neither null nor a tfoot element,
		 * then a HierarchyRequestError DOM exception must be thrown instead.
		 * @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-tfoot
		 */
		if($value !== null && strtolower($value->nodeName) !== 'tfoot') {
			throw new HierarchyRequestError();
		}
		$this->delChild('tfoot');
		$this->placeTFoot($value);
	}

	protected function __prop_get_tBodies():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		return HTMLCollectionFactory::create(function() {
			$tbodies = [];
			for($i = 0, $len = $this->childNodes->length; $i < $len; $i++) {
				$child = $this->childNodes->item($i);
				if($child !== null && strtolower($child->nodeName) === 'tbody') {
					array_push($tbodies, $child);
				}
			}

			return NodeListFactory::create(...$tbodies);
		});
	}

	/**
	 * Return existing child or create it first if it does not exist.
	 * If the child already exists it is simply returned. If not, it will be created first
	 * and inserted at the correct place before being returned.
	 * @param string $name element name
	 * @return Element
	 */
	private function getCreateChild(
		string $name
	):Element {
		$child = $this->hasChildFirst($name);
		if($child === null) {
			$child = $this->ownerDocument->createElement($name);
			$this->placeChild($name, $child);
		}

		return $child;
	}

	/**
	 * Remove the child element from the table.
	 * @param string $name element name
	 */
	private function delChild(string $name):void {
		$node = $this->hasChildFirst($name);
		if($node !== null) {
			$this->removeChild($node);
		}
	}

	/**
	 * Check if the table already has the specified child element.
	 * Returns the first occurrence of the child or null if child was not found.
	 * @param string $name element name
	 * @return null|Node|Element
	 */
	private function hasChildFirst(string $name):null|Node|Element {
		for($i = 0, $len = $this->childNodes->length; $i < $len; $i++) {
			$child = $this->childNodes->item($i);
			if($child !== null && strtolower($child->nodeName) === $name) {
				return $child;
			}
		}

		return null;
	}

	/**
	 * Check if the table already has the specified child element.
	 * Returns the last occurrence of the child or null if child was not found.
	 * @param string $name element name
	 * @return null|Node|Element
	 */
	private function hasChildLast(string $name):null|Node|Element {
		$lastChild = null;
		for($i = 0, $len = $this->childNodes->length; $i < $len; $i++) {
			$child = $this->childNodes->item($i);
			if($child !== null && strtolower($child->nodeName) === $name) {
				$lastChild = $child;
			}
		}

		return $lastChild;
	}

	/**
	 * Insert the section element after the specified nodes.
	 * @param Element $newNode
	 * @param string[] $refNames names of nodes to insert after
	 */
	private function tableInsertChildAfter(Element $newNode, array $refNames):void {
		$child = $this->firstElementChild;
		while($child && in_array($child->nodeName, $refNames, true)) {
			$child = $child->nextElementSibling;
		}
		$this->insertBefore($newNode, $child);
	}

	/**
	 * Place the child at the correct location.
	 * @param string $name
	 * @param ?Element $node
	 */
	private function placeChild(string $name, ?Element $node):void {
		switch($name) {
		case 'caption':
			$this->placeCaption($node);
			break;
		case 'thead':
			$this->placeThead($node);
			break;
		case 'tfoot':
			$this->placeTFoot($node);
			break;
		}
	}

	private function placeCaption(?Element $caption):void {
		if($caption !== null) {
			$this->insertBefore($caption, $this->firstChild);
		}
	}

	private function placeThead(?Element $thead):void {
		if($thead !== null) {
			$this->tableInsertChildAfter($thead, ['caption', 'colgroup']);
		}
	}

	private function placeTBody(?Element $tbody):void {
		if($tbody !== null) {
			$this->tableInsertChildAfter($tbody, ['caption', 'colgroup', 'thead', 'tbody']);
		}
	}

	private function placeTFoot(?Element $tfoot):void {
		if($tfoot !== null) {
			$this->appendChild($tfoot);
		}
	}

	/**
	 * Returns an HTMLTableSectionElement representing the first <thead>
	 * that is a child of the element. If none is found, a new one is
	 * created and inserted in the tree immediately before the first element
	 * that is neither a <caption>, nor a <colgroup>, or as the last child
	 * if there is no such element.
	 *
	 * Note: If no header exists, createTHead() inserts a new header
	 * directly into the table. The header does not need to be added
	 * separately as would be the case if Document.createElement() had been
	 * used to create the new <thead> element.
	 *
	 * @return Element
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/createTHead
	 */
	public function createTHead():Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		return $this->getCreateChild('thead');
	}

	/**
	 * The HTMLTableElement.deleteTHead() removes the <thead> element from a given <table>.
	 * The deleteTHead() method must remove the first thead element child of the table element, if any.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/deleteTHead
	 * @link https://html.spec.whatwg.org/multipage/tables.html#dom-table-deletethead
	 */
	public function deleteTHead():void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		$this->delChild('thead');
	}

	/**
	 * The createTFoot() method of HTMLTableElement objects returns the
	 * <tfoot> element associated with a given <table>. If no footer exists
	 * in the table, this method creates it, and then returns it.
	 *
	 * Note: If no footer exists, createTFoot() inserts a new footer
	 * directly into the table. The footer does not need to be added
	 * separately as would be the case if Document.createElement() had been
	 * used to create the new <tfoot> element.
	 *
	 * @return Element
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/createTFoot
	 *
	 * For the order of elements @see https://www.w3.org/TR/html51/tabular-data.html#tabular-data
	 */
	public function createTFoot():Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		return $this->getCreateChild('tfoot');
	}

	/**
	 * The HTMLTableElement.deleteTFoot() method removes the <tfoot> element
	 * from a given <table>.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/deleteTFoot
	 */
	public function deleteTFoot():void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		$this->delChild('tfoot');
	}

	/**
	 * The createTBody() method of HTMLTableElement objects creates and
	 * returns a new <tbody> element associated with a given <table>.
	 *
	 * Note: Unlike HTMLTableElement.createTHead() and
	 * HTMLTableElement.createTFoot(), createTBody() systematically creates
	 * a new <tbody> element, even if the table already contains one or more
	 * bodies. If so, the new one is inserted after the existing ones.
	 *
	 * @return Element
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/createTBody
	 * For the order of elements @see https://www.w3.org/TR/html51/tabular-data.html#tabular-data
	 */
	public function createTBody():Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		$tbody = $this->ownerDocument->createElement('tbody');
		$this->placeTBody($tbody);
		return $tbody;
	}

	/**
	 * The HTMLTableElement.createCaption() method returns the <caption>
	 * element associated with a given <table>. If no <caption> element
	 * exists on the table, this method creates it, and then returns it.
	 *
	 * Note: If no caption exists, createCaption() inserts a new caption
	 * directly into the table. The caption does not need to be added
	 * separately as would be the case if Document.createElement() had been
	 * used to create the new <caption> element.
	 *
	 * @return Element
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/createCaption
	 */
	public function createCaption():Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		return $this->getCreateChild('caption');
	}

	/**
	 * The HTMLTableElement.deleteCaption() method removes the <caption>
	 * element from a given <table>. If there is no <caption> element
	 * associated with the table, this method does nothing.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/deleteCaption
	 */
	public function deleteCaption():void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
		);
		$this->delChild('caption');
	}

	/**
	 * The HTMLTableElement.insertRow() method inserts a new row (<tr>) in
	 * a given <table>, and returns a reference to the new row.
	 *
	 * If a table has multiple <tbody> elements, by default, the new row is
	 * inserted into the last <tbody>.
	 *
	 * Note: insertRow() inserts the row directly into the table. The row
	 * does not need to be appended separately as would be the case if
	 * Document.createElement() had been used to create the new <tr>
	 * element.
	 *
	 * @param ?int $index The row index of the new row. If index is -1 or
	 * equal to the number of rows, the row is appended as the last row.
	 * If index is greater than the number of rows, an IndexSizeError
	 * exception will result. If index is omitted it defaults to -1.
	 * @return Element an HTMLTableRowElement that references
	 * the new row.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/insertRow
	 * @link https://html.spec.whatwg.org/multipage/#htmltableelement
	 */
	// phpcs:ignore
	public function insertRow(?int $index = null):Element {
		$this->allowTypes(
			ElementType::HTMLTableElement,
			ElementType::HTMLTableSectionElement,
		);

		if($this->elementType === ElementType::HTMLTableSectionElement) {
			if(is_null($index)) {
				$index = $this->rows->length;
			}

			if($index < 0) {
				throw new IndexSizeException("Index or size is negative or greater than the allowed amount");
			}

			$insertAfter = $this->rows[$index - 1] ?? null;
			$tr = $this->ownerDocument->createElement("tr");
			$this->insertBefore($tr, $insertAfter?->nextSibling);
			return $tr;
		}

		$lastTBody = $this->hasChildLast('tbody');
		$numRow = $this->rows->length;
		$row = $this->ownerDocument->createElement('tr');
		if($index === null) {
			$index = -1;
		}

// note: for the order of statements @see https://www.w3.org/TR/html52/tabular-data.html#dom-htmltableelement-insertrow
		if($index < -1 || $index > $numRow) {
			throw new IndexSizeException('Row index is outside bounds.');
		}
		elseif($numRow === 0 && $lastTBody === null) {
// note: can't use HTMLTableElement::createTBody() because we need to append row before inserting
			$tbody = $this->ownerDocument->createElement('tbody');
			$tbody->appendChild($row);
			$this->tableInsertChildAfter($tbody, ['caption', 'colgroup']);
		}
		elseif($numRow === 0) {
			$lastTBody->appendChild($row);
		}
		elseif($index === -1 || $index === $numRow) {
			$lastRow = $this->rows->item($numRow - 1);
			$lastRow->parentNode->appendChild($row);
		}
		else {
			$refNode = $this->rows->item($index);
			$refNode->parentNode->insertBefore($row, $refNode);
		}

		return $row;
	}

	/**
	 * The HTMLTableElement.deleteRow() method removes a specific row (<tr>)
	 * from a given <table>.
	 *
	 * @param int $index index is an integer representing the row that
	 * should be deleted. However, the special index -1 can be used to
	 * remove the very last row of a table.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/deleteRow
	 * @link https://html.spec.whatwg.org/multipage/tables.html#dom-table-deleterow
	 */
	public function deleteRow(int $index):void {
		$this->allowTypes(
			ElementType::HTMLTableElement,
			ElementType::HTMLTableSectionElement,
		);
// note: for the order of statements @see https://html.spec.whatwg.org/multipage/tables.html#dom-table-rows
		$numRow = $this->rows->length;
		if($index < -1 || $index >= $numRow) {
			throw new IndexSizeException('Row index is outside bounds.');
		}
		elseif($index === -1) {
			if($numRow > 0) {
				$lastRow = $this->rows->item($numRow - 1);
				$lastRow->parentNode->removeChild($lastRow);
			}
		}
		else {
			$row = $this->rows->item($index);
			$row->parentNode->removeChild($row);
		}
	}

	/**
	 * Removes the cell at the given position in the row. If the given
	 * position is greater (or equal as it starts at zero) than the amount
	 * of cells in the row, or is smaller than 0, it raises a DOMException
	 * with the IndexSizeError value.
	 *
	 * @param int $index
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableRowElement/deleteCell
	 */
	public function deleteCell(int $index):void {
		$this->allowTypes(
			ElementType::HTMLTableRowElement,
		);
		if($index < 0 || $index >= $this->children->length) {
			throw new IndexSizeException("Index or size is negative or greater than the allowed amount");
		}

		$td = $this->getElementsByTagName("td")->item($index);
		$td->remove();
	}

	/**
	 * The HTMLTableRowElement.insertCell() method inserts a new cell (<td>)
	 * into a table row (<tr>) and returns a reference to the cell.
	 *
	 * @param ?int $index index is the cell index of the new cell. If index
	 * is -1 or equal to the number of cells, the cell is appended as the
	 * last cell in the row. If index is greater than the number of cells,
	 * an IndexSizeError exception will result. If index is omitted it
	 * defaults to -1.
	 * @return Element an HTMLTableCellElement that references
	 * the new cell.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableRowElement/insertCell
	 */
	public function insertCell(?int $index = null):Element {
		$this->allowTypes(
			ElementType::HTMLTableRowElement,
		);
		if(is_null($index)) {
			$index = $this->cells->length;
		}

		if($index < 0) {
			throw new IndexSizeException("Index or size is negative or greater than the allowed amount");
		}

		$insertAfter = $this->cells[$index - 1] ?? null;
		$td = $this->ownerDocument->createElement("td");
		$this->insertBefore($td, $insertAfter?->nextSibling);

		return $td;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableRowElement/cells
	 */
	protected function __prop_get_cells():HTMLCollection {
		$this->allowTypes(
			ElementType::HTMLTableRowElement,
		);
		return HTMLCollectionFactory::create(
			fn() => $this->querySelectorAll("td, th")
		);
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableRowElement/rowIndex
	 */
	// phpcs:ignore Generic.Metrics.CyclomaticComplexity
	protected function __prop_get_rowIndex():int {
		$this->allowTypes(
			ElementType::HTMLTableRowElement,
		);
		$table = $this;
		while($table = $table->parentElement) {
			if($table->elementType === ElementType::HTMLTableElement) {
				break;
			}
		}

		if($this->parentElement === $table) {
			foreach($table?->children ?? [] as $i => $child) {
				if($child === $this) {
					return $i;
				}
			}
		}

		$headCount = 0;
		foreach($table?->querySelectorAll("thead>tr") ?? [] as $headIndex => $headChild) {
			$headCount++;
			if($headChild === $this) {
				return $headIndex;
			}
		}

		$bodyCount = 0;
		foreach($table?->querySelectorAll("tbody>tr") ?? [] as $bodyIndex => $headChild) {
			$bodyCount++;
			if($headChild === $this) {
				return $headCount + $bodyIndex;
			}
		}

		foreach($table?->querySelectorAll("tfoot>tr") ?? [] as $footIndex => $headChild) {
			if($headChild === $this) {
				return $headCount + $bodyCount + $footIndex;
			}
		}

		return -1;
	}

	/**
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableRowElement/sectionRowIndex
	 */
	protected function __prop_get_sectionRowIndex():int {
		$this->allowTypes(
			ElementType::HTMLTableRowElement,
		);
		$parent = $this->parentElement;

		foreach($parent?->children ?? [] as $i => $child) {
			if($child === $this) {
				return $i;
			}
		}

		return -1;
	}

	protected function __prop_get_kind():string {
		return $this->getAttribute("kind") ?? "";
	}

	protected function __prop_set_kind(string $value):void {
		$this->setAttribute("kind", $value);
	}

	protected function __prop_get_srclang():string {
		return $this->getAttribute("srclang") ?? "";
	}

	protected function __prop_set_srclang(string $value):void {
		$this->setAttribute("srclang", $value);
	}

	protected function __prop_get_default():bool {
		return $this->hasAttribute("default");
	}

	protected function __prop_set_default(bool $value):void {
		if($value) {
			$this->setAttribute("default", "");
		}
		else {
			$this->removeAttribute("default");
		}
	}

	protected function __prop_get_track():TextTrack {
		return new TextTrack();
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLVideoElement/poster */
	protected function __prop_get_poster():string {
		$this->allowTypes(
			ElementType::HTMLVideoElement,
		);
		return $this->getAttribute("poster") ?? "";
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLVideoElement/poster */
	protected function __prop_set_poster(string $src):void {
		$this->allowTypes(
			ElementType::HTMLVideoElement,
		);
		$this->setAttribute("poster", $src);
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLVideoElement/videoHeight */
	protected function __prop_get_videoHeight():int {
		return 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLVideoElement/videoWidth */
	protected function __prop_get_videoWidth():int {
		return 0;
	}
}
