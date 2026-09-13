<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable

{
    
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasSystemRole(string $role): bool
    {
        return $this->getRoleNames()->contains($role);
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAnySystemRole(array $roles): bool
    {
        return $this->getRoleNames()->intersect($roles)->isNotEmpty();
    }

    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    public function children()
    {
        return $this->belongsToMany(Siswa::class, 'parent_student', 'parent_id', 'siswa_id')
            ->withPivot(['relationship', 'is_primary']);
    }
}
