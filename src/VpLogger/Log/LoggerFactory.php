<?php
namespace VpLogger\Log;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

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

        if (!count($config['writers'])) {
            $logger->addWriter('null');
        } else {
            foreach ($config['writers'] as $writer => $writerConfig) {
                if (is_int($writer)) {
                    //No writer config specified, just a writer name (i.e. numeric indices)
                    $writer         = $writerConfig;
                    $writerConfig   = array();
                }
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
            }
            $logger->log(Logger::INFO, 'Logger init.');
            $eventListener = new EventListener($logger, $config['listener']);
            $eventListener->setSharedManager($serviceLocator->get('shared_event_manager'));
        }
        return $logger;
    }
}
