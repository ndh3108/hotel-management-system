<?php

/**
 * MoMo Payment Configuration (V2 API)
 * User's Specific Verified Credentials
 */
return [
    'MomoApiUrl' => 'https://test-payment.momo.vn/v2/gateway/api/create',
    
    // User's specific Sandbox credentials
    'PartnerCode' => 'MOMOBKUN20180529',
    'AccessKey'   => 'klm05TvNBzhg7h7j',
    'SecretKey'   => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',

    // Using HTTPS placeholders to bypass strict sandbox validation (diagnostics)
    'ReturnUrl'   => 'https://momo.vn',
    'NotifyUrl'   => 'https://momo.vn',
    
    'RequestType' => 'captureWallet',
];
