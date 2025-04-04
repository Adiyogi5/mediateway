@extends('layouts.front')

@section('content')
    {{-- Breadcrumb --}}
    @include('front.includes.profile_header')

    <div class="container blog-section">
        <div class="row my-5">
            <div class="col-lg-9">
                <div class="row" id="blog-container">
                    @include('front.news_data')
                </div>

                <div class="text-center mt-4">
                    <button id="load-more" class="btn btn-warning" data-page="2">Load More</button>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-3">
                <h5 class="popular-heading">POPULAR</h5>
                <div class="popular-list">
                    @foreach($newssidebar as $sidebar)
                    <div class="popular-item d-flex align-items-center">
                        <img src="{{ asset('storage/' . $sidebar['image']) }}" alt="{{$sidebar['title']}}">
                        <p class="card-text fw-bold">{{$sidebar['title']}}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#load-more').on('click', function () {
            let page = $(this).data('page');

            $.ajax({
                url: "{{ route('front.news') }}?page=" + page,
                type: "GET",
                beforeSend: function () {
                    $('#load-more').text('Loading...').prop('disabled', true);
                },
                success: function (data) {
                    if (data.trim() !== '') {
                        $('#blog-container').append(data);
                        $('#load-more').data('page', page + 1).text('Load More').prop('disabled', false);
                    } else {
                        $('#load-more').text('No More news').prop('disabled', true);
                    }
                },
                error: function () {
                    alert('Something went wrong!');
                    $('#load-more').text('Load More').prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection
