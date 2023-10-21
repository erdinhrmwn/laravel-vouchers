<?php

namespace BeyondCode\Vouchers\Traits;

use BeyondCode\Vouchers\Exceptions\VoucherAlreadyMaxUsed;
use BeyondCode\Vouchers\Facades\Vouchers;
use BeyondCode\Vouchers\Models\Voucher;
use BeyondCode\Vouchers\Events\VoucherRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherIsInvalid;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyRedeemed;

trait CanRedeemVouchers
{
    /**
     * @param string $code
     * @return Voucher
     * @throws VoucherIsInvalid
     * @throws VoucherAlreadyRedeemed|VoucherExpired|VoucherAlreadyMaxUsed
     */
    public function redeemCode(string $code): Voucher
    {
        $voucher = Vouchers::check($code);

        if ($voucher->isExpired()) {
            throw VoucherExpired::create($voucher);
        }

        if($voucher->use_count != 1){
            if($voucher->isMaxUsed()){
                throw VoucherAlreadyMaxUsed::create($voucher);
            }
            $voucher->used_count++;
        }else{
            if ($voucher->users()->wherePivot('user_id', $this->id)->exists()) {
                throw VoucherAlreadyRedeemed::create($voucher);
            }
        }

        $this->vouchers()->attach($voucher, [
            'redeemed_at' => now()
        ]);

        event(new VoucherRedeemed($this, $voucher));

        return $voucher;
    }

    /**
     * @param Voucher $voucher
     * @return Voucher
     * @throws VoucherIsInvalid
     * @throws VoucherAlreadyRedeemed|VoucherAlreadyMaxUsed
     * @throws VoucherExpired
     */
    public function redeemVoucher(Voucher $voucher): Voucher
    {
        return $this->redeemCode($voucher->code);
    }

    /**
     * @return mixed
     */
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class)->withPivot('redeemed_at');
    }
}
