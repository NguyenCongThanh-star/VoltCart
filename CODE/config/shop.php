<?php

/*
|--------------------------------------------------------------------------
| Shop settings
|--------------------------------------------------------------------------
| Read with config('shop.bank_transfer.account_number') and so on.
*/

return [

    // Simulated bank transfer (C-15). These are demo values: no real account,
    // no money is moved. The admin confirms the order by hand.
    'bank_transfer' => [
        'bank_name' => 'VoltBank (demo)',
        'account_number' => '0000 1111 2222',
        'account_name' => 'VOLTCART DEMO',
        'note' => 'Use the order code as the transfer description. This is a simulated payment.',
    ],

];
