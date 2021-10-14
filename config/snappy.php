<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltopdf',
        'timeout' => false,
        'options' => array(
                            'print-media-type' => true,
                            'outline' => true,
                            // 'dpi' => 96,
                            'dpi' => 85,
                            'page-size' => 'A4',
                            'zoom' => 1.5,
                        ),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
    ),


);
