<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Credential extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'username',
        'password',
        'description',
    ];

    protected $hidden = [
        'password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function setPasswordAttribute($value): void
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    public function getPasswordAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(CredentialShare::class);
    }

    public function sharedWithUsers(): HasMany
    {
        return $this->hasMany(CredentialShare::class)->with('sharedWith');
    }

    public function scopeAccessibleByUser($query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereHas('shares', function ($query) use ($userId) {
                    $query->where('shared_with_user_id', $userId);
                });
        });
    }

    public function canBeShared(): bool
    {
        return $this->category?->name !== 'Personal';
    }
}
