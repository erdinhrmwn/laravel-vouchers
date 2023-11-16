<?php

namespace BeyondCode\Vouchers\Tests\Models;

use BeyondCode\Vouchers\Traits\HasVouchers;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasVouchers;

    protected $fillable = ['name'];
}
