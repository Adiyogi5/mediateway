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
            <ul class="collapse submenu list-unstyled @routeis('sliders,testimonials,homecms,cms,blogs,news,faqs,features,enquiries,banners,clients') show @endrouteis"
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

                @if(Helper::userCan(104))
                <li class="@routeis('blogs') active @endrouteis">
                    <a href="{{ route('blogs') }}">Blogs</a>
                </li>
                @endif

                @if(Helper::userCan(104))
                <li class="@routeis('news') active @endrouteis">
                    <a href="{{ route('news') }}">News</a>
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

        <li class="menu @routeis('organizationlist') active @endrouteis">
            <a href="{{route('organizationlist')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-sitemap"></i>
                    <span>Organization List</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('cases.casebulkupdate') active @endrouteis">
            <a href="{{route('cases.casebulkupdate')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-upload"></i>
                    <span>Case Bulk Update</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('caseassign') active @endrouteis">
            <a href="{{route('caseassign')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-bookmark"></i>
                    <span>Cases Assign</span>
                </div>
            </a>
        </li>

        <li class="menu @routeis('allcasenotices.casenoticelist') active @endrouteis">
            <a href="{{route('allcasenotices.casenoticelist')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-flag"></i>
                    <span>All Notices</span>
                </div>
            </a>
        </li>

        {{-- @if(Helper::userCan([105,106])) --}}
        <li class="menu @routeis('noticetemplate,ordersheet,award,settlementletter,claimpetition') active @endrouteis">
            <a href="#location_content1" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('noticetemplate,ordersheet,award,settlementletter,claimpetition') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-file-lines"></i>
                    <span>Templates</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('noticetemplate,ordersheet,award,settlementletter,claimpetition') show @endrouteis" id="location_content1"
                data-bs-parent="#accordionExample">
                {{-- @if(Helper::userCan(105)) --}}
                <li class="@routeis('noticetemplate') active @endrouteis">
                    <a class="nav-link" href="{{ route('noticetemplate') }}">Notices</a>
                </li>
                {{-- @endif --}}

                {{-- @if(Helper::userCan(105)) --}}
                <li class="@routeis('ordersheet') active @endrouteis">
                    <a class="nav-link" href="{{ route('ordersheet') }}">Order Sheets</a>
                </li>
                {{-- @endif --}}

                {{-- @if(Helper::userCan(106)) --}}
                <li class="@routeis('award') active @endrouteis">
                    <a class="nav-link" href="{{ route('award') }}">Awards</a>
                </li>
                {{-- @endif --}}

                
                {{-- @if(Helper::userCan(106)) --}}
                <li class="@routeis('settlementletter') active @endrouteis">
                    <a class="nav-link" href="{{ route('settlementletter') }}">Settlement Letter</a>
                </li>
                {{-- @endif --}}

                {{-- @if(Helper::userCan(106)) --}}
                <li class="@routeis('claimpetition') active @endrouteis">
                    <a class="nav-link" href="{{ route('claimpetition') }}">Claim Petition</a>
                </li>
                {{-- @endif --}}
            </ul>
        </li>
        {{-- @endif --}}


        {{-- @if(Helper::userCan([105,106])) --}}
        <li class="menu @routeis('inquiries,bookappointments,callbacks') active @endrouteis">
            <a href="#location_content3" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('inquiries,bookappointments,callbacks') }}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-envelope"></i>
                    <span>Inquiries</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('inquiries,bookappointments,callbacks') show @endrouteis" id="location_content3"
                data-bs-parent="#accordionExample">
                {{-- @if(Helper::userCan(105)) --}}
                <li class="@routeis('inquiries') active @endrouteis">
                    <a class="nav-link" href="{{ route('inquiries') }}">Contact Inquiries</a>
                </li>
                {{-- @endif --}}

                {{-- @if(Helper::userCan(106)) --}}
                <li class="@routeis('bookappointments') active @endrouteis">
                    <a class="nav-link" href="{{ route('bookappointments') }}">Book Appointment Inquiries</a>
                </li>
                {{-- @endif --}}

                
                {{-- @if(Helper::userCan(106)) --}}
                <li class="@routeis('callbacks') active @endrouteis">
                    <a class="nav-link" href="{{ route('callbacks') }}">Call Back Inquiries</a>
                </li>
                {{-- @endif --}}
            </ul>
        </li>
        {{-- @endif --}}


        <li class="menu @routeis('servicefee') active @endrouteis">
            <a href="{{route('servicefee')}}" class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-file-lines"></i>
                    <span>Service Fee</span>
                </div>
            </a>
        </li>

        
        @if(Helper::userCan([105,106]))
        <li class="menu @routeis('states,cities') active @endrouteis">
            <a href="#location_content2" data-bs-toggle="collapse" aria-expanded="{{ Helper::routeis('states,cities') }}"
                class="dropdown-toggle">
                <div class="">
                    <i class="fa-duotone fa-location-dot"></i>
                    <span>Location</span>
                </div>
                <div> <i class="fa-solid fa-chevron-right"></i> </div>
            </a>
            <ul class="collapse submenu list-unstyled @routeis('states,cities') show @endrouteis" id="location_content2"
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