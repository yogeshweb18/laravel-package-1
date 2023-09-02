<?php

namespace Axistrustee\ComplianceOverview\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Compliancetool extends Model
{
	use HasFactory, LogsActivity;

	protected $table='compliances';

	protected $guarded = [];

	protected $fillable = [
        'clcode', 
        'startDate',
        'endDate',
        'priority',
        'secured',
        'inconsistencyTreatment',
		'clientReference',
        'mailCC',
        'documentNames',
        'complianceStatus',
        'userId',
        'organization_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}