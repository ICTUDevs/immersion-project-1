<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class qrcode extends Model
{
    protected $fillable = [
        'type',
        'code'
    ];
}
