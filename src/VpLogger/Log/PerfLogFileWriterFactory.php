<?php
namespace VpLogger\Log;

use VpLogger\Log\Formatter\PerfLog as PerfLogFormatter;
use VpLogger\Log\Filter\Events as EventsFilter;

use Zend\Log\Writer\Stream;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Log\Filter\Priority;
use Zend\Stdlib\ArrayUtils;

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
        'log_dir'       => null,
        'log_name'      => 'perf',
        'priority_min'  => Logger::PERF_BASE,
        'priority_max'  => Logger::PERF_FINEST,
        'events'        => array(
            'allow'         => array(
                'all'           => array('*', '*'),
            ),
            'block'         => array(
            ),
        ),
    );

    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options  = ArrayUtils::merge($this->options, $options);
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
        $requestId  = $sm->get('VpLogger\request_id');
        $filename   = sprintf('%s/vivo_perf_%s_%s.log', $this->options['log_dir'], $time, $requestId);
        $writer     = new Stream($filename);
        //Min priority filter
        if (!is_null($this->options['priority_min'])) {
            $filter     = new Priority($this->options['priority_min'], '>=');
            $writer->addFilter($filter);
        }
        //Max priority filter
        if (!is_null($this->options['priority_max'])) {
            $filter     = new Priority($this->options['priority_max']);
            $writer->addFilter($filter);
        }
        //Event filter
        if (isset($this->options['events']['allow'])) {
            $allow  = $this->options['events']['allow'];
        } else {
            $allow  = array();
        }
        if (isset($this->options['events']['block'])) {
            $block  = $this->options['events']['block'];
        } else {
            $block  = array();
        }
        if (count($allow) > 0 || count($block) > 0) {
            $filter = new EventsFilter($allow, $block);
            $writer->addFilter($filter);
        }
        //Formatter
        $formatter  = new PerfLogFormatter();
        $writer->setFormatter($formatter);
        return $writer;
    }
}
