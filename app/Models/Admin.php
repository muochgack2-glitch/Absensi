<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use Notifiable;
    
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    protected $fillable = [
        'username',
        'password',
        'nama_petugas',
        'theme_preference',
    ];

    protected $hidden = [
        'password',
    ];
}
