<?php

namespace Axistrustee\ComplianceOverview\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ComplianceInstance extends Model
{
	use HasFactory, LogsActivity;

	protected $table='compliances_covenants_instances';

	protected $guarded = [];

	protected $fillable = [
        'complianceId', 
        'covenantId',
        'instanceNo',
        'activateDate',
        'dueDate',
        'applicableMonth',
        'status',
        'approvalStatus',
        'reminderBefore',
		'reminderInterval',
        'is_child',
        'child_label',
        'associated_instance',
        'is_fail',
        'fail_label',
        'is_manner_invoked',
        'date_of_invocation',
        'amount_invoked',
        'resolution_value',
        'uploads',
        'comments',
        'resolver',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }

    public function setStatusAttribute($value) 
    {
        if($value == 'Not Started')
            $this->attributes['status'] = 0;
        else if($value == 'Started')
            $this->attributes['status'] = 1;
        else if($value == 'pass') {
            $this->attributes['status'] = 2;
        }else if($value == 'Pending') {
            $this->attributes['status'] = 4;
        }
        else if($value == 'Approved') {
            $this->attributes['status'] = 5;
        }
        else if($value == 'Rejected') {
            $this->attributes['status'] = 6;
        }
        else if($value == 'Pending For Approval') {
            $this->attributes['status'] = 7;
        }
        else {
            $this->attributes['status'] = 3; 
        }
    }
    
    function getStatusAttribute()     
    { 
        if($this->attributes['status'] == 0)
            return 'Not Started';
        else if($this->attributes['status'] == 1)
            return 'Started';
        else if($this->attributes['status'] == 2)
            return 'Passed';
        else if($this->attributes['status'] == 4)
            return 'Pending';
        else if($this->attributes['status'] == 5)
            return 'Approved';
        else if($this->attributes['status'] == 6)
            return 'Rejected';
        else if($this->attributes['status'] == 7)
            return 'Pending For Approval';        
        else
            return 'Failed';
    }
}