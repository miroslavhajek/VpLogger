<?php
namespace VpLogger\Log\Writer;

interface DirectWriterInterface
{
    public function writeLine(array $event);
}
