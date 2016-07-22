<!-- Add "fixed" class to make the sidebar fixed always to the browser viewport. -->
<!-- Adding class "toggle-others" will keep only one menu item open at a time. -->
<!-- Adding class "collapsed" collapse sidebar root elements and show only icons. -->
<div class="sidebar-menu toggle-others {{$settings->get('sidebar_position')}}">
  <div class="sidebar-menu-inner">
    <header class="logo-env">
      <!-- logo -->
      <div class="logo">
        <a href="{{ cms_url() }}">
          <div class="logo-expanded">
            <img src="{{ asset('assets/images/logo@2x.png') }}" height="24" alt="Digital Design" />
          </div>
          <div class="logo-collapsed">
            <img src="{{ asset('assets/images/logo-collapsed@2x.png') }}" height="24" alt="Digital Design" />
          </div>
        </a>
      </div>
      <!-- This will toggle the mobile menu and will be visible only on mobile devices -->
      <div class="mobile-menu-toggle visible-xs">
        <a href="#" data-toggle="mobile-menu">
          <i class="fa fa-bars"></i>
        </a>
      </div>
      <!-- This will open the popup with user profile settings, you can use for any purpose, just be creative -->
      <div class="settings-icon">
        <a href="#" data-toggle="settings-pane" data-animate="true">
          <i class="fa fa-gear"></i>
        </a>
      </div>
    </header>
    <ul id="main-menu" class="main-menu">
      <!-- add class "multiple-expanded" to allow multiple submenus to open -->
      <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
      <li>
        <a href="{{ cms_url() }}">
          <i class="{{icon_type('dashboard')}}" title="Dashboard"></i>
          <span class="title">Home</span>
        </a>
      </li>
      <li>
        <a href="{{ cms_route('menus.index') }}">
          <i class="{{icon_type('pages')}}" title="Pages"></i>
          <span class="title">Site Map</span>
        </a>
        <ul>
      @if (! empty($menus))
        @foreach ($menus as $item)
          <li>
            <a href="{{ cms_route('pages.index', [$item->id]) }}">
              <span class="title">{{ $item->title }}</span>
            </a>
          </li>
        @endforeach
      @endif
          <li>
            <a href="{{ cms_route('menus.index') }}">
              <i class="{{icon_type('menus')}}" title="Menus"></i>
              <span class="title">Menu Management</span>
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="{{ cms_route('collections.index') }}">
          <i class="{{icon_type('collections')}}" title="Collections"></i>
          <span class="title">Collections</span>
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
          <i class="{{icon_type('cms_users')}}" title="CMS Users"></i>
          <span class="title">CMS Users</span>
        </a>
      </li>
      <li>
        <a href="{{ cms_route('settings.index') }}">
          <i class="fa fa-gears" title="Settings"></i>
          <span class="title">Settings</span>
        </a>
        <ul>
          <li>
            <a href="{{ cms_route('settings.index') }}">
              <i class="fa fa-gear" title="Admin Settings"></i>
              <span class="title">CMS Settings</span>
            </a>
          </li>
          <li>
            <a href="{{ cms_route('siteSettings.index') }}">
              <i class="fa fa-gear" title="Admin Settings"></i>
              <span class="title">Site Settings</span>
            </a>
          </li>
          <li>
            <a href="{{ cms_route('translations.index') }}">
              <i class="fa fa-language" title="Translations"></i>
              <span class="title">Translations</span>
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="{{ cms_route('calendar.index') }}">
          <i class="fa fa-flask" title="Extra"></i>
          <span class="title">Extra</span>
        </a>
        <ul>
          <li>
            <a href="{{ cms_route('slider.index') }}">
              <i class="fa fa-photo" title="Slider"></i>
              <span class="title">Homepage Slider</span>
            </a>
          </li>
          <li>
            <a href="{{ cms_route('notes.index') }}">
              <i class="fa fa-file-text-o" title="notes"></i>
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
            <a href="{{cms_route('bugReport.index')}}">
              <i class="fa fa-bug" title="Bug report"></i>
              <span class="title">Bug Report</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</div>
