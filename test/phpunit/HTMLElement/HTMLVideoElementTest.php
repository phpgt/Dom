<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\ClientSide\MediaStream;
use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLVideoElementTest extends HTMLElementTestCase {
	public function testAudioTracks():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$track = $sut->audioTracks;
	}

	public function testTimeRanges():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$buffered = $sut->buffered;
	}

	public function testController():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertNull($sut->controller);
	}

	public function testError():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertNull($sut->error);
	}

	public function testTextTracks():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$tracks = $sut->textTracks;
	}

	public function testVideoTracks():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$tracks = $sut->videoTracks;
	}

	public function testAutoplay():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelateBool($sut, "autoplay");
	}

	public function testControls():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelateBool($sut, "controls");
	}

	public function testControlsList():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		$controlsList = $sut->controlsList;
		self::assertCount(0, $controlsList);
		$sut->setAttribute("controlsList", "one two three");
		self::assertCount(3, $controlsList);
		self::assertEquals("one", $controlsList->item(0));
		self::assertEquals("two", $controlsList->item(1));
		self::assertEquals("three", $controlsList->item(2));

		$sut->controlsList->add("four");
		self::assertCount(4, $sut->controlsList);
	}

	public function testCrossOrigin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelate(
			$sut,
			"crossorigin",
			"crossOrigin"
		);
	}

	public function testCurrentSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertEquals("", $sut->currentSrc);
	}

	public function testCurrentTime():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$t = $sut->currentTime;
	}

	public function testCurrentTimeSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->currentTime = 123;
	}

	public function testDefaultMuted():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelateBool(
			$sut,
			"muted",
			"defaultMuted"
		);
	}

	public function testDefaultPlaybackRate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$r = $sut->defaultPlaybackRate;
	}

	public function testDefaultPlaybackRateSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->defaultPlaybackRate = 1.25;
	}

	public function testDisableRemotePlayback():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$b = $sut->disableRemotePlayback;
	}

	public function testDisableRemotePlaybackSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->disableRemotePlayback = true;
	}

	public function testDuration():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertNull($sut->duration);
	}

	public function testEnded():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertFalse($sut->ended);
	}

	public function testLoop():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelateBool($sut, "loop");
	}

	public function testMediaGroup():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelate(
			$sut,
			"mediagroup",
			"mediaGroup"
		);
	}

	public function testMuted():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$m = $sut->muted;
	}

	public function testMutedSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->muted = true;
	}

	public function testNetworkState():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertEquals(0, $sut->networkState);
	}

	public function testPaused():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$m = $sut->paused;
	}

	public function testPlaybackRate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$r = $sut->playbackRate;
	}

	public function testPlaybackRateSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->playbackRate = 1.25;
	}

	public function testPlayed():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$p = $sut->played;
	}

	public function testPreload():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelate($sut, "preload");
	}

	public function testReadyState():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertEquals(0, $sut->readyState);
	}

	public function testSeekable():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$p = $sut->seekable;
	}

	public function testSeeking():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertFalse($sut->seeking);
	}

	public function testSinkId():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertEquals("", $sut->sinkId);
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testSrcObject():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$s = $sut->srcObject;
	}

	public function testSrcObjectSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->srcObject = self::createMock(MediaStream::class);
	}

	public function testVolume():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$v = $sut->volume;
	}

	public function testVolumeSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->volume = 0.5;
	}

// Video-specific tests:
	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "height");
	}

	public function testPoster():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelate($sut, "poster");
	}

	public function testVideoHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertEquals(0, $sut->videoHeight);
	}

	public function testVideoWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertEquals(0, $sut->videoWidth);
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("video");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "width");
	}
}
