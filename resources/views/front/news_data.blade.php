@if($news->count())
    @foreach($news as $new)
        <div class="col-md-6">
            <div class="custom-card">
                <img src="{{ asset('storage/' . $new['image']) }}" class="card-img-top" alt="{{$new['title']}}">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <small class="text-small"><i class="fa-solid fa-calendar-days"></i> {{$new['date']}}</small>
                        <small class="text-small"><i class="fa-solid fa-pencil"></i> {{$new['post_by']}}</small>
                    </div>
                    <h5 class="card-title">{{$new['title']}}</h5>
                    <p class="card-text">{{$new['short_description']}}</p>
                    <a href="{{ route('front.newsdetails', $new['id'])}}" class="read-more">Read More</a>
                </div>
            </div>
        </div>
    @endforeach
@endif
