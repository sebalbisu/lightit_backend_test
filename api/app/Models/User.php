<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class User extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'notify_sms',
    ];

    protected $hidden = [
        'password',
        'photo',
        'email_verified_at',
    ];

    protected $appends = [
        'photo_url',
        'is_verified',
    ];

    protected $datetime = [
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'notify_sms' => 'boolean',
    ];

    public function setPassword(string $password): self
    {
        $this->password = Hash::make($password);

        return $this;
    }

    public function getPhotoUrlAttribute()
    {
        return Storage::url($this->photo);
    }

    public function getIsVerifiedAttribute()
    {
        return $this->email_verified_at != null;
    }

    public function routeNotificationForMail(Notification $notification): array|string
    {
        return [$this->email => $this->name];
    }

    public function routeNotificationForVonage(Notification $notification): string
    {
        return $this->phone;
    }

    public function verifyEmail(): static
    {
        if (! $this->email_verified_at) {
            $this->email_verified_at = now();
        }

        return $this;
    }
}
