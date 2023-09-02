<?php

namespace Axis\Newcompliance\Models;
use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceCovenantChild extends Model
{
	use HasFactory;

	protected $table='compliances_covenants_child';

	protected $guarded = [];

	protected $fillable = [
        'complianceId', 
        'compliancesCovenantsId',
        'childCovenant',
        'childFrequency',
        'childCovenantValue',
    ];
}