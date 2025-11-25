<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'unit_id', 'price', 'gst_rate', 'category', 'branch_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
