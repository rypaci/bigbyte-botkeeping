<?php

return array(


    'pdf' => array(
        'enabled' => true,
        // 'binary'  => '/usr/local/bin/wkhtmltopdf',
        
        'binary'  => (strtoupper(substr(PHP_OS, 0, 3) === 'WIN')) 
            ? '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'
            : '~/wkhtmltox/wkhtmltox/bin/wkhtmltopdf',
            
        'timeout' => false,
        'options' => array(
            'margin-top'    => 10,
            'margin-left'   => 10,
            'margin-right'  => 10,
            'margin-bottom' => 10,
        ),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        // 'binary'  => '/usr/local/bin/wkhtmltoimage',
        
        'binary'  => (strtoupper(substr(PHP_OS, 0, 3) === 'WIN')) 
            ? '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage"'
            : '~/wkhtmltox/wkhtmltox/bin/wkhtmltoimage',
            
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
