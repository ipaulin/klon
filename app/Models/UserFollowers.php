<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class UserFollowers extends Model
{

    // change table name
    protected $table = 'user_followers';

    protected $fillable = [
        'user_id',
        'user_following_id'
    ];

    public $timestamps = false;


    /**
     * Define relationship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('Models\User');
    }

}