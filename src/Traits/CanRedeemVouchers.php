<?php

namespace BeyondCode\Vouchers\Traits;

use BeyondCode\Vouchers\Events\VoucherRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyMaxUsed;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherInvalid;
use BeyondCode\Vouchers\Facades\Vouchers;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CanRedeemVouchers
{
    /**
     * @throws VoucherInvalid
     * @throws VoucherExpired
     * @throws VoucherAlreadyRedeemed
     * @throws VoucherAlreadyMaxUsed
     */
    public function redeemCode(string $code): Voucher
    {
        $voucher = Vouchers::check($code);

        if ($voucher->isExpired()) {
            throw VoucherExpired::create($voucher);
        }

        if ($voucher->use_count != 1) {
            if ($voucher->isMaxUsed()) {
                throw VoucherAlreadyMaxUsed::create($voucher);
            }

            $voucher->increment('used_count');
        }

        if ($voucher->users()->wherePivot('user_id', $this->id)->exists()) {
            throw VoucherAlreadyRedeemed::create($voucher);
        }

        $this->vouchers()->attach($voucher, [
            'redeemed_at' => now(),
        ]);

        event(new VoucherRedeemed($this, $voucher));

        return $voucher;
    }

    /**
     * @throws VoucherInvalid
     * @throws VoucherExpired
     * @throws VoucherAlreadyRedeemed
     * @throws VoucherAlreadyMaxUsed
     */
    public function redeemVoucher(Voucher $voucher): Voucher
    {
        return $this->redeemCode($voucher->code);
    }

    /**
     * @return BelongsToMany
     */
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class)->withPivot('redeemed_at');
    }
}
