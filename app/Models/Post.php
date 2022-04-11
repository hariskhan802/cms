<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['cats'];
    public function getCatsAttribute() {
        return \App\Models\PostCategoryRelation::select(['cat_id'])->where(['post_id' => $this->id])->get()->pluck('cat_id');
    }


}
