<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meeting'; 

    protected $fillable = [
        'id',
        'meeting_link',
        'host_id',
        'start_date',
        'start_time',
        'description',
        'password',
        'status',
        'created_at',
        'updated_at',
    ];
}
