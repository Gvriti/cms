  <nav class="navbar horizontal-menu{{$settings->get('layout_boxed') ? '' : ' navbar-fixed-top'}} {{$settings->get('horizontal_menu_minimal')}}"><!-- set fixed position by adding class "navbar-fixed-top" -->
    <div class="navbar-inner">
      <!-- Navbar Brand -->
      <div class="navbar-brand">
        <a href="{{ cms_url() }}" class="logo">
          <img src="{{ asset('assets/images/logo-white-bg@2x.png') }}" height="24" alt="Digital Design" class="hidden-xs" />
          <img src="{{ asset('assets/images/logo@2x.png') }}" height="24" alt="Digital Design" class="visible-xs" />
        </a>
        <a href="#" data-toggle="settings-pane" data-animate="true">
          <i class="fa fa-gear"></i>
        </a>
      </div>
      <!-- Mobile Toggles Links -->
      <div class="nav navbar-mobile">
        <!-- This will toggle the mobile menu and will be visible only on mobile devices -->
        <div class="mobile-menu-toggle">
          <!-- This will open the popup with user profile settings, you can use for any purpose, just be creative -->
          <a href="#" data-toggle="settings-pane" data-animate="true">
            <i class="fa fa-gear"></i>
          </a>
          <!-- data-toggle="mobile-menu-horizontal" will show horizontal menu links only -->
          <!-- data-toggle="mobile-menu" will show sidebar menu links only -->
          <!-- data-toggle="mobile-menu-both" will show sidebar and horizontal menu links -->
          <a href="#" data-toggle="mobile-menu-horizontal">
            <i class="fa fa-bars"></i>
          </a>
        </div>
      </div>
      <div class="navbar-mobile-clear"></div>
      <!-- main menu -->
      <ul class="navbar-nav {{$settings->get('horizontal_menu_click')}}">
        <li>
          <a href="{{ cms_route('menus.index') }}">
            <i class="{{icon_type('menus')}}" title="Menus"></i>
            <span class="title">Menus</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('menus.index') }}">
            <i class="{{icon_type('pages')}}" title="Pages"></i>
            <span class="title">Pages</span>
          </a>
        @if (! empty($menus))
          <ul>
          @foreach ($menus as $item)
            <li>
              <a href="{{ cms_route('pages.index', [$item->id]) }}">
                <span class="title">{{ $item->title }}</span>
              </a>
            </li>
          @endforeach
          </ul>
        @endif
        </li>
        <li>
          <a href="{{ cms_route('collections.index') }}">
            <i class="{{icon_type('collections')}}" title="Collections"></i>
            <span class="title">Collections</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('slider.index') }}">
            <i class="fa fa-photo" title="Slider"></i>
            <span class="title">Slider</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('filemanager') }}">
            <i class="fa fa-files-o" title="File Manager"></i>
            <span class="title">File Manager</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('cmsUsers.index') }}">
            <i class="fa fa-user-secret" title="CMS Users"></i>
            <span class="title">CMS Users</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('notes.index') }}">
            <i class="fa fa-file-text-o" title="Notes"></i>
            <span class="title">Notes</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('calendar.index') }}">
            <i class="fa fa-calendar" title="Calendar"></i>
            <span class="title">Calendar</span>
          </a>
        </li>
        <li>
          <a href="{{ cms_route('settings.index') }}">
            <i class="fa fa-gears" title="Settings"></i>
            <span class="title">settings</span>
          </a>
          <ul>
            <li>
              <a href="{{ cms_route('settings.index') }}">
                <i class="fa fa-gear" title="CMS Settings"></i>
                <span class="title">CMS Settings</span>
              </a>
            </li>
            <li>
              <a href="{{ cms_route('siteSettings.index') }}">
                <i class="fa fa-gear" title="Site Settings"></i>
                <span class="title">Site Settings</span>
              </a>
            </li>
            <li>
              <a href="{{ cms_route('localization.index') }}">
                <i class="fa fa-language" title="Localization"></i>
                <span class="title">Localization</span>
              </a>
            </li>
          </ul>
        </li>
        <li>
          <a href="{{cms_route('bugReport.index')}}">
            <i class="fa fa-bug" title="Bug report"></i>
            <span class="title">Bug Report</span>
          </a>
        </li>
      </ul>
      <!-- notifications and other links -->
      <ul class="nav nav-userinfo navbar-right">
        @if ($languages)
        <!-- Added in v1.2 -->
        <li class="dropdown hover-line language-switcher">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="{{ asset('assets/images/flags/flag-'.language().'.png') }}" alt="flag-{{language()}}" />
          </a>
          <ul class="dropdown-menu languages">
            @foreach ($languages as $key => $value)
            <li>
              <a href="{{$value['url']}}">
                <img src="{{ asset('assets/images/flags/flag-'.$key.'.png') }}" alt="flag-{{$key}}" />
                {{ $value['name'] }}
              </a>
            </li>
            @endforeach
          </ul>
        </li>
        @endif
        <li class="dropdown xs-left">
          <a href="#" data-toggle="dropdown" class="notification-icon notification-icon-messages">
            <i class="fa fa-calendar"></i>
          @if (($calendarEventsCount = count($calendarEvents)) > 0)
            <span class="badge badge-orange">{{$calendarEventsCount}}</span>
          @endif
          </a>
        @if ($calendarEventsCount)
          <ul class="dropdown-menu notifications">
            <li class="top">
              <p class="small">
                You have <strong>{{$calendarEventsCount}}</strong> upcoming event{{$calendarEventsCount > 1 ? 's' : ''}}.
              </p>
            </li>
            <li>
              <ul class="dropdown-menu-list list-unstyled ps-scrollbar">
              @foreach ($calendarEvents as $item)
                <li {!!($date = date('d F Y', strtotime($item->start))) == date('d F Y') ? ' class="active"' : ''!!}>
                  <a href="{{cms_route('calendar.index', ['gotoDate' => $item->start])}}">
                    <i class="fa fa-calendar-o icon-color-{{$item->color}}"></i>
                    <span class="line">
                      <strong>{{$item->title}}</strong>
                    </span>
                    <span class="line small time">
                      Date: {{$date}}
                    </span>
                  @if ($item->time_start)
                    <span class="line small time">
                      Time: {{date('H:i', strtotime($date))}}
                    </span>
                  @endif
                  </a>
                </li>
              @endforeach
              </ul>
            </li>
            <li class="external">
              <a href="{{cms_route('calendar.index')}}">
                <span>View calendar</span>
                <i class="fa fa-link-ext"></i>
              </a>
            </li>
          </ul>
        @endif
        </li>
        <li>
          <form method="post" action="{{cms_route('lockscreen')}}" id="set-lockscreen">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="put">
            <button type="submit" class="btn btn-link" title="Lockscreen">
              <i class="fa fa-lock"></i>
            </button>
          </form>
        </li>
        <li>
          <a href="{{site_url()}}" target="_blank"><i class="el el-website"></i></a>
        </li>
        <li class="dropdown user-profile">
          <a href="#" data-toggle="dropdown">
            <img src="{{ AuthCms::get()->photo }}" alt="user-image" class="img-circle img-inline userpic-32" width="28" />
            <span>
              {{AuthCms::get()->firstname}} {{AuthCms::get()->lastname}}
              <i class="fa fa-angle-down"></i>
            </span>
          </a>
          <ul class="dropdown-menu user-profile-menu list-unstyled">
            <li>
              <a href="{{cms_route('cmsUsers.show', [AuthCms::id()])}}">
                <i class="fa fa-user-secret"></i>
                Profile
              </a>
            </li>
            <li>
              <a href="{{cms_route('cmsUsers.edit', [AuthCms::id()])}}">
                <i class="fa fa-edit"></i>
                Edit
              </a>
            </li>
            <li>
              <a href="#help">
                <i class="fa fa-info"></i>
                Help
              </a>
            </li>
            <li class="last">
              <a href="{{cms_route('logout')}}">
                <i class="fa fa-sign-out"></i>
                Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
