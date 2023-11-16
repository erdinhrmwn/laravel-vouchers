<?php

namespace BeyondCode\Vouchers\Exceptions;

use BeyondCode\Vouchers\Models\Voucher;
use Exception;

class VoucherAlreadyRedeemed extends Exception
{
    protected $message = 'The voucher is already redeemed.';

    protected $voucher;

    public static function create(Voucher $voucher): VoucherAlreadyRedeemed
    {
        return new static($voucher);
    }

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }
}
