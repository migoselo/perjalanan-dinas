<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodationItem extends Model
{
    use HasFactory;

    protected $table = 'accommodation_items';

    protected $fillable = [
        'travel_id',
        'name',
        'nights',
        'price',
    ];

    protected $casts = [
        'nights' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Relasi ke Travel
     */
    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }
}
