<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseIndent extends Model
{
    use HasFactory;

    protected $fillable = [
        'indent_no',
        'indent_date',
        'upload_path',
        'upload_original_name',
        'status',
        'remarks',
        'branch_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'indent_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseIndentItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}


