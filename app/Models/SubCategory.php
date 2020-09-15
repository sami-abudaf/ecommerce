<?php

namespace App\Models;

use App\Observers\MainCategoryObserver;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategory;

class SubCategory extends Model
{
    protected  $table ='sub_categories';
    protected $fillable = [
        'category_id','parent_id','translation_lang', 'translation_of', 'name', 'slug', 'photo', 'active', 'created_at', 'updated_at'
    ];

    protected static function boot() {
        parent::boot();
        MainCategory::observe(MainCategoryObserver::class);
    }

    public function scopeActive($query){
        return $query->where('active' ,1);
    }

    public  function scopeSelection($query){

        return $query->select('id','category_id','parent_id', 'translation_lang', 'name', 'slug', 'photo', 'active', 'translation_of');
    }

    public function getActive(){
        return   $this -> active == 1 ? 'مفعل'  : 'غير مفعل';
    }

    public function scopeDefaultCategory($query)
    {
        return $query->where('translation_of',0);
    }
    public function getPhotoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }
    //relationship  get all translation categories
    public function categories()
    {
        return $this->hasMany(self::class, 'translation_of');
    }


    public function  vendors(){
        return $this->hasMany('App\Models\Vendor','category_id','id');
    }
    //get main category of subcategory
    public  function mainCategory(){
        return $this -> belongsTo(MainCategory::class,'category_id','id');
    }
}
