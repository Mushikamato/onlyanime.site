<?php

namespace App\Model; // <-- CORRECTED NAMESPACE

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopHentaiNotAdultPost extends Model
{
    use HasFactory;

    protected $table = 'top_hentai_not_adult_posts'; // Explicitly define the table name

    protected $fillable = [
        'post_id',
        'rank',
        'calculated_at',
    ];

    public function post()
    {
        return $this->belongsTo(\App\Model\Post::class, 'post_id'); // <-- Use full namespace for Post
    }
}