<?php

namespace BeyondCode\Vouchers\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Voucher extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'code',
        'data',
        'expires_at',
        'use_count',
        'used_count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data'       => 'collection',
        'expires_at' => 'date',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('vouchers.table', 'vouchers');
    }

    /**
     * Get the users who redeemed this voucher.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('vouchers.user_model'), config('vouchers.relation_table'))->withPivot('redeemed_at');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
    }

    /**
     * Check if code is not expired.
     */
    public function isNotExpired(): bool
    {
        return !$this->isExpired();
    }

    public function isMaxUsed(): bool
    {
        return $this->use_count != null && $this->use_count <= $this->used_count;
    }
}
