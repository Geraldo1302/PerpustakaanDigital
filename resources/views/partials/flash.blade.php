@if (session('success') || session('error') || session('status'))
    <div class="rb-flash">
        @if (session('success'))
            <div class="rb-alert rb-alert-success" role="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rb-alert rb-alert-error" role="alert">{{ session('error') }}</div>
        @endif

        @if (session('status'))
            <div class="rb-alert rb-alert-info" role="alert">{{ session('status') }}</div>
        @endif
    </div>
@endif
