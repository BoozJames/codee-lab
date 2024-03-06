<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;

    protected $fillable = [
        'srcode',
        'first_name',
        'middle_name',
        'last_name',
        'extra_name',
        'college_id',
        'campus',
        'programs',
        'courses_array',
    ];

    protected $casts = [
        'courses_array' => 'array',
    ];
}