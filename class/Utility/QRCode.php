<?php
namespace Utility;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRCode {
    private $content;
    private $writer;

    public function __construct($content = "") {
       $this->setContent($content);
       $this->writer = new Writer(
	  new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
          ));
    }

    public function setContent($content) { $this->content = $content; return $this; }
    public function getContent() { return $this->content; }
    public function emptyContent() { return empty($this->content); }

    public function __toString() {
        return $this->writer->writeString($this->content);
    }

    public function save($filepath) {
        $this->writer->writeFile($this->content, $filepath);
        return $this;
    }
}