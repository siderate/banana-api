<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $table = 'quizzes';

    protected $primaryKey = 'quiz_id';

    protected $fillable = [
        'user_id', 'media_id', 'type', 'title', 'content', 'state_visibility',
        'url', 'date_published'
    ];
    protected $dates = [
        'date_deleted', 'date_created', 'date_updated', 'date_published'
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

    const CREATED_AT = 'date_created';

    const UPDATED_AT = 'date_updated';

    const DELETED_AT = 'date_deleted';

    public function apiGetLinks()
    {
        return [
            'self' => route('get_quiz', ['id' => $this->getKey()]),
            'comments' => route('get_quiz_comments', ['id' => $this->getKey()]),
        ];
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('date_published');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('quizzes.date_created');
    }

    // RELATIONSHIPS
    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'quiz_has_tags');
    }

    public function media()
    {
        return $this->belongsTo('App\Media')->select(['media_id', 'type', 'mime', 'width', 'height']);
    }

    public function user()
    {
        return $this->belongsTo('App\User')->select(['user_id', 'login', 'first_name', 'last_name', 'media_id'])->with('media');
    }

    public function questions()
    {
        return $this->hasMany('App\Question')->select(['question_id', 'quiz_id', 'title', 'state_order'])->with('answers');
    }

    public function results()
    {
        return $this->hasMany('App\Result')->select(['result_id', 'quiz_id', 'title', 'content', 'moreof']);
    }
}
