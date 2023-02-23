<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'provider_id',
        'order_id',
        'number',
        'file',
        'notes',
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
