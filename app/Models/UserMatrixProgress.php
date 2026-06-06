<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMatrixProgress extends Model
{
    // Define the table name explicitly
    protected $table = 'user_matrix_progress';

    // Allow these fields to be filled
    protected $fillable = ['user_id', 'rank_level', 'tier_1_count', 'tier_2_count', 'tier_3_count'];
}