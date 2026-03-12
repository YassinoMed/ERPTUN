<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteItem extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'invoice_product_id',
        'product_id',
        'quantity',
        'description',
    ];

    public function deliveryNote()
    {
        return $this->hasOne(DeliveryNote::class, 'id', 'delivery_note_id');
    }

    public function invoiceProduct()
    {
        return $this->hasOne(InvoiceProduct::class, 'id', 'invoice_product_id');
    }

    public function product()
    {
        return $this->hasOne(ProductService::class, 'id', 'product_id');
    }
}
