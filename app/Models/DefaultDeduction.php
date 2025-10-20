<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'type',
        'description',
        'is_active',
        'is_statutory',
        'sort_order'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_statutory' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope to get only active deductions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only statutory deductions
     */
    public function scopeStatutory($query)
    {
        return $query->where('is_statutory', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Calculate the deduction amount based on type and base amount
     */
    public function calculateAmount($baseAmount = 0)
    {
        if ($this->type === 'percentage') {
            return round($baseAmount * ($this->amount / 100), 2);
        }
        
        return $this->amount;
    }
}
