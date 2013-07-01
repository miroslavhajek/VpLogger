<?php
namespace VpLogger\Log\Filter;

use Zend\Log\Filter\FilterInterface;
use Zend\Http\Request;

/**
 * BlockAll
 * Log filter blocking all messages
 */
class BlockAll implements FilterInterface
{
    /**
     * Returns TRUE to accept the message, FALSE to block it.
     * @param array $event event data
     * @return bool accepted?
     */
    public function filter(array $event)
    {
        return false;
    }
}
