<?php
return array(
    'service_manager'   => array(
        'invokables'        => array(
        ),
        'factories'         => array(
            'VpLogger\logger'                   => 'VpLogger\Log\LoggerFactory',
            'VpLogger\writer_plugin_manager'    => 'VpLogger\Log\WriterPluginManagerFactory',
        ),
    ),

    //Logger configuration
    'VpLogger\logger' => array(
        'listener' => array (
            'attach' => array (
                //array('*', 'log'), //log 'log' events
                //array('*', '*'), //log all events
            ),
        ),
        'writers' => array (
//            writers from writer plugin manager
//            'default_log'   => array(
//                'priority'  => 1,
//                'options'   => array(
//                    'log_dir'   => '',
//                    'priority'  => \Vivo\Log\Logger::DEBUG,
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
