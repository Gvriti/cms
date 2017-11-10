<!-- Add "fixed" class to make the sidebar fixed always to the browser viewport. -->
<!-- Adding class "toggle-others" will keep only one menu item open at a time. -->
<!-- Adding class "collapsed" collapse sidebar root elements and show only icons. -->
<div class="sidebar-menu toggle-others {{$cmsSettings->get('sidebar_position')}}">
  <div class="sidebar-menu-inner">
    <header class="logo-env">
      <!-- logo -->
      <div class="logo">
        <a href="{{ cms_url('/') }}">
          <div class="logo-expanded">
            <div class="name">CMS</div>
          </div>
          <div class="logo-collapsed">
            <div class="name">CMS</div>
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
      @include('admin._partials.menu')
    </ul>
  </div>
</div>
