<?php
namespace VpLogger\Log\Writer;

use VpLogger\Log\Logger;

use Zend\Log\Writer\FirePhp as ZfFirePhpWriter;

/**
 * FirePhp
 */
class FirePhp extends ZfFirePhpWriter
{
    /**
     * Write a message to the log.
     * @param  array $event event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        $firephp = $this->getFirePhp();
        if (!$firephp->getEnabled()) {
            return;
        }
        $line   = $this->formatter->format($event);
//        list($line, $label) = $this->formatter->format($event);
        switch ($event['priority']) {
//                $firephp->error($line);
//                break;
            case Logger::EMERG:
            case Logger::ALERT:
            case Logger::CRIT:
            case Logger::ERR:
            case Logger::WARN:
                $firephp->warn($line);
                break;
            case Logger::NOTICE:
            case Logger::INFO:
            case Logger::DEBUG:
            default:
                //$firephp->trace() and $firephp->log() send large amounts of data (the trace) to FirePhp rendering
                //it unusable. Therefore only $firephp->info() is used.
//                $firephp->log($line, $label);
                $firephp->info($line);
                break;
        }
    }
}
