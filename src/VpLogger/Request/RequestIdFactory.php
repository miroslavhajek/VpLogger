<?php
namespace VpLogger\Request;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * RequestIdFactory
 * Constructs Request ID
 */
class RequestIdFactory implements FactoryInterface
{
    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $requestStart   = $serviceLocator->get('VpLogger\request_start');
        $requestId      = substr(md5((string)$requestStart), 0, 5);
        return $requestId;
    }
}
