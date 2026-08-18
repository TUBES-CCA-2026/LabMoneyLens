<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $table = 'images';
    protected $primaryKey = 'id_image';

    protected $fillable = [
        'filename',
        'path',
        'mime_type',
        'size',
        'alt_text',
        'description',
        'imageable_type',
        'imageable_id',
        'width',
        'height',
        'image_type',
        'id_user',
    ];

    /**
     * Get the parent imageable model (Pemasukan, Pengeluaran, User, etc)
     */
    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user that uploaded this image
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}

