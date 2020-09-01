<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id', 'course_id', 'rating', 'note'
    ];

    // Set datetime custom
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    // Mengambil data courses dari model Review
    public function course(){
        return $this->belongsTo('App\Course');
    }
}
