<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'status', 'priority', 'deadline_at', 'metadata', 'assigned_to'])]
class Task extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
            'deadline_at' => 'immutable_date',
            'metadata' => AsArrayObject::class,
        ];
    }

    /**
     * O projeto ao qual esta tarefa pertence.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * O usuário responsável (atribuído) por esta tarefa.
     */
    public function assigneeUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope para buscar tarefas atrasadas não concluídas.
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->where('deadline_at', '<', CarbonImmutable::now())
            ->where('status', '!=', TaskStatus::Concluida);
    }

    /**
     * Scope para filtrar por prioridade.
     */
    public function scopeOfPriority(Builder $query, TaskPriority $priority): void
    {
        $query->where('priority', $priority);
    }
}
