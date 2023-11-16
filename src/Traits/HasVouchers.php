<?php

namespace BeyondCode\Vouchers\Traits;

use BeyondCode\Vouchers\Facades\Vouchers;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVouchers
{
    /**
     * Set the polymorphic relation.
     *
     * @return MorphMany
     */
    public function vouchers()
    {
        return $this->morphMany(config('vouchers.model', Voucher::class), 'model');
    }

    /**
     * @param  int  $amount
     * @param  array  $data
     * @param  \DateTime|null  $expires_at
     * @param  int  $use_count
     * @return Voucher[]
     */
    public function createVouchers($amount, $data = [], $expires_at = null, $use_count = 1)
    {
        return Vouchers::create($this, $amount, $data, $expires_at, $use_count);
    }

    /**
     * @param  array  $data
     * @param  \DateTime|null  $expires_at
     * @return Voucher
     */
    public function createVoucher($data = [], $expires_at = null)
    {
        return $this->createVouchers(1, $data, $expires_at)[0];
    }
}
