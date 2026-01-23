<?php

namespace App\Models\Sales;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\HasOneThrough;
// use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
// use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasManyThrough;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// load column name attribute
use Illuminate\Database\Eloquent\Casts\Attribute;

// load helper
use Illuminate\Support\Str;

// load sluggable
// use Cviebrock\EloquentSluggable\Sluggable;
// use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class SalesAmend extends Model
{
	//
	use SoftDeletes;
	// protected $connection = '';
	// protected $table = '';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	// protected $casts = [
	// 	'is_active' => 'boolean',
	// ];

	// public function sluggable(): array
	// {
	// 	return [
	// 		'slug' => ['source' => 'UniqueColumnName']
	// 	];
	// }

	// public function getRouteKeyName()
	// {
	// 	return 'slug';
	// }

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	// protected function amend(): Attribute
	// {
	// 	return Attribute::make(
	// 		set: fn ($value) => Str::title(Str::lower($value)),
	// 	);
	// }

	// protected function remarks(): Attribute
	// {
	// 	return Attribute::make(
	// 		set: fn ($value) => Str::title(Str::lower($value)),
	// 	);
	// }

	protected function setAmendAttribute($value)
	{
	    $this->attributes['amend'] = Str::title(Str::lower($value));
	}

	protected function setRemarksAttribute($value)
	{
	    $this->attributes['remarks'] = Str::title(Str::lower($value));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship

}
