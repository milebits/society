<?php


namespace Milebits\Society\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Eloquent\Filters\Concerns\Enableable;

class Story extends Model
{
    use SoftDeletes, HasFactory, Enableable;

    protected $fillable = ['content'];
}