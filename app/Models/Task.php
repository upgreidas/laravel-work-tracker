<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'assignee_id',
        'project_id',
        'name',
        'description',
        'due_date',
        'status',
    ];
}
