<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'type',
        'user_id',
        'team_id',
        'path',
        'configuration'
    ];
}
