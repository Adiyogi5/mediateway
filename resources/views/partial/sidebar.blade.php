<nav id="sidebar">
    <div class="shadow-bottom"></div>
    <ul class="list-unstyled menu-categories ps ps--active-y" id="accordionExample">
        <li class="menu @routeis('dashboard') active @endrouteis">
            <a href="{{ route('dashboard') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-house"></i>
                    <span>Dashboard</span>
                </div>
            </a>
        </li>
        @if(Helper::userCan([102,103]))
        <li class="menu @routeis('roles,users') active @endrouteis">
            <a href="#master" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('roles,users') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-solid fa-sparkles"></i>
                    <span>Master</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('roles,users') show @endrouteis" id="master"
                data-bs-parent="#accordionExample">
                @if(Helper::userCan(102))
                <li class="@routeis('roles') active @endrouteis">
                    <a href="{{ route('roles') }}">Roles</a>
                </li>
                @endif
                @if(Helper::userCan(103))
                <li class="@routeis('users') active @endrouteis">
                    <a href="{{ route('users') }}">Sub Admins</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(Helper::userCan([104]))
        <li class="menu @routeis('sliders,testimonials,homecms,cms,faq,enquiries,banners,features,clients') active @endrouteis">
            <a href="#static_content" data-bs-toggle="collapse"
                aria-expanded="{{ Helper::routeis('sliders,testimonials,homecms,cms,faq,enquiries,banners,features,clients') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-sharp fa-solid fa-photo-film"></i>
                    <span>Content</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('sliders,testimonials,homecms,cms,faq,features,enquiries,banners,clients') show @endrouteis"
                id="static_content" data-bs-parent="#accordionExample">

                @if(Helper::userCan(112))
                <li class="@routeis('banners') active @endrouteis">
                    <a href="{{ route('banners') }}">Banners</a>
                </li>
                @endif

                @if(Helper::userCan(104))
                <li class="@routeis('homecms') active @endrouteis">
                    <a href="{{ route('homecms') }}">Home CMS</a>
                </li>
                @endif
                
                @if(Helper::userCan(104))
                <li class="@routeis('cms') active @endrouteis">
                    <a href="{{ route('cms') }}">CMS</a>
                </li>
                @endif
                
                @if(Helper::userCan(110))
                <li class="@routeis('faqs') active @endrouteis">
                    <a href="{{ route('faqs') }}">FAQs</a>
                </li>
                @endif

                @if(Helper::userCan(110))
                <li class="@routeis('features') active @endrouteis">
                    <a href="{{ route('features') }}">Features</a>
                </li>
                @endif

                @if(Helper::userCan(110))
                <li class="@routeis('testimonials') active @endrouteis">
                    <a href="{{ route('testimonials') }}">Testimonials</a>
                </li>
                @endif

                @if(Helper::userCan(110))
                <li class="@routeis('clients') active @endrouteis">
                    <a href="{{ route('clients') }}">Clients</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(Helper::userCan([105,106]))
        <li class="menu @routeis('states,cities') active @endrouteis">
            <a href="#location_content" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('states,cities') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-location-dot"></i>
                    <span>Location</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('states,cities') show @endrouteis" id="location_content"
                data-bs-parent="#accordionExample">
                @if(Helper::userCan(105))
                <li class="@routeis('states') active @endrouteis">
                    <a class="nav-link" href="{{ route('states') }}">States</a>
                </li>
                @endif

                @if(Helper::userCan(106))
                <li class="@routeis('cities') active @endrouteis">
                    <a class="nav-link" href="{{ route('cities') }}">Cities</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if(Helper::userCan(101))
        <li class="menu @routeis('setting') active @endrouteis">
            <a href="#setting" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('setting') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa fa-cog my-auto"></i>
                    <span>App Setting</span>
                </div>
                <div><i class="fa-solid fa-chevron-right"></i></div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('setting') show @endrouteis" id="setting"
                data-bs-parent="#accordionExample">
                @foreach(config('constant.setting_array', []) as $key => $setting)
                <li class="@if(request()->path() == 'setting/'.$key) active @endif">
                    <a class="nav-link" href="{{ route('setting', ['id' => $key]) }}">
                        {{ $setting }}
                    </a>
                </li>
                @endforeach
            </ul>
        </li>

        <li class="menu">
            <a href="{{route('database_backup')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-database"></i>
                    <span>Database Backup</span>
                </div>
            </a>
        </li>

        <li class="menu  @routeis('server-control') active @endrouteis">
            <a href="{{ route('server-control') }}" aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-server"></i>
                    <span>Server Control Panel</span>
                </div>
            </a>
        </li>
        @endif
    </ul>
</nav>