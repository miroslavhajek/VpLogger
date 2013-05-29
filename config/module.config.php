<?php
return array(
    'service_manager'   => array(
        'invokables'        => array(
        ),
        'factories'         => array(
            'VpLogger\logger'                   => 'VpLogger\Log\LoggerFactory',
            'VpLogger\writer_plugin_manager'    => 'VpLogger\Log\WriterPluginManagerFactory',
            'VpLogger\request_start'            => 'VpLogger\Request\RequestStartFactory',
            'VpLogger\request_id'               => 'VpLogger\Request\RequestIdFactory',
        ),
    ),

    //Logger configuration
    'VpLogger\logger' => array(
        'listener' => array (
            'attach' => array (
                //array('*', 'log'), //log 'log' events
                //Default is listening for all events, filtering on source/event is performed using the Events filter
                array('*', '*'), //listen for all events
                //array('*', 'log'), //listen for log events
            ),
        ),
        'writers' => array (
//            Writers from writer plugin manager
//            'default_log'   => array(
//                'priority'  => 1,
//                'options'   => array(
//                    'log_dir'       => null,
//                    'log_name'      => 'application',
//                    'priority_min'  => null,
//                    'priority_max'  => \VpLogger\Log\Logger::DEBUG,
//                    'events'        => array(
//                        'allow'         => array(
//                            'all'           => array('*', '*'),
//                        ),
//                        'block'         => array(
//                        ),
//                    ),
//                ),
//            ),
//            'firephp'       => array(),
        ),
        'writer_plugin_manager' => array(
            'factories'     => array(
                'default_log'               => 'VpLogger\Log\LogFileWriterFactory',
                'perf_log'                  => 'VpLogger\Log\PerfLogFileWriterFactory',
            ),
        ),
    ),


);
