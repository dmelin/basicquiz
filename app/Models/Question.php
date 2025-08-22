<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $fillable = [
        'content',
        'answer',
        'wrongs',
        'category',
    ];

    protected $hidden = [
        'wrongs',
        'answer',
    ];

    // "I have sworn upon the altar of God eternal hostility against every form 
    // of JSON tyranny over the mind of array." - Thomas Jefferson (probably)
    protected $casts = [
        'wrongs' => 'array',
    ];

    protected $appends = ['options'];

    /**
     * Get the answers for the question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Set the answer attribute, ensuring it is always a string.
     *
     * @param mixed $value
     */
    public function setAnswerAttribute($value)
    {
        $this->attributes['answer'] = (string) $value;
    }

    /**
     * Get the options for the question, including the correct answer and wrong answers.
     *
     * @return array
     */
    public function getOptionsAttribute()
    {
        $options = [$this->answer, ...$this->wrongs];
        sort($options);

        return $options;
    }
}
