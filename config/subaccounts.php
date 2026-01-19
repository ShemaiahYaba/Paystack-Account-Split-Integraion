// config/subaccounts.php
<?php

return [
    'paystack' => [
        // Map state names to Paystack subaccount codes
        // Format: 'State Name' => 'SUBACCT_xxxxxxxxx'

        'Abia' => env('PAYSTACK_SUBACCT_ABIA'),
        'Adamawa' => env('PAYSTACK_SUBACCT_ADAMAWA'),
        'Akwa Ibom' => env('PAYSTACK_SUBACCT_AKWA_IBOM'),
        'Anambra' => env('PAYSTACK_SUBACCT_ANAMBRA'),
        'Bauchi' => env('PAYSTACK_SUBACCT_BAUCHI'),
        'Bayelsa' => env('PAYSTACK_SUBACCT_BAYELSA'),
        'Benue' => env('PAYSTACK_SUBACCT_BENUE'),
        'Borno' => env('PAYSTACK_SUBACCT_BORNO'),
        'Cross River' => env('PAYSTACK_SUBACCT_CROSS_RIVER'),
        'Delta' => env('PAYSTACK_SUBACCT_DELTA'),
        'Ebonyi' => env('PAYSTACK_SUBACCT_EBONYI'),
        'Edo' => env('PAYSTACK_SUBACCT_EDO'),
        'Ekiti' => env('PAYSTACK_SUBACCT_EKITI'),
        'Enugu' => env('PAYSTACK_SUBACCT_ENUGU'),
        'FCT' => env('PAYSTACK_SUBACCT_FCT'), // Federal Capital Territory (Abuja)
        'Gombe' => env('PAYSTACK_SUBACCT_GOMBE'),
        'Imo' => env('PAYSTACK_SUBACCT_IMO'),
        'Jigawa' => env('PAYSTACK_SUBACCT_JIGAWA'),
        'Kaduna' => env('PAYSTACK_SUBACCT_KADUNA'),
        'Kano' => env('PAYSTACK_SUBACCT_KANO'),
        'Katsina' => env('PAYSTACK_SUBACCT_KATSINA'),
        'Kebbi' => env('PAYSTACK_SUBACCT_KEBBI'),
        'Kogi' => env('PAYSTACK_SUBACCT_KOGI'),
        'Kwara' => env('PAYSTACK_SUBACCT_KWARA'),
        'Lagos' => env('PAYSTACK_SUBACCT_LAGOS'),
        'Nasarawa' => env('PAYSTACK_SUBACCT_NASARAWA'),
        'Niger' => env('PAYSTACK_SUBACCT_NIGER'),
        'Ogun' => env('PAYSTACK_SUBACCT_OGUN'),
        'Ondo' => env('PAYSTACK_SUBACCT_ONDO'),
        'Osun' => env('PAYSTACK_SUBACCT_OSUN'),
        'Oyo' => env('PAYSTACK_SUBACCT_OYO'),
        'Plateau' => env('PAYSTACK_SUBACCT_PLATEAU'),
        'Rivers' => env('PAYSTACK_SUBACCT_RIVERS'),
        'Sokoto' => env('PAYSTACK_SUBACCT_SOKOTO'),
        'Taraba' => env('PAYSTACK_SUBACCT_TARABA'),
        'Yobe' => env('PAYSTACK_SUBACCT_YOBE'),
        'Zamfara' => env('PAYSTACK_SUBACCT_ZAMFARA'),
    ],
];
