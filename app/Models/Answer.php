<?php

namespace App\Models;

use App\States\Answer\Draft;
use Laravel\Scout\Searchable;
use App\States\Answer\Reviewed;
use App\States\Answer\Published;
use Spatie\ModelStates\HasStates;
use App\States\Answer\AnswerState;
use App\States\Answer\ReadyForReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;
    use HasStates;
    use Searchable;

    protected $fillable = [
        'content',
        'drafted_by',
        'reviewed_by',
        'state',
    ];

    protected $casts = [
        'state' => AnswerState::class,
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toSearchableArray()
    {
        return [
            'content' => $this->content,
            'state' => $this->state->name,
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->state->equals(Published::class);
    }

    protected function registerStates(): void
    {
        $this
            ->addState('state', Draft::class)
            ->default(Draft::class)
            ->allowTransitions([
                [Draft::class, ReadyForReview::class],
                [ReadyForReview::class, Reviewed::class],
                [Reviewed::class, Published::class],
            ]);
    }
}
