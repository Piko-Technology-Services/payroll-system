<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'type',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope to get only active earnings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Calculate the earning amount based on type and base amount
     */
    public function calculateAmount($baseAmount = 0)
    {
        if ($this->type === 'percentage') {
            return round($baseAmount * ($this->amount / 100), 2);
        }
        
        return $this->amount;
    }
}
