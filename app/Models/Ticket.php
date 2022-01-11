<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['station_id', 'schedule_id', 'namaLengkap', 'tujuan', 'harga'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
