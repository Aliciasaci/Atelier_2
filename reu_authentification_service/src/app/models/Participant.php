<?php
namespace reu\authentification\app\models;

class Participant extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'Participant';
    protected $primaryKey = 'id';
    public  $incrementing = false;
    public $keyType='string';           

} 