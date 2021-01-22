<?php


namespace Milebits\Society\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Eloquent\Filters\Concerns\Enableable;
use Milebits\Society\Concerns\Attachable;

class Story extends Model
{

    use SoftDeletes, HasFactory, Enableable, Attachable;

    protected $fillable = ['content'];
}