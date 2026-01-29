<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
  use HasFactory, SoftDeletes;
  protected $guarded = [];
  public $timestamps = true;

  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

    public function student()
  {
    return $this->belongsTo(StudentInfo::class, 'student_id');
  }

  public function course()
  {
    return $this->belongsTo(Course::class, 'course_id');
  }

  public function applicationStatus()
  {
    return $this->belongsTo(ApplicationStatus::class, 'status', 'status_order');
  }

}
