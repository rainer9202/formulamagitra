<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            $user = User::find($model->user_id);
            if ($user) $user->delete();
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nif',
        'description',
        'city',
        'province',
        'address',
        'postal_code',
        'phone',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
