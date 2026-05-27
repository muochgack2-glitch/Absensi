<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    
    protected $fillable = [
        'username',
        'password',
        'nama_petugas',
    ];

    protected $hidden = [
        'password',
    ];
}
