<?php
namespace Models;

use Cartalyst\Sentinel\Users\EloquentUser as SentinelUser;

class User extends SentinelUser
{
    protected $fillable = [
        'email',
        'password',
        'description',
        'followers_count',
        'followd_count',
        'nickname',
        'display_name',
    ];

    protected $hidden = [
        'password',
        'last_name',
        'first_name',
        'permissions'
    ];

    // fields which can be used for login
    protected $loginNames = ['nickname', 'email'];


    /**
     * Define relationship with the posts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany('Models\Post');
    }


    /**
     * Define relationship with the user_followers
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userFollowers()
    {
        return $this->belongsToMany('Models\UserFollowers', 'user_followers', 'user_id', 'user_following_id');
    }


    /**
     * Create big user object
     * @return object
     */
    public function getBigUserObject()
    {
        $user = (object) [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'description' => $this->description,
            'followers_count' => $this->followers_count,
            'followd_count' => $this->followd_count,
            'images' => [
                'profile' => '/uploads/profile/' . $this->profile_image,
                'cover'   => '/uploads/cover/' . $this->cover_image
            ],
            'links' => [
                'profile'       => '/user/' . $this->id,
                'user_timeline' => '/statuses/user-timeline/' . $this->id . '/2/3'
            ]
        ];

        return $user;
    }


    /**
     * Create small user object
     * @return object
     */
    public function getSmallUserObject()
    {
        $user = (object) [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'images' => [
                'profile' => '/uploads/profile/' . $this->profile_image,
                'cover'   => '/uploads/cover/' . $this->cover_image
            ],
            'links' => [
                'profile' => '/user/' . $this->id,
            ]
        ];

        return $user;
    }

}