<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $table = 'settings'; // Explicitly tell Laravel the table name
    protected $fillable = ['qr_scanner_img']; // Allow this column to be updated
}
