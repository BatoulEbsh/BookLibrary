<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Interaction;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @method static mimeType(string $path)
 */
class File extends Model implements HasMedia
{
    use  HasFactory, Notifiable, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_name',
        'created_date',
        'path',
        'state',
        'file',
        'group_id',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function report()
    {
        return $this->hasOne(Report::class, 'file_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }
}
