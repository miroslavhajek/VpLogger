<?php
namespace VpLogger\Log;

use VpLogger\Log\Writer\DirectWriterInterface;
use Zend\Log\Logger as ZfLogger;

/**
 * Logger
 * Vivo logger
 */
class Logger extends ZfLogger
{
    /**
     * Log levels for performance logging
     */
    const PERF_BASE     = 8;
    const PERF_FINER    = 9;
    const PERF_FINEST   = 10;

    /**
     * List of priority code => priority (short) name
     * @var array
     */
    protected $priorities = array(
        self::EMERG  => 'EMERG',
        self::ALERT  => 'ALERT',
        self::CRIT   => 'CRIT',
        self::ERR    => 'ERR',
        self::WARN   => 'WARN',
        self::NOTICE => 'NOTICE',
        self::INFO   => 'INFO',
        self::DEBUG  => 'DEBUG',
        self::PERF_BASE     => 'PERF_BASE',
        self::PERF_FINER    => 'PERF_FINER',
        self::PERF_FINEST   => 'PERF_FINEST',
    );

    /**
     * 'Zero' time
     * @var float
     */
    protected $start;

    /**
     * Id of this logger instance
     * @var string
     */
    protected $loggerId;

    /**
     * Constructor
     * @param float $start
     * @param string $loggerId
     */
    public function __construct($start = null, $loggerId = null)
    {
        if (is_null($start) || !is_float($start)) {
            $start  = microtime(true);
        }
        if (is_null($loggerId)) {
            $loggerId   = substr(md5(microtime()),0,4);
        }
        $this->start    = $start;
        $this->loggerId = (string) $loggerId;
        parent::__construct();
    }

    /**
     * Add a message as a log entry
     * @param  int $priority
     * @param  mixed $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function log($priority, $message, $extra = array())
    {
        parent::log($priority, $message, $this->prepareExtra($extra));
    }

    public function writeLine($message, $extra = array())
    {
        $event = array(
            'timestamp'    => new \DateTime(),
            'priority'     => 0,
            'priorityName' => 'INFO_LINE',
            'message'      => (string) $message,
            'extra'        => $this->prepareExtra($extra),
        );

        foreach ($this->processors->toArray() as $processor) {
            $event = $processor->process($event);
        }

        foreach ($this->writers->toArray() as $writer) {
            if ($writer instanceof DirectWriterInterface) {
                $writer->writeLine($event);
            }
        }
    }

    private function prepareExtra(array $extra = array())
    {
        $deltaTime = round((microtime(true) - $this->start)*1000, 2);
        //Format to total length 8 chars, leading zeros, two decimals
        $extra['deltaTime'] = sprintf('%08.2F', $deltaTime);
        $extra['request']   = $this->loggerId;

        return $extra;
    }

    /**
     * Log
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function perfBase($message, $extra = array())
    {
        return $this->log(self::PERF_BASE, $message, $extra);
    }

}