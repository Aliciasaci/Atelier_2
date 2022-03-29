<?php
namespace reu\authentification\app\models;
use \Illuminate\Database\Eloquent\Model as EloquentModel;

class User extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'users';
    protected $primaryKey = 'id';
    public  $incrementing = false;
    public $keyType='string';           

} 