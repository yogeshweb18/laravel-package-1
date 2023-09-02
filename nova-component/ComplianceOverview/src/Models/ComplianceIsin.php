<?php

namespace Axistrustee\ComplianceOverview\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ComplianceIsin extends Model
{
	use HasFactory, LogsActivity;

	protected $table='compliances_isin';

	protected $guarded = [];
    protected static $logOnlyDirty = true;

	protected $fillable = [
        'complianceId', 
        'isin',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }
}