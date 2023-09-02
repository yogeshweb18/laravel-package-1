<?php

namespace Axis\Newcompliance\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceCovenantParameter extends Model
{
	use HasFactory;

	protected $table='compliances_covenants_parameters';

	protected $guarded = [];

	protected $fillable = [
        'complianceId', 
        'compliancesCovenantsId',
        'parameterKey',
        'parameterType',
        'parameterAction',
        'parameterSelectedOption',
        'parameterValue',
    ];
}