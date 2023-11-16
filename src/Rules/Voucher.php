<?php

namespace BeyondCode\Vouchers\Rules;

use BeyondCode\Vouchers\Exceptions\VoucherAlreadyMaxUsed;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherInvalid;
use BeyondCode\Vouchers\Facades\Vouchers;
use Illuminate\Contracts\Validation\Rule;

class Voucher implements Rule
{
    protected bool $isInvalid = false;

    protected bool $isExpired = false;

    protected bool $wasRedeemed = false;

    protected bool $maxUsed = false;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $voucher = Vouchers::check($value);

            // Check if the voucher is already redeemed
            if (auth()->check() && $voucher->users()->wherePivot('user_id', auth()->id())->exists()) {
                throw VoucherAlreadyRedeemed::create($voucher);
            }
        } catch (VoucherInvalid $exception) {
            $this->isInvalid = true;
            return false;
        } catch (VoucherExpired $exception) {
            $this->isExpired = true;
            return false;
        } catch (VoucherAlreadyMaxUsed $exception) {
            $this->maxUsed = true;
            return false;
        } catch (VoucherAlreadyRedeemed $exception) {
            $this->wasRedeemed = true;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->wasRedeemed) {
            return trans('vouchers::validation.code_redeemed');
        }

        if ($this->isExpired) {
            return trans('vouchers::validation.code_expired');
        }

        if ($this->maxUsed) {
            return trans('vouchers::validation.code_max_used');
        }

        if ($this->isInvalid) {
            return trans('vouchers::validation.code_invalid');
        }

        return 'Unexpected error';
    }
}
