<?php

namespace Axistrustee\ComplianceOverview\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ComplianceCovenant extends Model
{
	use HasFactory, LogsActivity;

	protected $table='compliances_covenants';

	protected $guarded = [];
    protected static $logOnlyDirty = true;

	protected $fillable = [
        'complianceId', 
        'type',
        'subType',
        'description',
        'associated_covenant_id',
        'isCustomCovenant',
        'comments',
        'frequency',
        'targetValue',
        'startDate',
        'applicableMonth',
        'dueDate',
        'covenantStatus',
        'maintained_as',
        'manner_of_creation',
        'account_number',
        'additional_details',
        'is_manner_invoked',
        'amount',
        'date_of_invocation',
        'amount_invoked',
        'period_for_renewal_before_expiry',
        'period_of_replenishment_after_shortfall',
        'rating_symbol',
        'suffix',
        'insurance_cover_effected',
        'insurance_policy_in_favour_DT',
        'restriction_on_fund_raising',
        'kind_of_fund_restriction',
        'manner_of_restriction_revocation',
        'bank_account_number',
        'period_for_cersai_from_security_creation',
        'period_for_chg_from_security_creation',
        'time_bucket',
        'change_in_kmp',
        'change_in_boardOfDirectors',
        'change_in_significant_person',
        'custom_parameter',
        'custom_value',
        'custom_child_dueDate',
        'custom_child'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }
}