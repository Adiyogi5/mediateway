<div class="bg-purple py-5">
    <div class="container">
        <h1 class="text-gray fw-semi-bold text-uppercase">{{ $pageName ?? 'No Details' }}</h1>
        <ol class="breadcrumb" style="--bs-breadcrumb-divider : '/">
            <li class="breadcrumb-item">
                <a class="text-gray text-decoration-none" href="{{ route('front.home') }}">Home</a>
            </li>
            <li class="breadcrumb-item text-gray active" aria-current="page">{{ $pageName ?? 'No Details' }}</li>
        </ol>
    </div>
</div>
