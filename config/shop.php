<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hồ sơ cửa hàng
    |--------------------------------------------------------------------------
    |
    | One place for the shop's name, address and contact details. The contact
    | page and the Store schema on the home page both read from here, so the
    | details search engines see always match the details customers see —
    | search engines treat a mismatched name/address/phone as a different
    | business. Override per environment with the SHOP_* variables in .env.
    |
    */

    'legal_name' => env('SHOP_LEGAL_NAME', 'phuonghihi'),

    'phone' => env('SHOP_PHONE', '0900 300 300'),

    'email' => env('SHOP_EMAIL', 'hotro@phuonghihi.example'),

    'address' => [
        'street' => env('SHOP_STREET', '123 Đường Lê Lợi'),
        'district' => env('SHOP_DISTRICT', 'Quận 1'),
        'city' => env('SHOP_CITY', 'TP. Hồ Chí Minh'),
        'country' => env('SHOP_COUNTRY', 'VN'),
    ],

    'opening_hours' => env('SHOP_OPENING_HOURS', '8:00 - 21:00, tất cả các ngày trong tuần'),

    /**
     * Machine-readable form of the line above, for schema.org.
     */
    'opening_hours_schema' => env('SHOP_OPENING_HOURS_SCHEMA', 'Mo-Su 08:00-21:00'),

    'price_range' => env('SHOP_PRICE_RANGE', '5.000.000đ - 40.000.000đ'),

];
