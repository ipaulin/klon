<?php
namespace Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $fillable = [
        'text',
        'user_id',
    ];

    /**
     * Define relationship with the user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Models\User');
    }

    /**
     * Create object with status data
     * @return object
     */
    public function getPostDataObject()
    {
        $post = (object) [
            'id' => $this->id,
            'text' => $this->text,
            'user' => $this->user->getSmallUserObject(),
            'links' => [
                'show' => ''
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $post;
    }

}