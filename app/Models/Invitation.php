<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'invited_by', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function inviter() {
        return $this->belongsTo(User::class, 'invited_by');
    }
}