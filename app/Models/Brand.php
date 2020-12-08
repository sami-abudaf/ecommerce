<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Brand extends Model
{

   protected $table = 'brands';
    //`id`, `name`, `active`, `photo`, `created_at`, `updated_at`
    protected $fillable= ['name','active','photo','created_at','updated_at'];


    public function  scopeActive($query){

        return $query->where('active',1);
    }

    public function getActive(){
        return   $this -> active == 1 ? 'مفعل'  : 'غير مفعل';
    }


    public function getPhotoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }

    public  function  scopeSelection($query){
        return $query->select('id', 'active','name','photo');
    }
}
