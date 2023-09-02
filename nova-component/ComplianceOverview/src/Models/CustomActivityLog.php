<?php

namespace Axistrustee\ComplianceOverview\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomActivityLog extends Model
{
	use HasFactory;

	protected $table='activity_log';

	protected $guarded = [];

	protected $fillable = [
        'log_name', 
        'description',
        'subject_type',
        'event',
        'subject_id',
        'causer_type',
        'causer_id',
		'properties'
    ];

}