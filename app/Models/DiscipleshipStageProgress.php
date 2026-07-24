<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscipleshipStageProgress extends Model
{
    use HasFactory;

    protected $table = 'discipleship_stage_progress';

    protected $fillable = ['stage_number', 'status', 'started_at', 'completed_at', 'notes'];

    protected function casts(): array
    {
        return ['started_at' => 'date', 'completed_at' => 'date'];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(DiscipleshipParticipant::class, 'discipleship_participant_id');
    }
}
