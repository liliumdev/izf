<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'email',
        'title',
        'content',
        'is_public',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'question_tag', 'question_id', 'tag_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'tags' => $this->tags->pluck('name')->implode(' '),
            'category_id' => (int) $this->category_id,
            'category_name' => $this->category?->name,
            'is_public' => (bool) $this->is_public,
        ];
    }
}
