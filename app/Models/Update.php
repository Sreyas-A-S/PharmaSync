<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    protected $fillable = [
        'user_id',
        'task',
        'description',
        'attachment_path'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
