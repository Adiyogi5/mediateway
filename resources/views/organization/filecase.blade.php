@extends('layouts.front')

@section('content')
 {{-- ===============Breadcrumb Start============= --}}
 @include('front.includes.profile_header')
 {{-- ===============Breadcrumb End============= --}}
 
    <div class="container my-xl-5 my-lg-4 my-3">
        <div class="row">
            <div class="col-md-3 col-12">

                @include('front.includes.sidebar_inner')

            </div>

            <div class="col-md-9 col-12">
                <div class="card-inner form-validate">
                    <a href="{{ route('organization.filecase.sample') }}" class="btn btn-primary">Download Sample File</a>

                    <form action="{{ route('organization.filecases.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file">
                        <button type="submit">Import</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
      
    </script>
@endsection
