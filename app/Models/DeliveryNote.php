<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'invoice_id',
        'customer_id',
        'delivery_date',
        'status',
        'reference',
        'tracking_number',
        'driver_name',
        'vehicle_number',
        'shipping_address',
        'notes',
        'created_by',
    ];

    public static $statuses = [
        'draft' => 'Draft',
        'dispatched' => 'Dispatched',
        'delivered' => 'Delivered',
        'returned' => 'Returned',
        'cancelled' => 'Cancelled',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class, 'delivery_note_id', 'id');
    }

    public function getTotalQuantity()
    {
        return (float) $this->items()->sum('quantity');
    }
}
