<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID';
    protected $guarded = ['ID'];
    protected $appends = ['cats', 'featured_image', 'template_id'];
    public $timestamps = false;
    protected $hidden = [
        'post_password',
    ];
    public function getCatsAttribute() {
        return \App\Models\TermRelationship::select(['term_taxonomy_id'])->where(['object_id' => $this->ID])->get()->pluck('term_taxonomy_id');
    }
    public function getFeaturedImageAttribute() {
        return get_post_meta($this->ID, '__featured_image', true);
    }
    public function getTemplateIdAttribute() {
        return get_post_meta($this->ID, '__template_id', true);
    }
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
