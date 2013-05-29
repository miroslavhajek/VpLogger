<?php
namespace VpLogger\Log;

use VpLogger\Log\Filter\Events as EventsFilter;

use Zend\Log\Writer\Stream;
use Zend\Log\Filter\Priority;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Zend\Stdlib\ArrayUtils;

/**
 * LogFileWriterFactory
 */
class LogFileWriterFactory implements FactoryInterface
{
    /**
     * Options
     * @var array
     */
    protected $options  = array(
        'log_dir'       => null,
        'log_name'      => 'application',
        'priority_min'  => null,
        'priority_max'  => Logger::DEBUG,
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
        $date       = date('Y-m-j');
        $filename   = sprintf('%s/%s_%s.log', $this->options['log_dir'], $this->options['log_name'], $date);
        $writer     = new Stream($filename);
        if (!is_null($this->options['priority_min'])) {
            $filter     = new Priority($this->options['priority_min'], '>=');
            $writer->addFilter($filter);
        }
        if (!is_null($this->options['priority_max'])) {
            $filter     = new Priority($this->options['priority_max']);
            $writer->addFilter($filter);
        }
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
        return $writer;
    }
}
