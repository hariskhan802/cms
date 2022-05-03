<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    use HasFactory;
    protected $guarded = ['term_taxonomy_id'];
    protected $table = 'term_taxonomy';
    public $timestamps = false;
}
