<?php
namespace reu\events\app\models;

class Comment extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'comments'; 
    protected $primaryKey = 'id';
    public  $incrementing = true;   
 
    public function items() {
       return $this->hasMany('\reu\comments\app\models\Comment', 'id');
    }
 
 
} 