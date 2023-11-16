<?php

namespace BeyondCode\Vouchers\Rules;

use BeyondCode\Vouchers\Exceptions\VoucherAlreadyMaxUsed;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherInvalid;
use BeyondCode\Vouchers\Facades\Vouchers;
use Illuminate\Contracts\Validation\ValidationRule;

class Voucher implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate($attribute, $value, $fail): void
    {
        try {
            $voucher = Vouchers::check($value);

            // Check if the voucher was already redeemed
            if (auth()->check() && $voucher->users()->wherePivot('user_id', auth()->id())->exists()) {
                throw VoucherAlreadyRedeemed::create($voucher);
            }
        } catch (VoucherInvalid $exception) {
            $fail(trans('vouchers::validation.code_invalid'));
        } catch (VoucherExpired $exception) {
            $fail(trans('vouchers::validation.code_expired'));
        } catch (VoucherAlreadyMaxUsed $exception) {
            $fail(trans('vouchers::validation.code_max_used'));
        } catch (VoucherAlreadyRedeemed $exception) {
            $fail(trans('vouchers::validation.code_redeemed'));
        }
    }
}
