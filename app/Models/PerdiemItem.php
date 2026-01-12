<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerdiemItem extends Model
{
    use HasFactory;

    protected $table = 'perdiem_items';

    protected $fillable = [
        'travel_id',
        'city',
        'days',
        'amount',
    ];

    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }
}