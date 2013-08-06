<?php
namespace VpLogger\Log\Writer;

use Zend\Log\Writer\Stream as ZfStream;

class Stream extends ZfStream implements DirectWriterInterface
{
    public function writeLine(array $event)
    {
        $this->doWrite($event);
    }
}
