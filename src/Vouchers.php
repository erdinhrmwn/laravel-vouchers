<?php

namespace BeyondCode\Vouchers;

use BeyondCode\Vouchers\Exceptions\VoucherAlreadyMaxUsed;
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherInvalid;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Database\Eloquent\Model;

class Vouchers
{
    private VoucherGenerator $generator;

    private Voucher $voucherModel;

    public function __construct(VoucherGenerator $generator)
    {
        $this->generator    = $generator;
        $this->voucherModel = app(config('vouchers.model', Voucher::class));
    }

    /**
     * Generate the specified amount of codes and return
     * an array with all the generated codes.
     *
     * @return string[]
     */
    public function generate(int $amount = 1): array
    {
        $codes = [];

        for ($i = 1; $i <= $amount; $i++) {
            $codes[] = $this->getUniqueVoucher();
        }

        return $codes;
    }

    /**
     * @param  int  $amount
     * @param  array  $data
     * @param  \DateTime|null  $expires_at
     * @param  int  $use_count
     * @return Voucher[]
     */
    public function create(Model $model, $amount = 1, $data = [], $expires_at = null, $use_count = 1): array
    {
        $vouchers = [];

        foreach ($this->generate($amount) as $voucherCode) {
            $vouchers[] = $this->voucherModel->create([
                'model_id'   => $model->getKey(),
                'model_type' => $model->getMorphClass(),
                'code'       => $voucherCode,
                'data'       => $data,
                'expires_at' => $expires_at,
                'use_count'  => $use_count,
                'used_count' => 0,
            ]);
        }

        return $vouchers;
    }

    /**
     * @throws VoucherExpired
     * @throws VoucherAlreadyMaxUsed
     * @throws VoucherInvalid
     */
    public function check(string $code): Voucher
    {
        /** @var Voucher $voucher */
        $voucher = $this->voucherModel->whereCode($code)->first();

        if (is_null($voucher)) {
            throw VoucherInvalid::withCode($code);
        }

        if ($voucher->isExpired()) {
            throw VoucherExpired::create($voucher);
        }

        if ($voucher->isMaxUsed()) {
            throw VoucherAlreadyMaxUsed::create($voucher);
        }

        return $voucher;
    }

    /**
     * @throws VoucherExpired
     * @throws VoucherAlreadyMaxUsed
     * @throws VoucherInvalid
     */
    public function isValid(string $code): bool
    {
        try {
            $this->check($code);
        } catch (VoucherInvalid $exception) {
            return false;
        } catch (VoucherExpired $exception) {
            return false;
        } catch (VoucherAlreadyMaxUsed $exception) {
            return false;
        }

        return true;
    }

    protected function getUniqueVoucher(): string
    {
        $voucher = $this->generator->generateUnique();

        while ($this->voucherModel->whereCode($voucher)->exists()) {
            $voucher = $this->generator->generateUnique();
        }

        return $voucher;
    }
}
