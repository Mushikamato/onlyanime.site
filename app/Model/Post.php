<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Model\Category;

class Post extends Model
{
    public const PENDING_STATUS = 0;
    public const APPROVED_STATUS = 1;
    public const DISAPPROVED_STATUS = 2;

    protected $fillable = [
        'user_id', 'text', 'price', 'status', 'release_date', 'expire_date',
        'is_pinned', 'is_adult_content', 'content_type',
    ];

    protected $hidden = [];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_adult_content' => 'boolean',
    ];

    public function getIsExpiredAttribute() {
        return $this->expire_date && $this->expire_date <= Carbon::now();
    }

    public function getIsScheduledAttribute() {
        return $this->release_date && $this->release_date > Carbon::now();
    }

    /*
     * Relationships
     */

    public function user()
    {
        // This now correctly and consistently points to the User model inside your App\Model folder.
        return $this->belongsTo('App\User', 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    public function comments() { return $this->hasMany('App\Model\PostComment'); }
    public function reactions() { return $this->hasMany('App\Model\Reaction'); }
    public function bookmarks() { return $this->hasMany('App\Model\UserBookmark'); }
    public function attachments() { return $this->hasMany('App\Model\Attachment'); }
    public function poll() { return $this->hasOne('App\Model\Poll', 'post_id', 'id'); }
    public function transactions() { return $this->hasMany('App\Model\Transaction'); }
    public function postPurchases() { return $this->hasMany('App\Model\Transaction', 'post_id', 'id')->where('status', 'approved')->where('type', 'post-unlock'); }
    public function tips() { return $this->hasMany('App\Model\Transaction')->where('type', 'tip')->where('status', 'approved'); }

    public static function getStatusName($status) {
        switch ($status){
            case self::PENDING_STATUS: return __("pending");
            case self::APPROVED_STATUS: return __("approved");
            case self::DISAPPROVED_STATUS: return __("disapproved");
        }
    }

    public function scopeNotExpiredAndReleased($query) {
        $query->where(function ($q) {
            $q->where('release_date', '<', Carbon::now())->orWhereNull('release_date');
        })->where(function ($q) {
            $q->where('expire_date', '>', Carbon::now())->orWhereNull('expire_date');
        });
    }
}
