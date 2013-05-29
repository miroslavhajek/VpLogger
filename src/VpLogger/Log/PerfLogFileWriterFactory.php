<?php
namespace VpLogger\Log;

use VpLogger\Log\Formatter\PerfLog as PerfLogFormatter;

use Zend\Log\Writer\Stream;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Log\Filter\Priority;

use DateTime;

/**
 * PerfLogFileWriterFactory
 */
class PerfLogFileWriterFactory implements FactoryInterface
{
    /**
     * Options
     * @var array
     */
    protected $options  = array(
        'log_dir'   => null,
    );

    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options  = array_merge($this->options, $options);
    }

    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\ConfigException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$this->options['log_dir']) {
            throw new Exception\ConfigException(sprintf("%s: 'log_dir' option not set", __METHOD__));
        }
        $sm         = $serviceLocator->getServiceLocator();
        $now        = new DateTime();
        $time       = $now->format('Y-m-j_His');
        $requestId  = $sm->get('request_id');
        $filename   = sprintf('%s/vivo_perf_%s_%s.log', $this->options['log_dir'], $time, $requestId);
        $writer     = new Stream($filename);
        //Filters
        $filterLow  = new Priority(Logger::PERF_BASE, '>=');
        $filterHi   = new Priority(Logger::PERF_FINEST, '<=');
        $writer->addFilter($filterLow);
        $writer->addFilter($filterHi);
        //Formatter
        $formatter  = new PerfLogFormatter();
        $writer->setFormatter($formatter);
        return $writer;
    }
}
