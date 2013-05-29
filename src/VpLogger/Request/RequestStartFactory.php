<?php
namespace VpLogger\Request;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * RequestStartFactory
 * Finds or creates request start time
 */
class RequestStartFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\ConfigException
     * @return float
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //Returns request start time as float
        if (!defined('REQUEST_START') || !is_float(REQUEST_START)) {
            throw new Exception\ConfigException(
                sprintf("%s: 'REQUEST_START' constant not defined or not float;"
                        . ' put "%s" as the first command into your index.php',
                    __METHOD__, "define('REQUEST_START', microtime(true));"));
        }
        return REQUEST_START;
    }
}
