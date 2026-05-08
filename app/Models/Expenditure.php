<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    protected $fillable = [
        'date', 'description', 'amount', 'category', 'attachment', 'notes',
    ];
}
