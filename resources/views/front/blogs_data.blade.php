@if($blogs->count())
    @foreach($blogs as $blog)
        <div class="col-md-6">
            <div class="custom-card">
                <img src="{{ asset('storage/' . $blog['image']) }}" class="card-img-top" alt="{{$blog['title']}}">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-small"><i class="fa-solid fa-calendar-days"></i> {{$blog['date']}}</small>
                        <small class="text-small"><i class="fa-solid fa-pencil"></i> {{$blog['post_by']}}</small>
                    </div>
                    <h5 class="card-title">{{$blog['title']}}</h5>
                    <p class="card-text">{{$blog['short_description']}}</p>
                    <a href="#" class="read-more">Read More</a>
                </div>
            </div>
        </div>
    @endforeach
@endif
