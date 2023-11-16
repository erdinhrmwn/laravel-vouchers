<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Voucher Table
    |--------------------------------------------------------------------------
    |
    | Database table name that will be used in migration
    |
    */

    'table' => 'vouchers',

    /*
    |--------------------------------------------------------------------------
    | Voucher Model
    |--------------------------------------------------------------------------
    |
    | Model name that will be used
    |
    */

    'model' => BeyondCode\Vouchers\Models\Voucher::class,

    /*
    |--------------------------------------------------------------------------
    | Voucher Pivot
    |--------------------------------------------------------------------------
    |
    | Database pivot table name for vouchers and users relation
    |
    */

    'relation_table' => 'user_voucher',

    /*
    |--------------------------------------------------------------------------
    | Voucher Characters
    |--------------------------------------------------------------------------
    |
    | List of characters that will be used for voucher code generation.
    |
    */

    'characters' => '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ',

    /*
    |--------------------------------------------------------------------------
    | Voucher Code Prefix
    |--------------------------------------------------------------------------
    |
    | Example: foo
    | Generated Code: foo-ABCD-1NH8
    |
    */

    'prefix' => null,

    /*
    |--------------------------------------------------------------------------
    | Voucher Code Suffix
    |--------------------------------------------------------------------------
    |
    | Example: foo
    | Generated Code: ABCD-1NH8-foo
    |
    */

    'suffix' => null,

    /*
    |--------------------------------------------------------------------------
    | Voucher Code Mask
    |--------------------------------------------------------------------------
    |
    | All asterisks will be removed by random characters.
    |
    */

    'mask' => '****-****',

    /*
    |--------------------------------------------------------------------------
    | Voucher Code Separator
    |--------------------------------------------------------------------------
    |
    | Separator to be used between prefix, code and suffix.
    |
    */

    'separator' => '-',

    /*
    |--------------------------------------------------------------------------
    | Voucher User Model
    |--------------------------------------------------------------------------
    |
    | The user model that belongs to vouchers.
    |
    */

    'user_model' => \App\User::class,
];
