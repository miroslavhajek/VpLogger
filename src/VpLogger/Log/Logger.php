<?php
namespace VpLogger\Log;

use Zend\Log\Logger as ZfLogger;

use Traversable;

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
        $deltaTime          = round((microtime(true) - $this->start)*1000, 2);
        //Format to total length 8 chars, leading zeros, two decimals
        $extra['deltaTime'] = sprintf('%08.2F', $deltaTime);
        $extra['request']   = $this->loggerId;
        parent::log($priority, $message, $extra);
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