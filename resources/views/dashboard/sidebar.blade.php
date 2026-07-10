<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('navia.dashboard') }}">
                    <div class="logo-icon-container">
                        <?php $admin_logo_img = Navia::setting('admin.icon_image', ''); ?>
                        @if($admin_logo_img == '')
                            <img src="{{ navia_asset('images/logo-icon-light.png') }}" alt="Logo Icon">
                        @else
                            <img src="{{ Navia::image($admin_logo_img) }}" alt="Logo Icon">
                        @endif
                    </div>
                    <div class="title">{{Navia::setting('admin.title', 'NAVIA')}}</div>
                </a>
            </div><!-- .navbar-header -->

            <div class="panel widget center bgimage"
                 style="background-image:url({{ Navia::image( Navia::setting('admin.bg_image'), navia_asset('images/bg.jpg') ) }}); background-size: cover; background-position: 0px;">
                <div class="dimmer"></div>
                <div class="panel-content">
                    <img src="{{ $user_avatar }}" class="avatar" alt="{{ Auth::user()->name }} avatar">
                    <h4>{{ ucwords(Auth::user()->name) }}</h4>
                    <p>{{ Auth::user()->email }}</p>

                    <a href="{{ route('navia.profile') }}" class="btn btn-primary">{{ __('navia::generic.profile') }}</a>
                    <div style="clear:both"></div>
                </div>
            </div>

        </div>
        <div id="adminmenu">
            <admin-menu :items="{{ menu('admin', '_json') }}"></admin-menu>
        </div>
    </nav>
</div>
