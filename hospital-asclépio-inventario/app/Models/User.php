<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $role
 * @property int|null $setor_id
 * @property int|null $created_by
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'username', 'password', 'role', 'setor_id', 'created_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isDiretor(): bool
    {
        return $this->role === 'diretor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }

    public function isCoordenador(): bool
    {
        return $this->role === 'coordenador';
    }

    public function isUsuario(): bool
    {
        return $this->role === 'usuario';
    }

    /**
     * @return BelongsTo<Local, $this>
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'setor_id', 'id_local');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany<User, $this>
     */
    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get all local IDs under the user's sector / secretaria.
     *
     * @return Collection<int, int>
     */
    public function getSectorLocalIds(): Collection
    {
        if (! $this->setor_id) {
            return collect();
        }

        $secretariaId = $this->setor?->secretaria_id;

        if ($secretariaId) {
            /** @var Collection<int, int> */
            return Local::where('secretaria_id', $secretariaId)->pluck('id_local');
        }

        /** @var Collection<int, int> */
        return collect([(int) $this->setor_id]);
    }

    /**
     * Check if the target user belongs to this user's sector / secretaria.
     */
    public function belongsToSameSector(User $targetUser): bool
    {
        if (! $this->setor_id || ! $targetUser->setor_id) {
            return false;
        }

        return $this->getSectorLocalIds()->contains($targetUser->setor_id);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
