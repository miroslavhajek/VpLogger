<?php
namespace VpLogger\Log;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Session\Container;

/**
 * Logger factory.
 */
class LoggerFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return Logger
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $writerPluginManager    = $serviceLocator->get('VpLogger\writer_plugin_manager');
        $requestStart           = $serviceLocator->get('VpLogger\request_start');
        $requestId              = $serviceLocator->get('VpLogger\request_id');
        $logger                 = new Logger($requestStart, $requestId);
        $logger->setWriterPluginManager($writerPluginManager);

        //add main service manager as peering sm
        $writerPluginManager->addPeeringServiceManager($serviceLocator);

        $config = $serviceLocator->get('config');
        $config = $config['VpLogger\logger'];

        $writerAdded    = false;
        foreach ($config['writers'] as $writer => $writerConfig) {
            if (!is_null($writerConfig)) {
                if (is_int($writer)) {
                    //No writer config specified, just a writer name (i.e. numeric indices)
                    $writer         = $writerConfig;
                    $writerConfig   = array();
                }
                if ((isset($writerConfig['enabled']) && $writerConfig['enabled'])
                        || $this->isActivatedBySession($writerConfig)) {
                    //The writer is enabled
                    if (array_key_exists('priority', $writerConfig)) {
                        $priority   = $writerConfig['priority'];
                    } else {
                        $priority   = 1;
                    }
                    if (array_key_exists('options', $writerConfig)) {
                        $options    = $writerConfig['options'];
                    } else {
                        $options    = null;
                    }
                    $logger->addWriter($writer, $priority, $options);
                    $writerAdded    = true;
                }
            }
        }
        //If no writer has been added, add the 'null' writer
        if (!$writerAdded) {
            $logger->addWriter('null');
        }
        $logger->log(Logger::INFO, 'VpLogger initialized');
        $eventListener = new EventListener($logger, $config['listener']);
        $eventListener->setSharedManager($serviceLocator->get('shared_event_manager'));
        return $logger;
    }

    /**
     * Returns true when the writer referenced by the writer config is activated by session
     * @param array $writerConfig
     * @return boolean
     */
    protected function isActivatedBySession(array $writerConfig)
    {
        if (isset($writerConfig['enable_by_session']) && $writerConfig['enable_by_session']
                && isset($writerConfig['session']['namespace'])
                && isset($writerConfig['session']['var'])) {
            $namespace  = $writerConfig['session']['namespace'];
            $var        = $writerConfig['session']['var'];
            $session    = new Container($namespace);
            if (isset($session[$var]) && $session[$var]) {
                $activated  = true;
            } else {
                $activated  = false;
            }
        } else {
            $activated  = false;
        }
        return $activated;
    }
}
