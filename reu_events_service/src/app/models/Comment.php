<?php
namespace reu\events\app\models;

class Comment extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'comments'; 
    protected $primaryKey = 'id';
    public  $incrementing = false;
    public $keyType='string';     
 
} 