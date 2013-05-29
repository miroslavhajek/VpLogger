<?php
namespace VpLogger\Log\Filter;

use VpLogger\Log\Exception;

use Zend\Log\Filter\FilterInterface;

/**
 * Events
 * Log filter filtering based on the 'source' and 'event' extra params
 */
class Events implements FilterInterface
{
    /**
     * Array of allowed events
     * array(
     *      'arbitrary_key'   => array('source or *', 'event or *'),
     * )
     * @var array
     */
    protected $allow    = array();

    /**
     * Array of blocked events
     * array(
     *      'arbitrary_key'   => array('source or *', 'event or *'),
     * )
     * @var array
     */
    protected $block    = array();

    /**
     * Constructor
     * @param array $allow List of allowed source/event pairs - put wildcard pairs as first items to optimize
     * @param array $block List of blocked source/event pairs - pur wildcard pairs as first items to optimize
     */
    public function __construct(array $allow = array(), array $block = array())
    {
        $this->allow  = $this->sanitizeList($allow);
        $this->block  = $this->sanitizeList($block);
    }

    /**
     * Sanitizes allow/block list
     * Removes items with null value (null values are supported as a means of configuration overriding)
     * Checks values are arrays with two string items
     * @param array $list
     * @throws \VpLogger\Log\Exception\InvalidArgumentException
     * @return array
     */
    protected function sanitizeList(array $list)
    {
        foreach ($list as $key => $value) {
            if (is_null($value)) {
                unset($list[$key]);
            } elseif (is_array($value)) {
                if (count($value) != 2) {
                    throw new Exception\InvalidArgumentException(
                        sprintf("%s: Item in allow/block list must be an array with two items; got %s items",
                            __METHOD__, count($value)));
                }
            } else {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Item in allow/block list must be an array; got %s", __METHOD__, gettype($value)));
            }
        }
        return $list;
    }

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     * @param array $event event data
     * @return bool accepted?
     */
    public function filter(array $event)
    {
        $isAllowed  = $this->isEventInList($event, $this->allow);
        $isBlocked  = $this->isEventInList($event, $this->block);
        $accept = $isAllowed && !$isBlocked;
        return $accept;
    }

    /**
     * Returns true when the specified log event is in the submitted list
     * @param array $logEvent
     * @param array $list
     * @return bool
     */
    protected function isEventInList(array $logEvent, array $list)
    {
        if (isset($logEvent['extra']['source'])) {
            $source = $logEvent['extra']['source'];
        } else {
            $source = null;
        }
        if (isset($logEvent['extra']['event'])) {
            $event  = $logEvent['extra']['event'];
        } else {
            $event  = null;
        }
        foreach ($list as $pair) {
            $sourceInList   = $pair[0];
            $eventInList    = $pair[1];
            if (($sourceInList == '*' || $sourceInList == $source)
                    && ($eventInList == '*' || $eventInList == $event)) {
                return true;
            }
        }
        return false;
    }
}
