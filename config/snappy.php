<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltopdf-amd64',
        'timeout' => false,
        'options' => array(
                            'print-media-type' => true,
                            'outline' => true,
                            // 'dpi' => 96,
                            'dpi' => 85,
                            'page-size' => 'A4',
                        ),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltoimage-amd64',
        'timeout' => false,
        'options' => array(),
    ),


);
