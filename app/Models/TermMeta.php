<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermMeta extends Model
{
    use HasFactory;
    protected $table = 'termmeta';
    protected $guarded = ['meta_id'];
    public $timestamps = false;
}
