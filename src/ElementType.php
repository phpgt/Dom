<?php
namespace GT\Dom;

enum ElementType {
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Element */
	case Element;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement */
	case HTMLElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/ElementCSSInlineStyle */
	case ElementCSSInlineStyle;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAnchorElement */
	case HTMLAnchorElement;
//	case HTMLAnchorOrAreaElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAreaElement */
	case HTMLAreaElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLAudioElement */
	case HTMLAudioElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBaseElement */
	case HTMLBaseElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBodyElement */
	case HTMLBodyElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLBRElement */
	case HTMLBRElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLButtonElement */
	case HTMLButtonElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCanvasElement */
	case HTMLCanvasElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataElement */
	case HTMLDataElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDataListElement */
	case HTMLDataListElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDetailsElement */
	case HTMLDetailsElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement */
	case HTMLDialogElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDivElement */
	case HTMLDivElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLDListElement */
	case HTMLDListElement;
//	case HTMLElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLEmbedElement */
	case HTMLEmbedElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFieldSetElement */
	case HTMLFieldSetElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement */
	case HTMLFormElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLHeadElement */
	case HTMLHeadElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLHeadingElement */
	case HTMLHeadingElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLHRElement */
	case HTMLHRElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLHtmlElement */
	case HTMLHtmlElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLIFrameElement */
	case HTMLIFrameElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement */
	case HTMLImageElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement */
	case HTMLInputElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLabelElement */
	case HTMLLabelElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLegendElement */
	case HTMLLegendElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLiElement */
	case HTMLLiElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLLinkElement */
	case HTMLLinkElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMapElement */
	case HTMLMapElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement */
	case HTMLMediaElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMenuElement */
	case HTMLMenuElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMetaElement */
	case HTMLMetaElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLMeterElement */
	case HTMLMeterElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLModElement */
	case HTMLModElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLObjectElement */
	case HTMLObjectElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOListElement */
	case HTMLOListElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptGroupElement */
	case HTMLOptGroupElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOptionElement */
	case HTMLOptionElement;
//	case HTMLOrForeignElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLOutputElement */
	case HTMLOutputElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLParagraphElement */
	case HTMLParagraphElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLParamElement */
	case HTMLParamElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLPictureElement */
	case HTMLPictureElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLPreElement */
	case HTMLPreElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLProgressElement */
	case HTMLProgressElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLQuoteElement */
	case HTMLQuoteElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLScriptElement */
	case HTMLScriptElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSelectElement */
	case HTMLSelectElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSourceElement */
	case HTMLSourceElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLSpanElement */
	case HTMLSpanElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLStyleElement */
	case HTMLStyleElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCaptionElement */
	case HTMLTableCaptionElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableCellElement */
	case HTMLTableCellElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableColElement */
	case HTMLTableColElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement */
	case HTMLTableElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableRowElement */
	case HTMLTableRowElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableSectionElement */
	case HTMLTableSectionElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTemplateElement */
	case HTMLTemplateElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTextAreaElement */
	case HTMLTextAreaElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTimeElement */
	case HTMLTimeElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTitleElement */
	case HTMLTitleElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLTrackElement */
	case HTMLTrackElement;
//	case HTMLUIElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLUListElement */
	case HTMLUListElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLUnknownElement */
	case HTMLUnknownElement;
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLVideoElement */
	case HTMLVideoElement;
}
