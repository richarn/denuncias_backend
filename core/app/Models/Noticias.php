<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Noticias extends Model
{
  use HasApiTokens, HasFactory, Notifiable;

  protected $table = 'noticias';


  protected $fillable = [
    'descripcion', 'fecha',
    'estado', 'titulo'
  ];

  // relacion - inner join
  public function imagenes() {
    return $this->belongsToMany(Imagenes::class, 'imagen_noticias', 'id_noticia', 'id_imagen');
    // tabla con los datos - imagenes
    // tabla intermedia imagen_noticias -> tiene la relacion con la noticia id_noticia
    // id_noticia - el campo en la table intermedia
    // id -> el primary key de la tabla actual
  }
  
}
