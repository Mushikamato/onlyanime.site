<?php

namespace App\Model; // <-- CORRECTED NAMESPACE

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopCosplayAdultPost extends Model
{
    use HasFactory;

    protected $table = 'top_cosplay_adult_posts'; // Explicitly define the table name

    // Define which columns can be mass assigned
    protected $fillable = [
        'post_id',
        'rank',
        'calculated_at',
    ];

    // Define the relationship to the Post model (assuming Post.php is also in App\Model)
    public function post()
    {
        return $this->belongsTo(\App\Model\Post::class, 'post_id'); // <-- Use full namespace for Post
    }
}