<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveUserUpload extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'employee_count',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the user who uploaded the file.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
