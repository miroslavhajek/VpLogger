<?php
namespace VpLogger\Log\Formatter;

use VpLogger\Log\Exception;

use Zend\Log\Formatter\Base;

use Traversable;

/**
 * PerfLog
 */
class PerfLog extends Base
{
    /**
     * Format specifier for log messages
     * @var string
     */
    protected $format;

    /**
     * Class constructor
     *
     * @see http://php.net/manual/en/function.date.php
     * @param null|string $format Format specifier for log messages
     * @param null|string $dateTimeFormat Format specifier for DateTime objects in event data
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($format = null, $dateTimeFormat = null)
    {
        if ($format instanceof Traversable) {
            $format = iterator_to_array($format);
        }
        if (is_array($format)) {
            $dateTimeFormat = isset($format['dateTimeFormat'])? $format['dateTimeFormat'] : null;
            $format         = isset($format['format'])? $format['format'] : null;
        }
        if (isset($format) && !is_string($format)) {
            throw new Exception\InvalidArgumentException('Format must be a string');
        }
        $this->format = isset($format) ? $format : $this->getDefaultFormat();
        parent::__construct($dateTimeFormat);
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        $output = $this->format;
        //Copy 'extra' items to event, to make them accessible from the format
        if (array_key_exists('extra', $event) && is_array($event['extra'])) {
            $event  = array_merge($event, $event['extra']);
        }
        $event = parent::format($event);
        foreach ($event as $name => $value) {
            if ('extra' == $name && count($value)) {
                $value = $this->normalize($value);
            } elseif ('extra' == $name) {
                // Don't print an empty array
                $value = '';
            }
            if (!is_null($value)) {
                $output = str_replace("%$name%", $value, $output);
            }
        }

        if (isset($event['extra']) && empty($event['extra'])
            && false !== strpos($this->format, '%extra%')
        ) {
            $output = rtrim($output, ' ');
        }
        return $output;
    }

    /**
     * Returns default format
     * @return string
     */
    protected function getDefaultFormat()
    {
        $format = '%deltaTime%    %message%, %source%#%event%, %timestamp%, %priorityName% (%priority%)';
        return $format;
    }
}
