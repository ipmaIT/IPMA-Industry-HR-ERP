@extends('layouts.app')

@section('content')
<?php
use App\Models\Staff;
use App\Models\HumanResources\OptReligion;
use App\Models\HumanResources\OptGender;
use App\Models\HumanResources\OptRace;
use App\Models\HumanResources\OptMaritalStatus;
use App\Models\HumanResources\OptCountry;
use App\Models\HumanResources\HRLeaveApprovalFlow;
?>

<div class="container row justify-content-center align-items-start">
@include('humanresources.hrdept.navhr')
	<h4 class="align-items-start">Edit Staff</h4>
	<form method="POST" action="{{ route('staff.update', $staff) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')

	<div class="col-sm-12 row">
		<div class="col-sm-6">

			<div class="form-group row m-2 {{ $errors->has('name') ? 'has-error' : '' }}">
				<label for="nam" class="col-form-label col-sm-4">Name : </label>
				<div class="col-sm-7">
					<input type="text" name="name" value="{{ old('name', $staff->name) }}" id="nam" class="form-control form-control-sm col-sm-12 @error('name') is-invalid @enderror" placeholder="Name">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('ic') ? 'has-error' : '' }}">
				<label for="ic" class="col-form-label col-sm-4">Identity Card/Passport : </label>
				<div class="col-sm-7">
					<input type="text" name="ic" value="{{ old('ic', $staff->ic) }}" id="ic" class="form-control form-control-sm col-sm-12 @error('ic') is-invalid @enderror" placeholder="Identity Card/Passport">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('dob') ? 'has-error' : '' }}" style="position: relative">
				<label for="dob" class="col-form-label col-sm-4">Date Of Birth : </label>
				<div class="col-sm-7">
					<input type="text" name="dob" value="{{ old('dob', $staff->dob) }}" id="dob" class="form-control form-control-sm col-sm-12 @error('dob') is-invalid @enderror" placeholder="Date Of Birth">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('gender_id') ? 'has-error' : '' }}">
				<label for="gender" class="col-form-label col-sm-4">Gender : </label>
				<div class="col-sm-7">
					<?php $i=0 ?>
					@foreach(\App\Models\HumanResources\OptGender::orderBy('id')->get() as $g)
					<div class="form-check form-check-inline">
						<label for="gen_{{ $i }}" class="col-form-label mx-2">
							<input type="radio" name="gender_id" value="{{ $g->id }}" id="gen_{{ $i }}" class="form-check-input @error('gender_id') is-invalid @enderror" {{ ( old('gender_id', $staff->gender_id) == $g->id )?'checked':NULL }}>
						{{ $g->gender }}</label>
					</div>
					<?php $i++ ?>
					@endforeach
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('marital_status_id') ? 'has-error' : '' }}">
				<label for="mar" class="col-form-label col-sm-4">Marital Status : </label>
				<div class="col-sm-7">
					<select name="marital_status_id" id="mar" class="form-select form-select-sm col-sm-12 @error('marital_status_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(OptMaritalStatus::pluck('marital_status', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('marital_status_id', $staff->marital_status_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('email') ? 'has-error' : '' }}">
				<label for="ema" class="col-form-label col-sm-4">Email : </label>
				<div class="col-sm-7">
					<input type="text" name="email" value="{{ old('email', $staff->email) }}" id="id" class="form-control form-control-sm col-sm-12 @error('email') is-invalid @enderror" placeholder="Name">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('address') ? 'has-error' : '' }}">
				<label for="add" class="col-form-label col-sm-4">Address : </label>
				<div class="col-sm-7">
					<textarea name="address" id="add" class="form-control form-control-sm col-sm-12 @error('address') is-invalid @enderror" placeholder="Address">{{ old('address', $staff->address) }}</textarea>
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('mobile') ? 'has-error' : '' }}">
				<label for="mob" class="col-form-label col-sm-4">Mobile : </label>
				<div class="col-sm-7">
					<input type="text" name="mobile" value="{{ old('mobile', $staff->mobile) }}" id="mob" class="form-control form-control-sm col-sm-12 @error('mobile') is-invalid @enderror" placeholder="Mobile">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('phone') ? 'has-error' : '' }}">
				<label for="pho" class="col-form-label col-sm-4">Phone : </label>
				<div class="col-sm-7">
					<input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" id="pho" class="form-control form-control-sm col-sm-12 @error('phone') is-invalid @enderror" placeholder="Phone">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('religion_id') ? 'has-error' : '' }}">
				<label for="rel" class="col-form-label col-sm-4">Religion : </label>
				<div class="col-sm-7">
					<select name="religion_id" id="rel" class="form-select form-select-sm col-sm-12 @error('religion_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(OptReligion::pluck('religion', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('religion_id', $staff->religion_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('race_id') ? 'has-error' : '' }}">
				<label for="rac" class="col-form-label col-sm-4">Race : </label>
				<div class="col-sm-7">
					<select name="race_id" id="rac" class="form-select form-select-sm col-sm-12 @error('race_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(OptRace::pluck('race', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('race_id', $staff->race_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('nationality_id') ? 'has-error' : '' }}">
				<label for="nat" class="col-form-label col-sm-4">Nationality : </label>
				<div class="col-sm-7">
					<select name="nationality_id" id="nat" class="form-select form-select-sm col-sm-12 @error('nationality_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(OptCountry::pluck('country', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('nationality_id', $staff->nationality_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('cimb_account') ? 'has-error' : '' }}">
				<label for="cia" class="col-form-label col-sm-4">CIMB Account : </label>
				<div class="col-sm-7">
					<input type="text" name="cimb_account" value="{{ old('cimb_account', $staff->cimb_account) }}" id="cia" class="form-control form-control-sm col-sm-12 @error('cimb_account') is-invalid @enderror" placeholder="CIMB Account">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('epf_account') ? 'has-error' : '' }}">
				<label for="epf" class="col-form-label col-sm-4">EPF Account : </label>
				<div class="col-sm-7">
					<input type="text" name="epf_account" value="{{ old('epf_account', $staff->epf_account) }}" id="epf" class="form-control form-control-sm col-sm-12 @error('epf_account') is-invalid @enderror" placeholder="EPF Account">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('income_tax_no') ? 'has-error' : '' }}">
				<label for="itn" class="col-form-label col-sm-4">Income Tax No : </label>
				<div class="col-sm-7">
					<input type="text" name="income_tax_no" value="{{ old('income_tax_no', $staff->income_tax_no) }}" id="itn" class="form-control form-control-sm col-sm-12 @error('income_tax_no') is-invalid @enderror" placeholder="Income Tax No">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('socso_no') ? 'has-error' : '' }}">
				<label for="son" class="col-form-label col-sm-4">SOCSO No : </label>
				<div class="col-sm-7">
					<input type="text" name="socso_no" value="{{ old('socso_no', $staff->socso_no) }}" id="son" class="form-control form-control-sm col-sm-12 @error('socso_no') is-invalid @enderror" placeholder="SOCSO No">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('weight') ? 'has-error' : '' }}">
				<label for="wei" class="col-form-label col-sm-4">Weight : </label>
				<div class="col-sm-7">
					<input type="text" name="weight" value="{{ old('weight', $staff->weight) }}" id="wei" class="form-control form-control-sm col-sm-12 @error('weight') is-invalid @enderror" placeholder="Weight">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('height') ? 'has-error' : '' }}">
				<label for="hei" class="col-form-label col-sm-4">Height : </label>
				<div class="col-sm-7">
					<input type="text" name="height" value="{{ old('height', $staff->height) }}" id="hei" class="form-control form-control-sm col-sm-12 @error('height') is-invalid @enderror" placeholder="Height">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('join') ? 'has-error' : '' }}">
				<label for="jpo" class="col-form-label col-sm-4">Date Join : </label>
				<div class="col-sm-7">
					<input type="text" name="join" value="{{ old('join', $staff->join) }}" id="jpo" class="form-control form-control-sm col-sm-12 @error('join') is-invalid @enderror" placeholder="Date Join">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('confirmed') ? 'has-error' : '' }}">
				<label for="jpo" class="col-form-label col-sm-4">Date Confirm : </label>
				<div class="col-sm-7">
					<input type="text" name="confirmed" value="{{ old('confirmed', $staff->confirmed) }}" id="jpo" class="form-control form-control-sm col-sm-12 @error('confirmed') is-invalid @enderror" placeholder="Date Confirm">
				</div>
			</div>

			<div class="form-group row m-2 {{ $errors->has('image') ? 'has-error' : '' }}">
				<label for="ima" class="col-form-label col-sm-4">Image : </label>
				<div class="col-sm-7 supportdoc">
					<input type="file" name="image" value="{{ old('image', $staff->image) }}" id="ima" class="form-control form-control-sm form-control-file @error('image') is-invalid @enderror" placeholder="Image">
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="col-sm-12">
				<div class="row m-1">
					<div class="col-sm-3">
						<h6>Staff Spouse</h6>
					</div>
					<div class="col-sm-7">
						<button type="button" class="col-auto btn btn-sm btn-outline-secondary spouse_add">
							<i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add Spouse
						</button>
					</div>
				</div>
				<div class="row spouse_wrap">
					@if($staff->hasmanyspouse()->get()->count())
						<?php $i=1 ?>
						@foreach($staff->hasmanyspouse()->get() as $spouse)
							<div class="row m-1 spouse_row">
								<div class="col-sm-1">
									<button class="btn btn-sm btn-outline-secondary spouse_delete" data-id="{{ $spouse->id }}" type="button">
										<i class="fas fa-trash" aria-hidden="true"></i>
									</button>
								</div>
								<div class="col-sm-11 form-group {{ $errors->has('staffspouse.*.spouse') ? 'has-error' : '' }}">
									<input type="hidden" name="staffspouse[{{ $i }}][id]" value="{{ $spouse->id }}">
									<input type="text" name="staffspouse[{{ $i }}][spouse]" id="spo" value="{{ $spouse->spouse }}" class="form-control form-control-sm" placeholder="Spouse">
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-5 form-group {{ $errors->has('staffspouse.*.phone') ? 'has-error' : '' }}">
									<input type="text" name="staffspouse[{{ $i }}][phone]" value="{{ $spouse->phone }}" id="pho" class="form-control form-control-sm" placeholder="Spouse Phone">
								</div>
								<div class="col-sm-6 form-group {{ $errors->has('staffspouse.*.profession') ? 'has-error' : '' }}">
									<input type="text" name="staffspouse[{{ $i }}][profession]" value="{{ $spouse->profession }}" id="pro" class="form-control form-control-sm" placeholder="Spouse Profession">
								</div>
							</div>
							<?php $i++ ?>
						@endforeach
					@endif
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="col-sm-12">
				<div class="row m-1">
					<div class="col-sm-3">
						<h6>Staff Children</h6>
					</div>
					<div class="col-sm-7">
						<button type="button" class="col-auto btn btn-sm btn-outline-secondary children_add">
							<i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add Children
						</button>
					</div>
				</div>
				<div class="row children_wrap">
					@if($staff->hasmanychildren()->get()->count())
						<?php $i=1 ?>
						@foreach($staff->hasmanychildren()->get() as $child)
							<div class="row m-1 children_row">
								<div class="col-sm-1">
									<button class="btn btn-sm btn-outline-secondary children_delete" data-id="{{ $child->id }}" type="button">
										<i class="fas fa-trash" aria-hidden="true"></i>
									</button>
								</div>
								<div class="col-sm-11 form-group {{ $errors->has('staffchildren.*.children') ? 'has-error' : '' }}">
									<input type="hidden" name="staffchildren[{{ $i }}][id]" value="{{ $child->id }}">
									<input type="text" name="staffchildren[{{ $i }}][children]" value="{{ $child->children }}" id="chi_{{ $i }}" class="form-control form-control-sm" placeholder="Children">
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-7 form-group {{ $errors->has('staffchildren.*.dob') ? 'has-error' : '' }}">
									<input type="text" name="staffchildren[1][dob]" value="{{ old('staffchildren[$i][dob]', $child->dob) }}" id="cdo_{{ $i }}" class="form-control form-control-sm" placeholder="Date Of Birth">
								</div>
								<div class="col-sm-4 form-group {{ $errors->has('staffchildren.*.gender_id') ? 'has-error' : '' }}">
									<select name="staffchildren[{{ $i }}][gender_id]" id="cge_{{ $i }}" class="form-select form-select-sm" placeholder="Gender">
										<option value="">Gender</option>
									@foreach(\App\Models\HumanResources\OptGender::all() as $g)
										<option value="{{ $g->id }}" {{ ($g->id == $child->gender_id)?'selected':NULL }}>{{ $g->gender }}</option>
									@endforeach
									</select>
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-7 form-group {{ $errors->has('staffchildren.*.education_level_id') ? 'has-error' : '' }}">
									<select name="staffchildren[{{ $i }}][education_level_id]" id="cel_{{ $i }}" class="form-select form-select-sm" placeholder="Education Level">
										<option value="">Education Level</option>
									@foreach(\App\Models\HumanResources\OptEducationLevel::all() as $el)
										<option value="{{ $el->id }}" {{ ($el->id == $child->education_level_id)?'selected':'' }}>{{ $el->education_level }}</option>
									@endforeach
									</select>
								</div>
								<div class="col-sm-4 form-group {{ $errors->has('staffchildren.*.health_status_id') ? 'has-error' : '' }}">
									<select name="staffchildren[{{ $i }}][health_status_id]" id="chs_{{ $i }}" class="form-select form-select-sm" placeholder="Health Status">
										<option value="">Health Status</option>
									@foreach(\App\Models\HumanResources\OptHealthStatus::all() as $hs)
										<option value="{{ $hs->id }}" {{ ($hs->id == $child->health_status_id)?'selected':NULL }}>{{ $hs->health_status }}</option>
									@endforeach
									</select>
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-5 form-group form-check {{ $errors->has('staffchildren.*.tax_exemption') ? 'has-error' : '' }}">
									<input type="hidden" name="staffchildren[{{ $i }}][tax_exemption]" class="form-check-input" value="0">
									<input type="checkbox" name="staffchildren[{{ $i }}][tax_exemption]" class="form-check-input" value="1" id="cte_{{ $i }}" {{ ($child->tax_exemption)?'checked':NULL }}>
									<label class="form-check-label" for="cte_{{ $i }}">Valid for Tax Exemption?</label>
								</div>
								<div class="col-sm-6 form-group {{ $errors->has('staffchildren.*.tax_exemption_percentage_id') ? 'has-error' : '' }}">
									<select name="staffchildren[{{ $i }}][tax_exemption_percentage_id]" id="ctep_{{ $i }}" class="form-select form-select-sm" placeholder="Tax Exemption Percentage">
										<option value="">Tax Exemption Percentage</option>
									@foreach(\App\Models\HumanResources\OptTaxExemptionPercentage::all() as $tep)
										<option value="{{ $tep->id }}" {{ ($tep->id == $child->tax_exemption_percentage_id)?'selected':NULL }}>{{ $tep->tax_exemption_percentage }}</option>
									@endforeach
									</select>
								</div>
							</div>
							<?php $i++ ?>
						@endforeach
					@endif
				</div>
			</div>

			<p>&nbsp;</p>

			<div class="col-sm-12">
				<div class="row m-1">
					<div class="col-sm-5">
						<h6>Staff Emergency Contact</h6>
					</div>
					<div class="col-sm-7">
						<button type="button" class="col-auto btn btn-sm btn-outline-secondary emergency_add">
							<i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add Emergency Contact
						</button>
					</div>
				</div>
				<div class="row emergency_wrap">
					@if($staff->hasmanyemergency()->get()->count())
						<?php $i=1 ?>
						@foreach($staff->hasmanyemergency()->get() as $emerg)
							<div class="row m-1 emergency_row">
								<div class="col-sm-1">
									<button class="btn btn-sm btn-outline-secondary emergency_delete" data-id="{{ $emerg->id }}" type="button">
										<i class="fas fa-trash" aria-hidden="true"></i>
									</button>
								</div>
								<div class="col-sm-11 form-group {{ $errors->has('staffemergency.*.contact_person') ? 'has-error' : '' }}">
									<input type="hidden" name="staffemergency[{{ $i }}][id]" value="{{ $emerg->id }}">
									<input type="text" name="staffemergency[{{ $i }}][contact_person]" value="{{ $emerg->contact_person }}" id="ecp_{{ $i }}" class="form-control form-control-sm" placeholder="Emergency Contact">
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-5 form-group {{ $errors->has('staffemergency.*.phone') ? 'has-error' : '' }}">
									<input type="text" name="staffemergency[{{ $i }}][phone]" value="{{ $emerg->phone }}" id="epp_{{ $i }}" class="form-control form-control-sm" placeholder="Phone">
								</div>
								<div class="col-sm-6 form-group {{ $errors->has('staffemergency.*.relationship_id') ? 'has-error' : '' }}">
									<select name="staffemergency[{{ $i }}][relationship_id]" id="ere_{{ $i }}" class="form-select form-select-sm" placeholder="Relationship">
											<option value="">Relationship</option>
										@foreach(\App\Models\HumanResources\OptRelationship::all() as $rel)
											<option value="{{ $rel->id }}" {{ ($rel->id == $emerg->relationship_id)?'selected':NULL }}>{{ $rel->relationship }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-sm-1"></div>
								<div class="col-sm-11 form-group {{ $errors->has('staffemergency.*.address') ? 'has-error' : '' }}">
									<input type="textarea" name="staffemergency[{{ $i }}][address]" value="{{ $emerg->address }}" id="ead_{{ $i }}" class="form-control form-control-sm" placeholder="Address">
								</div>
							</div>
							<?php $i++ ?>
						@endforeach
					@endif
				</div>
			</div>
		</div>

		<div class="col-sm-6 container">

			<div class="row mb-3 form-group {{ $errors->has('authorise_id') ? 'has-error' : '' }}">
				<div class="col-sm-7 form-check">
					<div class="pretty p-icon p-curve p-tada">
						<input type="hidden" name="authorise_id" value="">
						<input type="checkbox" name="authorise_id" value="1" id="authjj" class="form-check-input @error('authorise_id') is-invalid @enderror" {{ ( old('authorise_id', $staff->authorise_id) == 1 )?'checked':NULL }}>
						<div class="state p-primary-o">
							<i class="icon mdi mdi-check-all"></i>
							<label class="form-check-label" for="authjj">System Administrator</label>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('status_id') ? 'has-error' : '' }}">
				<label for="sta" class="col-form-label col-sm-4">Staff Status : </label>
				<div class="col-sm-7">
					<select name="status_id" id="sta" class="form-select form-select-sm col-sm-12 @error('status_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(\App\Models\HumanResources\OptStatus::pluck('status', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('status_id', $staff->status_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('username') ? 'has-error' : '' }}">
				<label for="unam" class="col-form-label col-sm-4">Username : </label>
				<div class="col-sm-7">
					<input type="text" name="username" value="{{ old('username', $staff->hasmanylogin()->where('active', 1)->first()->username) }}" id="unam" class="form-control form-control-sm col-sm-12 @error('username') is-invalid @enderror" placeholder="Username">
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('password') ? 'has-error' : '' }}">
				<label for="pas" class="col-form-label col-sm-4">Password : </label>
				<div class="col-sm-7">
					<input type="text" name="password" value="{{ old('password', $staff->password) }}" id="pas" class="form-control form-control-sm col-sm-12 @error('password') is-invalid @enderror" placeholder="Password">
					<div id="passHelp" class="form-text">Insert password if only need to be change. Otherwise, just leave it.</div>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('category_id') ? 'has-error' : '' }}">
				<label for="cat" class="col-form-label col-sm-4">Category : </label>
				<div class="col-sm-7">
					<select name="category_id" id="cat" class="form-select form-select-sm col-sm-12 @error('category_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(\App\Models\HumanResources\OptCategory::pluck('category', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('category_id', $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->category_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('branch_id') ? 'has-error' : '' }}">
				<label for="bra" class="col-form-label col-sm-4">Branch : </label>
				<div class="col-sm-7">
					<select name="branch_id" id="bra" class="form-select form-select-sm col-sm-12 @error('branch_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(\App\Models\HumanResources\OptBranch::pluck('location', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('branch_id', $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->branch_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('pivot_dept_id') ? 'has-error' : '' }}">
				<label for="dep" class="col-form-label col-sm-4">Department : </label>
				<div class="col-sm-7">
					<select name="pivot_dept_id" id="dep" class="form-select form-select-sm col-sm-12 @error('pivot_dept_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(\App\Models\HumanResources\DepartmentPivot::pluck('department', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('pivot_dept_id', $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('div_id') ? 'has-error' : '' }}">
				<label for="him" class="col-form-label col-sm-4">Management : </label>
				<div class="col-sm-7">
					<select name="div_id" id="him" class="form-select form-select-sm col-sm-12 @error('div_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(\App\Models\HumanResources\OptDivision::pluck('div', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('div_id', $staff->div_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('restday_group_id') ? 'has-error' : '' }}">
				<label for="rdg" class="col-form-label col-sm-4">Rest Day Group : </label>
				<div class="col-sm-7">
					<select name="restday_group_id" id="rdg" class="form-select form-select-sm col-sm-12 @error('restday_group_id') is-invalid @enderror">
						<option value="">Please choose</option>
						@foreach(\App\Models\HumanResources\OptRestdayGroup::pluck('group', 'id')->toArray() as $k1 => $v1)
							<option value="{{ $k1 }}" {{ (old('restday_group_id', $staff->restday_group_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="form-group row mb-3 {{ $errors->has('leave_flow_id') ? 'has-error' : '' }}">
				<label for="flow" class="col-form-label col-sm-4">Leave Flow Approval : </label>

				<div class="col-sm-7">
				@foreach(HRLeaveApprovalFlow::all() as $k)
					<div class="form-check form-check-inline">
						<div class="pretty p-icon p-curve p-tada mb-2">
							<input type="radio" name="leave_flow_id" class="form-check-input" value="{{ $k->id }}" {{ ($staff->leave_flow_id == $k->id)?'checked':NULL }} id="auth">
							<div class="state p-primary-o">
								<i class="icon mdi mdi-check"></i>
								<label class="form-check-label" for="auth">{{ $k->description }}</label>
							</div>
						</div>
					</div>
				@endforeach
				</div>
			</div>

			<div class="form-group row mb-3">
				<div class="row m-0 p-0">
					<div class="col-sm-4">
						<h6>Staff Cross Backup</h6>
					</div>
					<div class="col-sm-7">
						<button type="button" class="col-auto btn btn-sm btn-outline-secondary crossbackup_add">
							<i class="fas fa-plus" aria-hidden="true"></i>&nbsp;Add Cross Backup
						</button>
					</div>
				</div>
				<div class="row m-0 p-0 crossbackup_wrap">
					@if($staff->crossbackupto()->wherePivot('active', 1)->get()->count())
						<?php $i=1 ?>
						@foreach($staff->crossbackupto()->wherePivot('active', 1)->get() as $cb)
							<div class="row m-1 p-0 crossbackup_row">
								<div class="col-sm-1">
									<button type="button" class="btn btn-sm btn-outline-secondary crossbackup_delete" data-id="{{ $cb->id }}">
										<i class="fas fa-trash" aria-hidden="true"></i>
									</button>
								</div>
								<div class="col-sm-10 form-group {{ $errors->has('crossbackup.*.backup_staff_id') ? 'has-error' : '' }}">
									<input type="hidden" name="crossbackup[{{ $i }}][active]" value="1">
									<select name="crossbackup[{{ $i }}][backup_staff_id]" id="sta_{{ $i }}" class="form-select form-select-sm" placeholder="Cross Backup Personnel">
										<option value="">Cross Backup Personnel</option>
										@foreach(Staff::where('active', 1)->get() as $st)
											<option value="{{ $st->id }}" {{ ($st->id == $cb->id)?'selected':NULL }}>{{ $st->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						<?php $i++ ?>
						@endforeach
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="d-flex justify-content-center m-3">
		<button type="submit" class="btn btn-sm btn-outline-secondary">Update Staff</button>
	</div>

	</form>
</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// delete spouse
$(document).on('click', '.spouse_delete', function(e){
	var spouseId = $(this).data('id');
	SwalDelete(spouseId);
	e.preventDefault();
});

function SwalDelete(spouseId){
	swal.fire({
		title: 'Are you sure?',
		text: "It will be deleted permanently!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
		showLoaderOnConfirm: true,

		preConfirm: function() {
			return new Promise(function(resolve) {
				$.ajax({
					type: 'DELETE',
					url: '{{ url('spouse') }}' + '/' + spouseId,
					data: {
							_token : $('meta[name=csrf-token]').attr('content'),
							id: spouseId,
					},
					dataType: 'json'
				})
				.done(function(response){
					swal.fire('Deleted!', response.message, response.status)
					.then(function(){
						window.location.reload(true);
					});
					//$('#disable_user_' + spouseId).parent().parent().remove();
				})
				.fail(function(){
					swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
				})
			});
		},
		allowOutsideClick: false
	})
	.then((result) => {
		if (result.dismiss === swal.DismissReason.cancel) {
			swal.fire('Cancelled', 'Your data is safe from delete', 'info')
		}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////
// delete children
$(document).on('click', '.children_delete', function(e){
	var childrenId = $(this).data('id');
	SwalChildDelete(childrenId);
	e.preventDefault();
});

function SwalChildDelete(childrenId){
	swal.fire({
		title: 'Are you sure?',
		text: "It will be deleted permanently!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
		showLoaderOnConfirm: true,

		preConfirm: function() {
			return new Promise(function(resolve) {
				$.ajax({
					type: 'DELETE',
					url: '{{ url('children') }}' + '/' + childrenId,
					data: {
							_token : $('meta[name=csrf-token]').attr('content'),
							id: childrenId,
					},
					dataType: 'json'
				})
				.done(function(response){
					swal.fire('Deleted!', response.message, response.status)
					.then(function(){
						window.location.reload(true);
					});
					//$('#disable_user_' + childrenId).parent().parent().remove();
				})
				.fail(function(){
					swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
				})
			});
		},
		allowOutsideClick: false
	})
	.then((result) => {
		if (result.dismiss === swal.DismissReason.cancel) {
			swal.fire('Cancelled', 'Your data is safe from delete', 'info')
		}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////
// delete emergency contact
$(document).on('click', '.emergency_delete', function(e){
	var emergencyId = $(this).data('id');
	SwalEmergDelete(emergencyId);
	e.preventDefault();
});

function SwalEmergDelete(emergencyId){
	swal.fire({
		title: 'Are you sure?',
		text: "It will be deleted permanently!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
		showLoaderOnConfirm: true,

		preConfirm: function() {
			return new Promise(function(resolve) {
				$.ajax({
					type: 'DELETE',
					url: '{{ url('emergencycontact') }}' + '/' + emergencyId,
					data: {
							_token : $('meta[name=csrf-token]').attr('content'),
							id: emergencyId,
					},
					dataType: 'json'
				})
				.done(function(response){
					swal.fire('Deleted!', response.message, response.status)
					.then(function(){
						window.location.reload(true);
					});
					//$('#disable_user_' + emergencyId).parent().parent().remove();
				})
				.fail(function(){
					swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
				})
			});
		},
		allowOutsideClick: false
	})
	.then((result) => {
		if (result.dismiss === swal.DismissReason.cancel) {
			swal.fire('Cancelled', 'Your data is safe from delete', 'info')
		}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////
// delete crossbackup
$(document).on('click', '.crossbackup_delete', function(e){
	var crossbackupId = $(this).data('id');
	DeleteCrossBackUp(crossbackupId);
	e.preventDefault();
});

function DeleteCrossBackUp(crossbackupId){
	swal.fire({
		title: 'Are you sure?',
		text: "It will be deleted permanently!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
		showLoaderOnConfirm: true,

		preConfirm: function() {
			return new Promise(function(resolve) {
				$.ajax({
					type: 'DELETE',
					url: '{{ url('deletecrossbackup') }}' + '/' + {{ $staff->id }},
					data: {
							_token : $('meta[name=csrf-token]').attr('content'),
							id: crossbackupId,
					},
					dataType: 'json'
				})
				.done(function(response){
					swal.fire('Deleted!', response.message, response.status)
					.then(function(){
						window.location.reload(true);
					});
					//$('#disable_user_' + crossbackupId).parent().parent().remove();
				})
				.fail(function(){
					swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
				})
			});
		},
		allowOutsideClick: false
	})
	.then((result) => {
		if (result.dismiss === swal.DismissReason.cancel) {
			swal.fire('Cancelled', 'Your data is safe from delete', 'info')
		}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////
$('#dob, #jpo').datetimepicker({
	icons: {
		time: "fas fas-regular fa-clock fa-beat",
		date: "fas fas-regular fa-calendar fa-beat",
		up: "fa-regular fa-circle-up fa-beat",
		down: "fa-regular fa-circle-down fa-beat",
		previous: 'fas fas-regular fa-arrow-left fa-beat',
		next: 'fas fas-regular fa-arrow-right fa-beat',
		today: 'fas fas-regular fa-calenday-day fa-beat',
		clear: 'fas fas-regular fa-broom-wide fa-beat',
		close: 'fas fas-regular fa-rectangle-xmark fa-beat'
	},
	format: 'YYYY-MM-DD',
	useCurrent: true,
});

// select2 on supposed to be
$('#rel, #gen, #rac, #nat, #mar').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});

$('#gen_1').on('change', function () {
		if( $(this).val() == 2 ) {
			console.log($('#append').length);
			if( $('#append').length == 0 ) {
				$('#wrapmaternity').append(
					'<div id="append">' +
						'<div class="form-group row mb-3 {{ $errors->has('maternity_leave') ? 'has-error' : '' }}">' +
							'<label for="matl" id="matl" class="col-sm-4 col-form-label">Maternity Leave : </label>' +
							'<div class="col-auto">' +
								'<input type="text" name="maternity_leave" value="{{ old('maternity_leave', $staff->maternity_leave) }}" id="matl" class="form-control form-control-sm col-sm-12 @error('maternity_leave') is-invalid @enderror" placeholder="Maternity Leave">' +
							'</div>' +
						'</div>' +
					'</div>'
				);
				$('#form').bootstrapValidator('addField', $('#append').find('[name="maternity_leave"]'));
			}
		}
});

$('#gen_0').on('change', function () {
	if($(this).val() == 1) {
		$('#append').remove();
		$('#form').bootstrapValidator('removeField', $('#append').find('[name="maternity_leave"]'));
	}
});

$('#sta').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
})


$('#cat').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});
$('#cat').on("select2:select", function (e) {
	$('#dep').val(null).trigger('change');
});
$('#cat').on("select2:unselect", function (e) {
	$('#dep').val(null).trigger('change');
});

$('#bra').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});
$('#bra').on("select2:select", function (e) {
	$('#dep').val(null).trigger('change');
});
$('#bra').on("select2:unselect", function (e) {
	$('#dep').val(null).trigger('change');
});

$('#dep').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
	ajax: {
		url: '{{ route('department.department') }}',
		type: 'POST',
		dataType: 'json',
		data: function (params) {
			var query = {
				branch_id: $('#bra').val(),
				category_id: $('#cat').val(),
				_token: '{!! csrf_token() !!}',
				search: params.term,
			}
			return query;
		}
	},
});

$('#him').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});

@if($staff->crossbackupto()->wherePivot('active', 1)->get()->count())
<?php $i=1 ?>
	@foreach($staff->crossbackupto()->wherePivot('active', 1)->get() as $st)
		$('#sta_{{ $i }}').select2({
			placeholder: 'Please Select',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			//ajax: {
			//	url: '{{ route('staffcrossbackup.staffcrossbackup') }}',
			//	type: 'POST',
			//	dataType: 'json',
			//	data: function(params){
			//			var query = {
			//				_token: '{!! csrf_token() !!}',
			//				search: params.term,
			//			}
			//			return query;
			//		}
			//},
		});
	<?php $i++ ?>
	@endforeach
@endif

$('#rdg').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
	ajax: {
		url: '{{ route('restdaygroup.restdaygroup') }}',
		type: 'POST',
		dataType: 'json',
		data: function (params) {
			var query = {
				_token: '{!! csrf_token() !!}',
				search: params.term,
			}
			return query;
		}
	},
});

/////////////////////////////////////////////////////////////////////////////////////////
@if($staff->hasmanychildren()->get()->count())
	<?php $i=1 ?>
	@foreach($staff->hasmanychildren()->get() as $child)
		$('#cdo_{{ $i }}').datetimepicker({
			icons: {
				time: "fas fas-regular fa-clock fa-beat",
				date: "fas fas-regular fa-calendar fa-beat",
				up: "fa-regular fa-circle-up fa-beat",
				down: "fa-regular fa-circle-down fa-beat",
				previous: 'fas fas-regular fa-arrow-left fa-beat',
				next: 'fas fas-regular fa-arrow-right fa-beat',
				today: 'fas fas-regular fa-calenday-day fa-beat',
				clear: 'fas fas-regular fa-broom-wide fa-beat',
				close: 'fas fas-regular fa-rectangle-xmark fa-beat'
			},
			format: 'YYYY-MM-DD',
			useCurrent: true,
		});

// 	$('#cge_{{ $i }}').select2({
// 			placeholder: 'Gender',
// 			width: '100%',
// 			allowClear: true,
// 			closeOnSelect: true,
// 			// ajax: {
// 			// 	url: '{{ route('gender.gender') }}',
// 			// 	type: 'POST',
// 			// 	dataType: 'json',
// 			// 	data: function (params) {
// 			// 		var query = {
// 			// 			_token: '{!! csrf_token() !!}',
// 			// 			search: params.term,
// 			// 		}
// 			// 		return query;
// 			// 	}
// 			// },
// 	});

// 	$('#cel_{{ $i }}').select2({
// 			placeholder: 'Education Level',
// 			width: '100%',
// 			allowClear: true,
// 			closeOnSelect: true,
// 			// ajax: {
// 			// 	url: '{{ route('educationlevel.educationlevel') }}',
// 			// 	type: 'POST',
// 			// 	dataType: 'json',
// 			// 	data: function (params) {
// 			// 		var query = {
// 			// 			_token: '{!! csrf_token() !!}',
// 			// 			search: params.term,
// 			// 		}
// 			// 		return query;
// 			// 	}
// 			// },
// 	});

// 	$('#chs_{{ $i }}').select2({
// 			placeholder: 'Health Status',
// 			width: '100%',
// 			allowClear: true,
// 			closeOnSelect: true,
// 			// ajax: {
// 			// 	url: '{{ route('healthstatus.healthstatus') }}',
// 			// 	type: 'POST',
// 			// 	dataType: 'json',
// 			// 	data: function (params) {
// 			// 		var query = {
// 			// 			_token: '{!! csrf_token() !!}',
// 			// 			search: params.term,
// 			// 		}
// 			// 		return query;
// 			// 	}
// 			// },
// 	});

// 	$('#ctep_{{ $i }}').select2({
// 			placeholder: 'Tax Exemption Percentage',
// 			width: '100%',
// 			allowClear: true,
// 			closeOnSelect: true,
// 			// ajax: {
// 			// 	url: '{{ route('taxexemptionpercentage.taxexemptionpercentage') }}',
// 			// 	type: 'POST',
// 			// 	dataType: 'json',
// 			// 	data: function (params) {
// 			// 		var query = {
// 			// 			_token: '{!! csrf_token() !!}',
// 			// 			search: params.term,
// 			// 		}
// 			// 		return query;
// 			// 	}
// 			// },
// 	});
		<?php $i++ ?>
	@endforeach
@endif

// $('#ere_1').select2({
// 	placeholder: 'Relationship',
// 	width: '100%',
// 	allowClear: true,
// 	closeOnSelect: true,
// 	ajax: {
// 		url: '{{ route('relationship.relationship') }}',
// 		type: 'POST',
// 		dataType: 'json',
// 		data: function (params) {
// 			var query = {
// 				_token: '{!! csrf_token() !!}',
// 				search: params.term,
// 			}
// 			return query;
// 		}
// 	},
// });

/////////////////////////////////////////////////////////////////////////////////////////
// add spouse : add and remove row

var max_fields  = 4;						//maximum input boxes allowed
var add_buttons	= $(".spouse_add");
var wrappers	= $(".spouse_wrap");

var xs = {{ ($staff->hasmanyspouse()->get()->isNotEmpty())?$staff->hasmanyspouse()->get()->count():1 }};
$(add_buttons).click(function(){
	// e.preventDefault();

	//max input box allowed
	if(xs < max_fields){
		xs++;
		wrappers.append(

			'<div class="row m-1 spouse_row">' +
				'<div class="col-sm-1">' +
					'<button class="btn btn-sm btn-outline-secondary spouse_remove" type="button">' +
						'<i class="fas fa-trash" aria-hidden="true"></i>' +
					'</button>' +
				'</div>' +
				'<div class="col-sm-11 form-group {{ $errors->has('staffspouse.*.spouse') ? 'has-error' : '' }}">' +
					'<input type="hidden" name="staffspouse[' + xs + '][id]" value="">' +
					'<input type="text" name="staffspouse[' + xs + '][spouse]" id="spo" class="form-control form-control-sm" placeholder="Spouse">' +
				'</div>' +
				'<div class="col-sm-1"></div>' +
				'<div class="col-sm-5 form-group {{ $errors->has('staffspouse.*.phone') ? 'has-error' : '' }}">' +
					'<input type="text" name="staffspouse[' + xs + '][phone]" id="pho" class="form-control form-control-sm" placeholder="Spouse Phone">' +
				'</div>' +
				'<div class="col-sm-6 form-group {{ $errors->has('staffspouse.*.profession') ? 'has-error' : '' }}">' +
					'<input type="text" name="staffspouse[' + xs + '][profession]" id="pro" class="form-control form-control-sm" placeholder="Spouse Profession">' +
				'</div>' +
			'</div>'

		); //add input box

		//bootstrap validate
		$('#form').bootstrapValidator('addField',	$('.spouse_row')	.find('[name="staffspouse['+ xs +'][spouse]"]'));
		$('#form').bootstrapValidator('addField',	$('.spouse_row')	.find('[name="staffspouse['+ xs +'][phone]"]'));
		$('#form').bootstrapValidator('addField',	$('.spouse_row')	.find('[name="staffspouse['+ xs +'][profession]"]'));
	}
})

$(wrappers).on("click",".spouse_remove", function(e){
	//user click on remove text
	e.preventDefault();
	var $row = $(this).parent().parent();
	var $option1 = $row.find('[name="staffspouse[' + xs + '][spouse]"]');
	var $option2 = $row.find('[name="staffspouse[' + xs + '][phone]"]');
	var $option3 = $row.find('[name="staffspouse[' + xs + '][profession]"]');
	$row.remove();

	$('#form').bootstrapValidator('removeField', $option1);
	$('#form').bootstrapValidator('removeField', $option2);
	$('#form').bootstrapValidator('removeField', $option3);
	console.log();
	xs--;
})

/////////////////////////////////////////////////////////////////////////////////////////
// add children : add and remove row

var cmax_fields  = 12;						//maximum input boxes allowed
var cadd_buttons	= $(".children_add");
var cwrappers	= $(".children_wrap");

var xc = {{ ($staff->hasmanychildren()->get()->isNotEmpty())?$staff->hasmanychildren()->get()->count():1 }};
$(cadd_buttons).click(function(){
	// e.preventDefault();

	//max input box allowed
	if(xc < cmax_fields){
		xc++;
		cwrappers.append(
			'<div class="row m-1 children_row">' +
				'<div class="col-sm-1">' +
					'<button class="btn btn-sm btn-outline-secondary children_remove" type="button">' +
						'<i class="fas fa-trash" aria-hidden="true"></i>' +
					'</button>' +
				'</div>' +
				'<div class="col-sm-11 form-group {{ $errors->has('staffchildren.*.children') ? 'has-error' : '' }}">' +
					'<input type="hidden" name="staffchildren[' + xc + '][id]" value="">' +
					'<input type="text" name="staffchildren[' + xc + '][children]" id="chi_' + xc + '" class="form-control form-control-sm" placeholder="Children">' +
				'</div>' +
				'<div class="col-sm-1"></div>' +
				'<div class="col-sm-7 form-group {{ $errors->has('staffchildren.*.dob') ? 'has-error' : '' }}">' +
					'<input type="text" name="staffchildren[' + xc + '][dob]" value="" id="cdo_' + xc + '" class="form-control form-control-sm" placeholder="Date Of Birth">' +
				'</div>' +
				'<div class="col-sm-4 form-group {{ $errors->has('staffchildren.*.gender_id') ? 'has-error' : '' }}">' +
					'<select name="staffchildren[' + xc + '][gender_id]" id="cge_' + xc + '" class="form-select form-select-sm" placeholder="Gender"></select>' +
				'</div>' +
				'<div class="col-sm-1"></div>' +
				'<div class="col-sm-7 form-group {{ $errors->has('staffchildren.*.education_level_id') ? 'has-error' : '' }}">' +
					'<select name="staffchildren[' + xc + '][education_level_id]" id="cel_' + xc + '" class="form-select form-select-sm" placeholder="Education Level"></select>' +
				'</div>' +
				'<div class="col-sm-4 form-group {{ $errors->has('staffchildren.*.health_status_id') ? 'has-error' : '' }}">' +
					'<select name="staffchildren[' + xc + '][health_status_id]" id="chs_' + xc + '" class="form-select form-select-sm" placeholder="Health Status"></select>' +
				'</div>' +
				'<div class="col-sm-1"></div>' +
				'<div class="form-group form-check col-sm-5 {{ $errors->has('staffchildren.*.tax_exemption') ? 'has-error' : '' }}">' +
					'<input type="hidden" name="staffchildren[' + xc + '][tax_exemption]" class="form-check-input" value="0">' +
					'<input type="checkbox" name="staffchildren[' + xc + '][tax_exemption]" class="form-check-input" value="1" id="cte_' + xc + '">' +
					'<label class="form-check-label" for="cte_' + xc + '">Valid for Tax Exemption?</label>' +
				'</div>' +
				'<div class="col-sm-6 form-group {{ $errors->has('staffchildren.*.tax_exemption_percentage_id') ? 'has-error' : '' }}">' +
					'<select name="staffchildren[' + xc + '][tax_exemption_percentage_id]" id="ctep_' + xc + '" class="form-select form-select-sm" placeholder="Tax Exemption Percentage"></select>' +
				'</div>' +
			'</div>'
		); //add input box

		$('#cge_' + xc +'').select2({
			placeholder: 'Gender',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('gender.gender') }}',
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						search: params.term,
					}
					return query;
				}
			},
		});

		$('#cel_' + xc +'').select2({
			placeholder: 'Education Level',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('educationlevel.educationlevel') }}',
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						search: params.term,
					}
					return query;
				}
			},
		});

		$('#cdo_' + xc).datetimepicker({
			icons: {
				time: "fas fas-regular fa-clock fa-beat",
				date: "fas fas-regular fa-calendar fa-beat",
				up: "fa-regular fa-circle-up fa-beat",
				down: "fa-regular fa-circle-down fa-beat",
				previous: 'fas fas-regular fa-arrow-left fa-beat',
				next: 'fas fas-regular fa-arrow-right fa-beat',
				today: 'fas fas-regular fa-calenday-day fa-beat',
				clear: 'fas fas-regular fa-broom-wide fa-beat',
				close: 'fas fas-regular fa-rectangle-xmark fa-beat'
			},
			format: 'YYYY-MM-DD',
			useCurrent: true,
		});

		$('#chs_' + xc +'').select2({
			placeholder: 'Health Status',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('healthstatus.healthstatus') }}',
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						search: params.term,
					}
					return query;
				}
			},
		});

		$('#ctep_' + xc +'').select2({
			placeholder: 'Tax Exemption Percentage',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('taxexemptionpercentage.taxexemptionpercentage') }}',
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						search: params.term,
					}
					return query;
				}
			},
		});

		//bootstrap validate
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffchildren['+ xc +'][children]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffchildren['+ xc +'][gender_id]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffchildren['+ xc +'][education_level_id]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffchildren['+ xc +'][health_status_id]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffchildren['+ xc +'][tax_exemption]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffchildren['+ xc +'][tax_exemption_percentage_id]"]'));
	}
})

$(cwrappers).on("click",".children_remove", function(e){
	//user click on remove text
	e.preventDefault();
	var $row = $(this).parent().parent();
	var $option1 = $row.find('[name="staffchildren[' + xc + '][children]"]');
	var $option2 = $row.find('[name="staffchildren[' + xc + '][gender_id]"]');
	var $option3 = $row.find('[name="staffchildren[' + xc + '][education_level_id]"]');
	var $option4 = $row.find('[name="staffchildren[' + xc + '][health_status_id]"]');
	var $option5 = $row.find('[name="staffchildren[' + xc + '][tax_exemption]"]');
	var $option6 = $row.find('[name="staffchildren[' + xc + '][tax_exemption_percentage_id]"]');
	$row.remove();

	$('#form').bootstrapValidator('removeField', $option1);
	$('#form').bootstrapValidator('removeField', $option2);
	$('#form').bootstrapValidator('removeField', $option3);
	$('#form').bootstrapValidator('removeField', $option4);
	$('#form').bootstrapValidator('removeField', $option5);
	$('#form').bootstrapValidator('removeField', $option6);
	console.log();
	xc--;
})

/////////////////////////////////////////////////////////////////////////////////////////
// add emergency : add and remove row

var emax_fields = 3;						//maximum input boxes allowed
var eadd_buttons = $(".emergency_add");
var ewrappers = $(".emergency_wrap");

var xe = {{ ($staff->hasmanyemergency()->get()->isNotEmpty())?$staff->hasmanyemergency()->get()->count():1 }};
$(eadd_buttons).click(function(){
	// e.preventDefault();

	//max input box allowed
	if(xe < emax_fields){
		xe++;
		ewrappers.append(
			'<div class="row m-1 emergency_row">' +
				'<div class="col-sm-1">' +
					'<button class="btn btn-sm btn-outline-secondary emergency_remove" type="button">' +
						'<i class="fas fa-trash" aria-hidden="true"></i>' +
					'</button>' +
				'</div>' +
				'<div class="col-sm-11 form-group {{ $errors->has('staffemergency.*.contact_person') ? 'has-error' : '' }}">' +
					'<input type="hidden" name="staffemergency[' + xe + '][id]" value="">' +
					'<input type="text" name="staffemergency[' + xe + '][contact_person]" id="ecp_' + xe + '" class="form-control form-control-sm" placeholder="Emergency Contact">' +
				'</div>' +
				'<div class="col-sm-1"></div>' +
				'<div class="col-sm-5 form-group {{ $errors->has('staffemergency.*.phone') ? 'has-error' : '' }}">' +
					'<input type="text" name="staffemergency[' + xe + '][phone]" id="epp_' + xe + '" class="form-control form-control-sm" placeholder="Phone">' +
				'</div>' +
				'<div class="col-sm-6 form-group {{ $errors->has('staffemergency.*.relationship_id') ? 'has-error' : '' }}">' +
					'<select name="staffemergency[' + xe + '][relationship_id]" id="ere_' + xe + '" class="form-select form-select-sm" placeholder="Relationship"></select>' +
				'</div>' +
				'<div class="col-sm-1"></div>' +
				'<div class="col-sm-11 form-group {{ $errors->has('staffemergency.*.address') ? 'has-error' : '' }}">' +
					'<input type="textarea" name="staffemergency[' + xe + '][address]" id="ead_' + xe + '" class="form-control form-control-sm" placeholder="Address">' +
				'</div>' +
			'</div>'
		); //add input box

		$('#ere_' + xe +'').select2({
			placeholder: 'Relationship',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('relationship.relationship') }}',
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						search: params.term,
					}
					return query;
				}
			},
		});


		//bootstrap validate
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffemergency['+ xe +'][contact_person]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffemergency['+ xe +'][phone]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffemergency['+ xe +'][relationship_id]"]'));
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="staffemergency['+ xe +'][address]"]'));
	}
})

$(ewrappers).on("click",".emergency_remove", function(e){
	//user click on remove text
	e.preventDefault();
	var $row = $(this).parent().parent();
	var $option1 = $row.find('[name="staffemergency[' + xe + '][contact_person]"]');
	var $option2 = $row.find('[name="staffemergency[' + xe + '][phone]"]');
	var $option3 = $row.find('[name="staffemergency[' + xe + '][relationship_id]"]');
	var $option4 = $row.find('[name="staffemergency[' + xe + '][address]"]');
	$row.remove();

	$('#form').bootstrapValidator('removeField', $option1);
	$('#form').bootstrapValidator('removeField', $option2);
	$('#form').bootstrapValidator('removeField', $option3);
	$('#form').bootstrapValidator('removeField', $option4);
	console.log();
	xe--;
})

/////////////////////////////////////////////////////////////////////////////////////////
// add cross backup : add and remove row

var crb_max_fields = 5;						//maximum input boxes allowed
var crb_add_buttons = $(".crossbackup_add");
var crb_wrappers = $(".crossbackup_wrap");

var xcrb = {{ ($staff->crossbackupto()->get()->isnotEmpty())?$staff->crossbackupto()->get()->count():1 }};
$(crb_add_buttons).click(function(){
	// e.preventDefault();

	//max input box allowed
	if(xcrb < crb_max_fields){
		xcrb++;
		crb_wrappers.append(
			'<div class="row m-1 p-0 crossbackup_row">' +
					'<div class="col-sm-1">' +
						'<button class="btn btn-sm btn-outline-secondary crossbackup_remove" type="button">' +
							'<i class="fas fa-trash" aria-hidden="true"></i>' +
						'</button>' +
					'</div>' +
					'<div class="col-sm-10 form-group {{ $errors->has('crossbackup.*.backup_staff_id') ? 'has-error' : '' }}">' +
						'<select name="crossbackup[' + xcrb + '][backup_staff_id]" id="sta_' + xcrb + '" class="form-select form-select-sm" placeholder="Cross Backup Personnel"></select>' +
					'</div>' +
				'</div>' +
			'</div>'
		);

		$('#sta_' + xcrb ).select2({
			placeholder: 'Please Select',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('staffcrossbackup.staffcrossbackup') }}',
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						search: params.term,
					}
					return query;
				}
			},
		});


		//bootstrap validate
		$('#form').bootstrapValidator('addField',	$('.children_row')	.find('[name="crossbackup['+ xcrb +'][backup_staff_id]"]'));
	}
})

$(crb_wrappers).on("click",".crossbackup_remove", function(e){
	//user click on remove text
	e.preventDefault();
	var $row = $(this).parent().parent();
	var $option1 = $row.find('[name="crossbackup[' + xcrb + '][backup_staff_id]"]');
	$row.remove();

	$('#form').bootstrapValidator('removeField', $option1);
	xcrb--;
})

/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator
$('#form').bootstrapValidator({
	fields: {
		username: {
			validators: {
				notEmpty: {
					message: 'Please insert username. '
				},
				// remote: {
				// 	type: 'POST',
				// 	url: '{{ route('loginuser') }}',
				// 	message: 'Username exist. Please use another username. ',
				// 	data: function(validator) {
				// 				return {
				// 							_token: '{!! csrf_token() !!}',
				// 							username: $('#unam').val(),
				// 				};
				// 			},
				// 	delay: 1,		// wait 0.001 seconds
				// },
			}
		},
		password: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert password. '
				// },
			}
		},
		status_id: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
			}
		},
		category_id: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
			}
		},
		branch_id: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
			}
		},
		pivot_dept_id: {
			validators: {
				notEmpty: {
					field: 'branch_id',
					field: 'category_id',
					message: 'Please choose. '
				},
			}
		},
		div_id: {
			validators: {
				// notEmpty: {
				// 	field: 'pivot_dept_id',
				// 	message: 'Please choose. '
				// },
			}
		},
		restday_group_id: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
			}
		},
		authorise_id: {
			validators: {
				// notEmpty: {
				// 	message: 'Please choose. '
				// },
			}
		},
		leave_flow_id: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
			}
		},
		annual_leave: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
				numeric: {
					separator: '.',
					message: 'Numbers must be in decimal ',
				},
				step: {
					baseValue: 0,
					step: 0.5,
					message: 'Number increase must be in 0.5 ',
				},
			}
		},
		mc_leave: {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
				numeric: {
					separator: '.',
					message: 'Numbers must be in decimal ',
				},
				step: {
					baseValue: 0,
					step: 0.5,
					message: 'Number increase must be in 0.5 ',
				},
			}
		},
		maternity_leave: {
			validators: {
				// notEmpty: {
				// 	message: 'Please choose. '
				// },
				numeric: {
					separator: '.',
					message: 'Numbers must be in decimal ',
				},
				step: {
					baseValue: 0,
					step: 0.5,
					message: 'Number increase must be in 0.5 ',
				},
			}
		},
		name: {
			validators: {
				notEmpty: {
					message: 'Please insert new staff name. '
				},
			}
		},
		ic: {
			validators: {
				notEmpty: {
					message: 'Please insert Identity Card or Passport. '
				},
				// digits: {
				// 	message: 'Only numbers '
				// },
			}
		},
		religion_id: {
			validators: {
				// notEmpty: {
				// 	message: 'Please select. '
				// },
			}
		},
		gender_id: {
			validators: {
				notEmpty: {
					message: 'Please select. '
				},
			}
		},
		race_id: {
			validators: {
				// notEmpty: {
				// 	message: 'Please select. '
				// },
			}
		},
		nationality_id: {
			validators: {
				// notEmpty: {
				// 	message: 'Please select. '
				// },
			}
		},
		marital_status_id: {
			validators: {
				notEmpty: {
					message: 'Please select. '
				},
			}
		},
		email: {
			validators: {
				notEmpty: {
					message: 'Please insert email. '
				},
				emailAddress: {
					message: 'Please insert valid email '
				},
			}
		},
		address: {
			validators: {
				notEmpty: {
					message: 'Please insert address. '
				},
			}
		},
		mobile: {
			validators: {
				notEmpty: {
					message: 'Please insert mobile. '
				},
				digits: {
					message: 'Please insert valid mobile number '
				},
			}
		},
		phone: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				digits: {
					message: 'Please insert valid mobile number '
				},
			}
		},
		dob: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert valid mobile number '
				},
			}
		},
		cimb_account: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				digits: {
					message: 'Please insert valid mobile number '
				},
			}
		},
		epf_account: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				digits: {
					message: 'Please insert valid mobile number '
				},
			}
		},
		income_tax_no: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				// digits: {
				// 	message: 'Please insert valid mobile number '
				// },
			}
		},
		socso_no: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				digits: {
					message: 'Please insert valid mobile number '
				},
			}
		},
		weight: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				numeric: {
					separator: '.',
					message: 'Only numbers. '
				},
			}
		},
		height: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				numeric: {
					separator: '.',
					message: 'Only numbers. '
				}
			}
		},
		join: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				date: {
					format: 'YYYY-MM-DD',
					message: 'The value is not a valid date. '
				},
			}
		},
		confirmed: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert phone. '
				// },
				date: {
					format: 'YYYY-MM-DD',
					message: 'The value is not a valid date. '
				},
			}
		},
		image: {
			validators: {
				file: {
					extension: 'jpeg,jpg,png,bmp',
					type: 'image/jpeg,image/png,image/bmp',
					maxSize: 2097152,	// 2048 * 1024,
					message: 'The selected file is not valid. Please use jpeg or png and the image is below than 3MB. '
				},
			}
		},

// spouse
@for ($ie = 1; $ie <= 4; $ie++)
		'staffspouse[{{ $ie }}][spouse]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse. '
				// },
				regexp: {
					regexp: /^[a-z\s\'\@]+$/i,
					message: 'The full name can consist of alphabetical characters, \', @ and spaces only'
				},
			}
		},
		'staffspouse[{{ $ie }}][phone]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse phone. '
				// },
				digits: {
					message: 'Only numbers. '
				},
			}
		},
		'staffspouse[{{ $ie }}][profession]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse profession. '
				// },
			}
		},
@endfor
// children
@for ($ic = 1; $ic <= 4; $ic++)
		'staffchildren[{{ $ic }}][children]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse. '
				// },
				regexp: {
					regexp: /^[a-z\s\'\@]+$/i,
					message: 'The full name can consist of alphabetical characters, \', @ and spaces only'
				},
			}
		},
		'staffchildren[{{ $ic }}][gender_id]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse phone. '
				// },
			}
		},
		'staffchildren[{{ $ic }}][education_level_id]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse profession. '
				// },
			}
		},
		'staffchildren[{{ $ic }}][health_status_id]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse profession. '
				// },
			}
		},
		'staffchildren[{{ $ic }}][tax_exemption]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse profession. '
				// },
				// numeric: {
				// 	message: 'Only numbers. '
				// },
			}
		},
@endfor
@for ($ie = 1; $ie <= 4; $ie++)
		'staffemergency[{{ $ie }}][contact_person]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse. '
				// },
				regexp: {
					regexp: /^[a-z\s\'\@]+$/i,
					message: 'The full name can consist of alphabetieal characters, \', @ and spaces only'
				},
			}
		},
		'staffemergency[{{ $ie }}][phone]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse phone. '
				// },
				digits: {
					message: 'Please insert valid phone number '
				},
			}
		},
		'staffemergency[{{ $ie }}][relationship_id]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse profession. '
				// },
			}
		},
		'staffemergency[{{ $ie }}][address]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert spouse profession. '
				// },
			}
		},
@endfor
@for ($ie = 1; $ie <= 5; $ie++)
		'crossbackup[{{ $ie }}][backup_staff_id]': {
			validators: {
				// notEmpty: {
				// 	message: 'Please choose '
				// },
			}
		},
@endfor
	}
});

@endsection
