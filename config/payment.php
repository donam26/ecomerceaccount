<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình cổng thanh toán SePay
    |--------------------------------------------------------------------------
    |
    | Các cấu hình để kết nối với dịch vụ thanh toán SePay
    |
    */
    'pattern' => 'SEVQR',
    
    'sepay' => [
        'account' => env('SEPAY_ACCOUNT', '103870429701'),
        'bank' => env('SEPAY_BANK', 'VietinBank'),
        'token' => env('SEPAY_TOKEN', ''),
        'webhook_url' => env('SEPAY_WEBHOOK_URL', '/api/webhooks/sepay'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cấu hình nạp thẻ qua TheSieuRe
    |--------------------------------------------------------------------------
    |
    | Các cấu hình để kết nối với dịch vụ nạp thẻ TheSieuRe
    |
    */
    'thesieure' => [
        'partner_id' => env('TSR_PARTNER_ID', ''),
        'partner_key' => env('TSR_PARTNER_KEY', ''),
        'url' => env('TSR_API_URL', 'https://thesieure.com/chargingws/v2'),
        'webhook_url' => env('TSR_WEBHOOK_URL', '/api/webhooks/thesieure'),
        
        // Tỷ lệ khấu trừ cho từng loại thẻ
        'rate' => [
            'VIETTEL' => 0.02, // 2%
            'MOBIFONE' => 0.02,
            'VINAPHONE' => 0.02,
            'VIETNAMOBILE' => 0.03, // 3%
            'ZING' => 0.03,
            'GATE' => 0.03,
        ],
    ],
]; 