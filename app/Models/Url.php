<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Url extends Model
{
    use HasFactory, Notifiable;
    protected $primaryKey = 'hash';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash',
        'user_id',
        'target_url',
        'created_at',
        'updated_at',
        'expired_at'
    ];

    public function rules(){
        return [
            'target_url' => 'required'
        ];
    }

    public function feedback(){
        return [];
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
