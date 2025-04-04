@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">Service Fee :: Service Fee Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1" role="tablist">
                    <a href="{{ route('servicefee')  }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('servicefee.edit', $servicefee['id']) }}"
            enctype='multipart/form-data'>
            @csrf

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="ticket_size_min">Ticket Size Minimum <span class="required">*</span></label>
                <input class="form-control" id="ticket_size_min" placeholder="Enter Min Ticket Size" name="ticket_size_min" type="text"
                    value="{{ old('ticket_size_min', $servicefee['ticket_size_min']) }}" />
                @error('ticket_size_min')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="ticket_size_max">Ticket Size Maximum <span class="required">*</span></label>
                <input class="form-control" id="ticket_size_max" placeholder="Enter Max Ticket Size" name="ticket_size_max" type="text"
                    value="{{ old('ticket_size_max', $servicefee['ticket_size_max']) }}" />
                @error('ticket_size_max')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="cost">Cost <span class="required">*</span></label>
                <input class="form-control" id="cost" placeholder="Enter Cost" name="cost" type="text"
                    value="{{ old('cost', $servicefee['cost']) }}" />
                @error('cost')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>  

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" @selected(old('status', $servicefee['status'])==1)> Active </option>
                    <option value="0" @selected(old('status', $servicefee['status'])==0)> Inactive </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-primary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $("#ediUser").validate({
        rules: {
            ticket_size_min: {
                required: true,
                minlength: 1,
                maxlength: 20
            },
            ticket_size_max: {
                required: true,
                minlength: 1,
                maxlength: 20
            },
            cost: {
                required: true,
                minlength: 1,
                maxlength: 20
            },
        },
        messages: {
            ticket_size_min: {
                required: "Please enter Min Ticket Size",
            },
            ticket_size_max: {
                required: "Please enter Max Ticket Size",
            },
            cost: {
                required: "Please Enetr Cost",
            },
        },
    });
</script>
@endsection