<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Illuminate\Foundation\Auth\UserをAuthenticatableで使用可能にする
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

// Userクラスを拡張しAuthenticatableとして使用する
class User extends Authenticatable
{
    // UserクラスのHasFactoryとNotifiableを使用
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * IDEに型情報を伝える
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // 戻り値としてarray型を指定したメソッド
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // リレーション
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function generateApiToken(): string
    {
        $token = Str::random(60);
        $this->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return $token;
    }

    public function clearApiToken(): void
    {
        $this->forceFill(['api_token' => null])->save();
    }
}
