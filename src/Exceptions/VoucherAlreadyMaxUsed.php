<?php

namespace BeyondCode\Vouchers\Exceptions;

use BeyondCode\Vouchers\Models\Voucher;
use Exception;

class VoucherAlreadyMaxUsed extends Exception
{
    protected $message = 'The voucher code was already reached its usage limit.';

    protected $voucher;

    public static function create(Voucher $voucher): VoucherAlreadyMaxUsed
    {
        return new static($voucher);
    }

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }
}
