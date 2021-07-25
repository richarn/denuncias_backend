<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Denuncias extends Model
{

  use HasApiTokens, HasFactory, Notifiable;

  protected $table = 'denuncias';

  
  protected $fillable = [
    'fecha_denuncia', 'fecha_solucion',
    'ubicacion', 'estado',
    'descripcion_denuncia', 'descripcion_solucion',
    'id_user'
  ];

  public function imagenes() {
    return $this->belongsToMany(Imagenes::class, 'imagen_denuncias', 'id_denuncia', 'id_imagen');
  }

  public function imagenesSolucionadas() {
    return $this->imagenes()->where('estado', 2);
  }

  public function barrio() {
    return $this->hasOne(Barrios::class, 'id', 'id_barrio');
  }

}
