<?php

namespace GT\Dom;

use GT\Dom\Exception\DocumentStreamIsClosedException;
use GT\Dom\Exception\DocumentStreamNotWritableException;
use GT\Dom\Exception\DocumentStreamSeekFailureException;
use GT\Dom\Exception\WriteOnNonHTMLDocumentException;
use RuntimeException;
use TypeError;

trait DocumentStream {
	/** @var ?resource */
	protected $stream;
	protected int $streamBytesFilled;

	/**
	 * Closes the stream and any underlying resources.
	 */
	public function close():void {
		$this->stream = null;
	}

	/**
	 * Separates any underlying resources from the stream.
	 *
	 * After the stream has been detached, the stream is in an unusable state.
	 *
	 * @return resource|null Underlying PHP stream, if any
	 */
	public function detach() {
		$this->fillStream();
		$stream = $this->stream;
		$this->stream = null;
		return $stream;
	}

	/**
	 * Get the size of the stream if known.
	 */
	public function getSize():?int {
		$this->fillStream();
		return $this->streamBytesFilled;
	}

	/**
	 * Returns the current position of the file read/write pointer
	 */
	public function tell():int {
		return ftell($this->stream);
	}

	/**
	 * Returns true if the stream is at the end of the stream.
	 */
	public function eof():bool {
		$tell = $this->tell();
		$this->fillStream();
		$this->seek($tell);
		return feof($this->stream) || $tell >= $this->streamBytesFilled;
	}

	/**
	 * Returns whether or not the stream is seekable.
	 */
	public function isSeekable():bool {
		$seekable = $this->getMetadata("seekable");
		return $seekable ?? false;
	}

	/**
	 * Seek to a position in the stream.
	 *
	 * @link http://www.php.net/manual/en/function.fseek.php
	 * @param int $offset Stream offset
	 * @param int $whence Specifies how the cursor position will be calculated
	 *     based on the seek offset. Valid values are identical to the built-in
	 *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
	 *     offset bytes SEEK_CUR: Set position to current location plus offset
	 *     SEEK_END: Set position to end-of-stream plus offset.
	 * @throws RuntimeException on failure.
	 */
	public function seek($offset, $whence = SEEK_SET):void {
		$result = null;
		$this->fillStream();

		try {
			$result = fseek($this->stream, $offset, $whence);
		}
		finally {
			if($result === -1) {
				throw new DocumentStreamSeekFailureException("Error seeking Document Stream");
			}
		}
	}

	/**
	 * Seek to the beginning of the stream.
	 *
	 * If the stream is not seekable, this method will raise an exception;
	 * otherwise, it will perform a seek(0).
	 *
	 * @throws RuntimeException on failure.
	 * @link http://www.php.net/manual/en/function.fseek.php
	 * @see seek()
	 */
	public function rewind():void {
		$this->fillStream();
		$this->seek(0);
	}

	/**
	 * Returns whether or not the stream is writable.
	 *
	 * @return bool
	 */
	public function isWritable():bool {
		if(!isset($this->stream)) {
			return false;
		}

		$mode = $this->getMetadata("mode");
		$writable = false;

		if(strstr($mode, "w") || strstr($mode, "+") || strstr($mode, "a")) {
			$writable = true;
		}

		return $writable;
	}

	/**
	 * The Document.write() method writes a string of text to a document
	 * stream opened by document.open().
	 *
	 * Note: Because document.write() writes to the document stream, calling
	 * document.write() on a closed (loaded) document automatically calls
	 * document.open(), which will clear the document.
	 *
	 * @param string $string The string that is to be written.
	 * @return int Returns the number of bytes written to the stream.
	 * @throws RuntimeException on failure.
	 */
	public function write($string):int {
		if(!$this->isWritable() || !$this instanceof HTMLDocument) {
			throw new DocumentStreamNotWritableException();
		}

		$this->body?->append($string);
		return strlen($string);
	}

	/**
	 * Returns whether or not the stream is readable.
	 *
	 * @return bool
	 */
	public function isReadable():bool {
		$mode = $this->getMetadata("mode") ?? "";
		$readable = false;

		if(strstr($mode, "r")
			|| strstr($mode, "+")) {
			$readable = true;
		}

		return $readable;
	}

	public function open():Document {
		if(!$this instanceof HTMLDocument) {
			throw new WriteOnNonHTMLDocumentException();
		}
		/** @var HTMLDocument $this */
		$this->stream = fopen("php://memory", "r+");
		return $this;
	}

	/**
	 * Read data from the stream.
	 *
	 * @param int $length Read up to $length bytes from the object and return
	 *     them. Fewer than $length bytes may be returned if underlying stream
	 *     call returns fewer bytes.
	 * @return string Returns the data read from the stream, or an empty string
	 *     if no bytes are available.
	 * @throws RuntimeException if an error occurs.
	 */
	public function read($length):string {
		$tell = $this->tell();
		$this->fillStream();
		$this->seek($tell);
		return fread($this->stream, $length);
	}

	/**
	 * Returns the remaining contents in a string
	 *
	 * @return string
	 * @throws RuntimeException if unable to read or an error occurs while
	 *     reading.
	 */
	public function getContents():string {
		$this->fillStream();
		return stream_get_contents($this->stream);
	}

	/**
	 * Get stream metadata as an associative array or retrieve a specific key.
	 *
	 * The keys returned are identical to the keys returned from PHP's
	 * stream_get_meta_data() function.
	 *
	 * @link http://php.net/manual/en/function.stream-get-meta-data.php
	 * @param string $key Specific metadata to retrieve.
	 * @return array|mixed|null Returns an associative array if no key is
	 *     provided. Returns a specific key value if a key is provided and the
	 *     value is found, or null if the key is not found.
	 */
	public function getMetadata($key = null):mixed {
		try {
			$metaData = stream_get_meta_data($this->stream);
		}
		catch(TypeError) {
			return null;
		}

		if(is_null($key)) {
			return $metaData;
		}

		return $metaData[$key] ?? null;
	}

	private function fillStream():void {
		if(!isset($this->stream)) {
			throw new DocumentStreamIsClosedException("DocumentStream is closed");
		}

		if(isset($this->streamBytesFilled)) {
			return;
		}

		$this->streamBytesFilled = fwrite($this->stream, $this->__toString());
		fseek($this->stream, 0);
	}
}
