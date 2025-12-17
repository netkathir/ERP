<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcMaterialInward extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_material_no',
        'material_inward_id',
        'purchase_order_id',
        'supplier_id',
        'branch_id',
        'created_by_id',
        'updated_by_id',
    ];

    public function materialInward()
    {
        return $this->belongsTo(MaterialInward::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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

    public function items()
    {
        return $this->hasMany(QcMaterialInwardItem::class);
    }
}
