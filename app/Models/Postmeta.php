<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postmeta extends Model
{
    use HasFactory;
    protected $table = 'postmeta';
    protected $guarded = ['id'];
    public $timestamps = false;
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
}
