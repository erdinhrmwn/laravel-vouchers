<?php

namespace BeyondCode\Vouchers\Events;

use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Queue\SerializesModels;

class VoucherRedeemed
{
    use SerializesModels;

    public $user;

    public $voucher;

    public function __construct($user, Voucher $voucher)
    {
        $this->user    = $user;
        $this->voucher = $voucher;
    }
}
