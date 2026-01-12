<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportItem extends Model
{
    use HasFactory;

    protected $table = 'transport_items';

    protected $fillable = [
        'travel_id',
        'mode',
        'description',
        'amount',
    ];

    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }
}