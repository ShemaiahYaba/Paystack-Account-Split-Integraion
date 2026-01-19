<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array'
    ];

    /**
     * Get the parent transactionable model (donation, dues, etc).
     */
    public function transactionable()
    {
        return $this->morphTo();
    }
}
