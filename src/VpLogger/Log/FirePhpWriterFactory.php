<?php
namespace VpLogger\Log;

use VpLogger\Log\Filter\Events as EventsFilter;
use VpLogger\Log\Formatter\SimpleExtra;
use VpLogger\Log\Writer\FirePhp as FirePhpWriter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Log\Filter\Priority;
use Zend\Stdlib\ArrayUtils;

/**
 * FirePhpWriterFactory
 */
class FirePhpWriterFactory implements FactoryInterface
{
    /**
     * Options
     * @var array
     */
    protected $options  = array(
        'priority_min'  => null,
        'priority_max'  => null,
        'events'        => array(
            'allow'         => array(
//                'all'           => array('*', '*'),
            ),
            'block'         => array(
            ),
        ),
        'format'        => null,
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
//        $sm         = $serviceLocator->getServiceLocator();
        $writer     = new  FirePhpWriter();
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
        if (!is_null($this->options['format'])) {
            $format = $this->options['format'];
        } else {
            $format = '%deltaTime% %message%, %source%#%event%, %timestamp%, %priorityName% (%priority%)';
        }
        $formatter  = new SimpleExtra($format);
        $writer->setFormatter($formatter);
        return $writer;
    }
}
