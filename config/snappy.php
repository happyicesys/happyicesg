<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltopdf',
        'timeout' => false,
        'no-images' => false,
        'encoding' => 'utf-8',
        'disable-smart-shrinking' => true,
        'margin-top' => 0,
        'margin-left' => 0,
        'margin-right' => 0,
        'margin-bottom' => 0,
        'options' => array(
                            'print-media-type' => true,
                            'outline' => true,
                            // 'dpi' => 96,
                            'dpi' => 100,
                            'page-size' => 'A4',
                            'enable-javascript' => true,
                            'javascript-delay' => 3000,
                            'enable-smart-shrinking' => true
                        ),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
    ),


);
