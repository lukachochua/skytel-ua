<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $table = 'user_info';

    protected $fillable = ['user_id', 'phone_number', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
