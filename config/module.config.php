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
                //Default is listening for all events, filtering on source/event is performed using the Events filter
                'all'   => array('*', '*'), //listen for all events
            ),
            'default_priority'  => \VpLogger\Log\Logger::DEBUG,
        ),
        'writers' => array (
//            Writers from writer plugin manager
        ),
        'writer_plugin_manager' => array(
            'factories'     => array(
            ),
        ),
    ),


);
