@extends('layouts.app')

@section('css')
<link href="{{ asset('assets/plugins/summernote/summernote.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-header">
        <div class="row flex-between-end">
            <div class="col-auto align-self-center">
                <h5 class="mb-0" data-anchor="data-anchor">News :: News Edit </h5>
            </div>
            <div class="col-auto ms-auto">
                <div class="nav nav-pills nav-pills-falcon flex-grow-1 mt-2" role="tablist">
                    <a href="{{ route('news')  }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form class="row" id="ediUser" method="POST" action="{{ route('news.edit', $news['id']) }}"
            enctype='multipart/form-data'>
            @csrf
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="title">Title</label>
                <input class="form-control" id="title" placeholder="Title" name="title" type="text"
                    value="{{ old('title', $news['title'] )}}" />
                @error('title')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="post_by">Post By <span class="required">*</span></label>
                <input class="form-control" id="post_by" placeholder="Post By" name="post_by" type="text"
                    value="{{ old('post_by', $news['post_by']) }}" />
                @error('post_by')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="date">Date <span class="required">*</span></label>
                <input class="form-control" id="date" placeholder="Date" name="date" type="date"
                    value="{{ old('date', $news['date']) }}" />
                @error('date')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-6 mt-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" class="form-select" id="status">
                    <option value="1" @selected(old('status', $news['status'])==1)> Active </option>
                    <option value="0" @selected(old('status', $news['status'])==0)> Inactive </option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="col-lg-6 mt-2">
                <label class="form-label" for="image">Image</label>
                <div class="img-group mb-2">
                    <img class="" src="{{ asset('storage/' . $news['image']) }}" alt="">
                </div>
                <input class="form-control" id="image" name="image" type="file" value="" />
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-2">
                <label class="form-label" for="short_description">Short Description</label>
                <textarea class="form-control" id="short_description" name="short_description">{{ $news['short_description'] }}</textarea>
                @error('short_description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-2">
                <label class="form-label" for="description">Description </label>
                <textarea class="form-control" id="description" name="description">{{ $news['description'] }}</textarea>
                @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="col-lg-12 mt-3 d-flex justify-content-start">
                <button class="btn btn-secondary submitbtn" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/plugins/summernote/summernote.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#description').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview', 'help']],
            ]
        });

        let buttons = $('.note-editor button[data-toggle="dropdown"]');
        buttons.each((key, value) => {
            $(value).on('click', function (e) {
                $(this).attr('data-bs-toggle', 'dropdown')
            })
        });

        $("#ediUser").validate({
            ignore: ".ql-container *",
            rules: {
            title: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            post_by: {
                required: true,
                minlength: 2,
                maxlength: 100
            },
            date: {
                required: true,
            },
            short_description: {
                required: true,
            },
            description: {
                required: true,
            },
            image: {
                extension: "jpg|jpeg|png"
            }
        },
        messages: {
            title: {
                required: "Please enter title",
            },
            post_by: {
                required: "Please enter Post By",
            },
            date: {
                required: "Please Select date",
            },
            short_description: {
                required: "Please enter short description",
            },
            description: {
                required: "Please enter description",
            },
            image: {
                extension: "Supported Format Only : jpg, jpeg, png"
            }
        },
        });
    });
</script>
@endsection