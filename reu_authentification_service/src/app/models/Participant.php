<?php
namespace reu\authentification\app\models;
use \Illuminate\Database\Eloquent\Model as EloquentModel;

class Participant extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'Participant';
    protected $primaryKey = 'id';
    public  $incrementing = false;
    public $keyType='string';           

} 