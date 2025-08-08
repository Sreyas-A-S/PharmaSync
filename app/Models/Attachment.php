<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'update_id',
        'file_path',
    ];

    public function updateRelation()
    {
        return $this->belongsTo(Update::class);
    }
}
