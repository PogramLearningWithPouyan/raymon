<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tasks';

    protected $fillable = [
        'task_name',
        'description',
        'assigned_to',
        'due_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
