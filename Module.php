<?php
namespace VpLogger;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module
{
    /**
     * Module bootstrap method.
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        //Get basic objects
        /** @var $sm ServiceManager */
        $sm             = $e->getApplication()->getServiceManager();
//        $eventManager   = $e->getApplication()->getEventManager();
//        $config         = $sm->get('config');

        //Initialize logger
        $logger = $sm->get('VpLogger\logger');

        //Performance log
//        $eventManager->trigger('log', $this,
//            array ('message'    => 'Vivo Portal Module bootstrapped',
//                   'priority'   => Logger::PERF_BASE));
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
