<?php

namespace Axistrustee\ComplianceOverview\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ComplianceClcode extends Model
{
	use HasFactory, LogsActivity;

	protected $table='clcode_isins';

	protected $guarded = [];

	protected $fillable = [
        'organization_id', 
        'clcode',
        'isin',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }
}