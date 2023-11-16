<?php

namespace BeyondCode\Vouchers\Tests;

use BeyondCode\Vouchers\Events\VoucherRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyMaxUsed;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyRedeemed;
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherInvalid;
use BeyondCode\Vouchers\Facades\Vouchers;
use BeyondCode\Vouchers\Tests\Models\Item;
use BeyondCode\Vouchers\Tests\Models\User;
use Illuminate\Support\Facades\Event;

class CanRedeemVouchersTest extends TestCase
{
    /** @test */
    public function it_throws_an_invalid_voucher_exception_for_invalid_codes()
    {
        $this->expectException(VoucherInvalid::class);

        $user = User::first();

        $user->redeemCode('invalid');
    }

    /** @test */
    public function it_attaches_users_when_they_redeem_a_code()
    {
        $user = User::find(1);
        $item = Item::create(['name' => 'Foo']);

        $vouchers = Vouchers::create($item);
        $voucher  = $vouchers[0];

        $user->redeemCode($voucher->code);

        $this->assertCount(1, $user->vouchers);

        $userVouchers = $user->vouchers()->first();
        $this->assertNotNull($userVouchers->pivot->redeemed_at);
    }

    /** @test */
    public function users_can_not_redeem_the_same_voucher_twice()
    {
        $this->expectException(VoucherAlreadyRedeemed::class);

        $user = User::find(1);
        $item = Item::create(['name' => 'Foo']);

        $vouchers = Vouchers::create($item);
        $voucher  = $vouchers[0];

        $user->redeemCode($voucher->code);
        $user->redeemCode($voucher->code);
    }

    /** @test */
    public function users_can_not_redeem_expired_vouchers()
    {
        $this->expectException(VoucherExpired::class);

        $user = User::find(1);
        $item = Item::create(['name' => 'Foo']);

        $vouchers = Vouchers::create($item, 1, [], today()->subDay());
        $voucher  = $vouchers[0];

        $user->redeemCode($voucher->code);
    }

    /** @test */
    public function users_can_redeem_voucher_models()
    {
        $this->expectException(VoucherAlreadyRedeemed::class);

        $user = User::find(1);
        $item = Item::create(['name' => 'Foo']);

        $vouchers = Vouchers::create($item);
        $voucher  = $vouchers[0];

        $user->redeemVoucher($voucher);
        $user->redeemVoucher($voucher);
    }

    /** @test */
    public function users_can_not_redeem_max_used_vouchers()
    {
        $this->expectException(VoucherAlreadyMaxUsed::class);

        $item = Item::create(['name' => 'Foo']);

        $vouchers = Vouchers::create($item, 1, [], today()->addDay(), 2);
        $voucher  = $vouchers[0];

        $user = User::find(1);
        $user->redeemVoucher($voucher);

        $user1 = User::find(2);
        $user1->redeemVoucher($voucher);

        $user2 = User::find(3);
        $user2->redeemVoucher($voucher);
    }

    /** @test */
    public function redeeming_vouchers_fires_an_event()
    {
        Event::fake();

        $user = User::find(1);
        $item = Item::create(['name' => 'Foo']);

        $vouchers = Vouchers::create($item);
        $voucher  = $vouchers[0];

        $user->redeemVoucher($voucher);

        Event::assertDispatched(VoucherRedeemed::class, function ($e) use ($user, $voucher) {
            return $e->user->id === $user->id && $e->voucher->id === $voucher->id;
        });
    }
}
