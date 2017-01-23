<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'development',
        'certificate' =>'/path/to/certificate.pem',
        'passPhrase'  =>'password',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'AIzaSyDtxjf0MagAdbElfJ9bkZaH7DfqsFHTHmw',
        'service'     =>'gcm'
    )

);