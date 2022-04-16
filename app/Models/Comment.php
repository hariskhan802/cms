<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // public function getCreatedAtAttribute($date)
    // {
    //     $dateC = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(get_admin_panel_date());
    //     $timeC = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(get_admin_panel_time());
    //     return $date != '' ? $dateC.' at '.$timeC  : $date;
    // }

    // public function getUpdatedAtAttribute($date)
    // {
    //     $dateC = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(get_admin_panel_date());
    //     $timeC = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format(get_admin_panel_time());
    //     return $date != '' ? $dateC.' at '.$timeC  : $date;
    // }

    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function post() {
        return $this->belongsTo(\App\Models\Post::class, 'post_id');
    }
}
