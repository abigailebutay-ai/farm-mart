<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('print-title', 'Farm-Mart Report')</title>
    @include('prints.partials.styles')
</head>
<body>
    <div class="report-actions no-print">
        <a href="{{ url()->previous() }}">Back</a>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <main class="print-container">
        <header class="print-header">
            <div>
                <div class="print-brand">Farm-Mart</div>
                <div class="print-subtitle">Farm-to-Market Platform</div>
            </div>
            <div class="print-header-meta">
                <div class="print-title">@yield('print-heading', 'Report')</div>
                <div class="print-meta">Report Date: @yield('print-report-date', now()->timezone(config('app.timezone'))->format('M d, Y h:i A'))</div>
                @hasSection('print-meta')
                    <div class="print-meta">@yield('print-meta')</div>
                @endif
            </div>
        </header>

        @yield('content')

        <footer class="print-footer">
            <p>Prepared by Farm-Mart System</p>
            <p>Thank you for supporting local farmers.</p>
        </footer>
    </main>
</body>
</html>
