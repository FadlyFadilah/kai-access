<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'kelas'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
