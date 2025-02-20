<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    

    // Add the fields that you want to allow mass assignment for
    protected $fillable = [
        'product_id',
        'quantity',
        'price',
        'total',
        'sale_id',
    ];

    // Define the relationship to the Product model if needed
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function sale(){
        return $this->belongsTo(Sale::class);
    }
}