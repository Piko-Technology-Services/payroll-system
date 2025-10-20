<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductionRule extends Model
{
    protected $fillable = ['name', 'type', 'default_value'];
}
