<?php
namespace VpLogger\Log;

use Zend\Log\LoggerInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedEventManagerAwareInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Event listener for logging. The listener is attached to shared event manager and log event.
 * @todo avoid logging same event more than once.
 */
class EventListener implements SharedEventManagerAwareInterface
{
    /**
     * Logger.
     * @var \Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Shared event manager.
     * @var SharedEventManagerInterface
     */
    protected $sharedEventManager;

    /**
     * Options
     * @example
     *  array (
            'attach' => array (
                array('*', 'log') //log 'log' events
                array('*', '*') //log all events
            )
        )
     *
     * @var array
     */
    protected $options = array (
        'attach' => array (
            //array('*', '*'), //listen for all events
        ),
        //Default priority to use when the event does not explicitly specify the logging priority
        'default_priority'  => Logger::DEBUG,
    );

   /**
    * Constructor.
    * @param LoggerInterface $logger
    * @param array $options
    */
   public function __construct(LoggerInterface $logger, array $options = array())
    {
        $this->logger   = $logger;
        $this->options  = array_merge($this->options, $options);
        //Remove null items (configuration override support)
        foreach ($this->options['attach'] as $key => $value) {
            if (is_null($value)) {
                unset($this->options['attach'][$key]);
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\SharedEventManagerAwareInterface::setSharedManager()
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager)
    {
        $this->sharedEventManager = $sharedEventManager;
        //attach listener to all events
        foreach ($this->options['attach'] as $rule) {
            $this->sharedEventManager->attach($rule[0], $rule[1], array($this, 'log'), 10000);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\SharedEventManagerAwareInterface::getSharedManager()
     */
    public function getSharedManager()
    {
        return $this->sharedEventManager;
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\SharedEventManagerAwareInterface::unsetSharedManager()
     */
    public function unsetSharedManager()
    {
        $this->sharedEventManager = null;
    }

    /**
     * Log callback function attached to shared event manager.
     * @param Event $event
     * @return bool
     */
    public function log(Event $event)
    {
        if ($event->getName() == 'log') {
            //When event 'log' is triggered - this event hasn't any function except to log something
            $log = $event->getParams();
        } else if($event->getName() == 'log:write') {
            $source  = is_object($event->getTarget()) ? get_class($event->getTarget()) : $event->getTarget();
            $log     = $event->getParams();
            $message = isset($log['message']) ? $log['message'] : '';

            //Extra information for custom writers
            $extra            = isset($log['extra']) && is_array($log['extra']) ? $log['extra'] : array();
            $extra['source']  = $source;
            $extra['event']   = $event->getName();

            $this->logger->writeLine($message, $extra);

            return true;
        } else {
            //When other events are triggered
            $log = $event->getParam('log', array());
        }

        $source     = is_object($event->getTarget()) ? get_class($event->getTarget()) : $event->getTarget();
        $priority   = isset($log['priority']) ? $log['priority'] : $this->options['default_priority'];

        $message  = $source . "#" . $event->getName() .
                (isset($log['message']) ? ': ' . $log['message'] : '');

        //Extra information for custom writers
        $extra              = isset($log['extra']) && is_array($log['extra']) ? $log['extra'] : array();
        $extra['source']    = $source;
        $extra['event']     = $event->getName();
        $extra['message']   = isset($log['message']) ? $log['message'] : null;

        $this->logger->log($priority, $message, $extra);

        //return true to continue with event propagation.
        return true;
    }
}
