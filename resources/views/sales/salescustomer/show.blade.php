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
    <div class="col-md-2">
      <h4>Customer Details</h4>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-2">
      Customer
    </div>
    <div class="col-md-10">
      <input type="text" name="customer" value="{{ old('customer', @$customer->customer) }}" id="id" class="form-control form-control-sm col-sm-12 @error('customer') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      Contact
    </div>
    <div class="col-md-10">
      <input type="text" name="contact" value="{{ old('contact', @$customer->contact) }}" id="id" class="form-control form-control-sm col-sm-12 @error('contact') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      Phone
    </div>
    <div class="col-md-10">
      <input type="text" name="phone" value="{{ old('phone', @$customer->phone) }}" id="id" class="form-control form-control-sm col-sm-12 @error('phone') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      Fax
    </div>
    <div class="col-md-10">
      <input type="text" name="fax" value="{{ old('fax', @$customer->fax) }}" id="id" class="form-control form-control-sm col-sm-12 @error('fax') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      Area
    </div>
    <div class="col-md-10">
      <input type="text" name="area" value="{{ old('area', @$customer->area) }}" id="id" class="form-control form-control-sm col-sm-12 @error('area') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      Address
    </div>
    <div class="col-md-10">
      <textarea name="address" id="id" class="form-control form-control-sm col-sm-12 @error('address') is-invalid @enderror" readonly>{{ old('address', @$customer->address) }}</textarea>
    </div>
  </div>

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
