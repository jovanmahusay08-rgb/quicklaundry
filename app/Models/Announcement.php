<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'content', 'image_url', 'visible_to', 'is_active', 'created_by',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
