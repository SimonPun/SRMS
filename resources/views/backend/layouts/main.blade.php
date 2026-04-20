

@include('backend.layouts.header')

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        @include('backend.layouts.sidebar')
        <div class="layout-page">
            @include('backend.layouts.navbar')
            <div class="content-wrapper">
                @yield('content')
                @include('backend.layouts.footer')
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
@include('backend.layouts.script')
