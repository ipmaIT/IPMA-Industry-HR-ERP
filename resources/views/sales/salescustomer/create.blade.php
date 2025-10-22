@extends('layouts.app')

@section('content')
<style>
  /* div,
  table,
  tr,
  td {
    border: 1px solid black;
  } */
</style>

<?php
$no = 1;
?>

<div class="container">
  @include('sales.salesdept.navhr')

  <div class="row mt-3">
    <div class="col-sm-12">
      <h4>Add Customer</h4>
    </div>
  </div>

  <form method="POST" action="{{ route('salescustomer.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
    @csrf

  <div class="row mt-4">
    <label for="customer" class="col-form-label col-sm-2">Customer : </label>
    <div class="col-md-10">
      <input type="text" name="customer" value="{{ old('customer') }}" id="customer" class="form-control form-control-sm col-sm-12 @error('customer') is-invalid @enderror" placeholder="Customer">
    </div>
  </div>

  <div class="row mt-3">
      <label for="contact" class="col-form-label col-sm-2">Contact : </label>
    <div class="col-md-10">
      <input type="text" name="contact" value="{{ old('contact') }}" id="id" class="form-control form-control-sm col-sm-12 @error('contact') is-invalid @enderror" placeholder="Contact">
    </div>
  </div>

  <div class="row mt-3">
      <label for="phone" class="col-form-label col-sm-2">Phone : </label>
    <div class="col-md-10">
      <input type="text" name="phone" value="{{ old('phone') }}" id="phone" class="form-control form-control-sm col-sm-12 @error('phone') is-invalid @enderror" placeholder="Phone">
    </div>
  </div>

  <div class="row mt-3">
      <label for="fax" class="col-form-label col-sm-2">Fax : </label>
    <div class="col-md-10">
      <input type="text" name="fax" value="{{ old('fax') }}" id="fax" class="form-control form-control-sm col-sm-12 @error('fax') is-invalid @enderror" placeholder="Fax">
    </div>
  </div>

  <div class="row mt-3">
      <label for="area" class="col-form-label col-sm-2">Area : </label>
    <div class="col-md-10">
      <input type="text" name="area" value="{{ old('area') }}" id="area" class="form-control form-control-sm col-sm-12 @error('area') is-invalid @enderror" placeholder="Area">
    </div>
  </div>

  <div class="row mt-3">
      <label for="address" class="col-form-label col-sm-2">Address : </label>
    <div class="col-md-10">
      <textarea name="address" id="address" class="form-control form-control-sm col-sm-12 @error('address') is-invalid @enderror" placeholder="Address">{{ old('address') }}</textarea>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-12 text-center">
      <button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
    </div>
  </div>

  </form>

  <div class="row mt-3">
    <div class="col-md-12 text-center">
      <a href="">
        <button onclick="goBack()" class="btn btn-sm btn-outline-secondary" id="back">
          Back
        </button>
      </a>
    </div>
  </div>

</div>

@endsection

@section('js')
@endsection

@section('nonjquery')
function goBack() {
  window.history.back();
}
@endsection
