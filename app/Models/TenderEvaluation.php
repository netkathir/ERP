<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'evaluation_document',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }
}
