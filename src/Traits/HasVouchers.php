<?php

namespace BeyondCode\Vouchers\Traits;

use BeyondCode\Vouchers\Models\Voucher;
use BeyondCode\Vouchers\Facades\Vouchers;

trait HasVouchers
{
    /**
     * Set the polymorphic relation.
     *
     * @return mixed
     */
    public function vouchers()
    {
        return $this->morphMany(config('vouchers.model', Voucher::class), 'model');
    }

    /**
     * @param int $amount
     * @param array $data
     * @param null $expires_at
     * @param int $use_count
     * @return array
     */
    public function createVouchers(int $amount, array $data = [], $expires_at = null, int $use_count = 1): array
    {
        return Vouchers::create($this, $amount, $data, $expires_at, $use_count);
    }

    /**
     * @param array $data
     * @param null $expires_at
     * @return Voucher
     */
    public function createVoucher(array $data = [], $expires_at = null): Voucher
    {
        return $this->createVouchers(1, $data, $expires_at)[0];
    }
}
