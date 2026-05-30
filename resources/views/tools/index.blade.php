<x-layouts.app>
<div class="container-lg py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h5 fw-semibold mb-1" style="font-family:var(--font-display)">Developer Tools</h1>
            <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);margin:0">
                Run a tool standalone, or attach the output to a task.
            </p>
        </div>
    </div>

    @production
    {{-- ── Coming soon banner (production) ── --}}
    <div class="card border-0 text-center py-5 mb-4" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important">
        <div style="font-size:36px;margin-bottom:16px">🔧</div>
        <h2 class="h5 fw-semibold mb-2" style="font-family:var(--font-display)">Tools coming soon</h2>
        <p style="font-family:var(--font-mono);font-size:11px;color:var(--text-3);max-width:360px;margin:0 auto">
            CSV → SQL, QR codes, PDF generation and more are in development.<br>They'll appear here when ready.
        </p>
    </div>

    <div class="row g-3">
        @foreach([
            ['Text → QR Code',    'bi-qr-code',           'Generate a QR code from any text or URL.'],
            ['CSV → SQL',         'bi-table',              'Convert a CSV file to INSERT or CREATE TABLE SQL.'],
            ['PDF Generate',      'bi-file-pdf',           'Convert HTML or Markdown to a downloadable PDF.'],
            ['PDF Read',          'bi-file-earmark-text',  'Extract text from a PDF file.'],
            ['Word → Clean HTML', 'bi-filetype-doc',       'Convert a .docx to clean, semantic HTML.'],
            ['CSV → QR Batch',    'bi-grid-3x3',           'Generate a QR per CSV row. Outputs a ZIP.'],
        ] as [$name, $icon, $desc])
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 h-100" style="background:var(--bg-raised);border:1px solid var(--border-faint)!important;opacity:.55">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div style="background:var(--bg-elevated);border-radius:8px;padding:8px">
                            <i class="bi {{ $icon }}" style="font-size:18px;color:var(--text-3)"></i>
                        </div>
                        <span style="font-family:var(--font-mono);font-size:9px;color:var(--text-3);background:var(--bg-elevated);border:1px solid var(--border-faint);border-radius:4px;padding:2px 7px">
                            Soon
                        </span>
                    </div>
                    <h2 class="h6 fw-semibold mb-1" style="color:var(--text-2)">{{ $name }}</h2>
                    <p style="font-size:12px;color:var(--text-3);margin:0">{{ $desc }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    {{-- ── Live tools (non-production) ── --}}
    <div class="row g-3">
        @foreach([
            ['text-to-qr',      'Text → QR Code',    'bi-qr-code',           'Generate a QR code from any text or URL.'],
            ['csv-to-sql',      'CSV → SQL',          'bi-table',             'Convert a CSV file to INSERT or CREATE TABLE SQL.'],
            ['pdf-generate',    'PDF Generate',       'bi-file-pdf',          'Convert HTML or Markdown to a downloadable PDF.'],
            ['pdf-read',        'PDF Read',           'bi-file-earmark-text', 'Extract text from a PDF file.'],
            ['word-to-html',    'Word → Clean HTML',  'bi-filetype-doc',      'Convert a .docx to clean, semantic HTML.'],
            ['csv-to-qr-batch', 'CSV → QR Batch',     'bi-grid-3x3',         'Generate a QR per CSV row. Outputs a ZIP.'],
        ] as [$key, $name, $icon, $desc])
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('tools.run', $key) }}" class="card border-0 h-100 text-decoration-none"
               style="background:var(--bg-raised);border:1px solid var(--border-faint)!important;transition:border-color .2s,box-shadow .2s"
               onmouseover="this.style.borderColor='var(--border-base)';this.style.boxShadow='0 4px 20px rgba(0,0,0,.3)'"
               onmouseout="this.style.borderColor='var(--border-faint)';this.style.boxShadow='none'">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div style="background:var(--accent-bg);border-radius:8px;padding:8px;display:inline-block">
                            <i class="bi {{ $icon }}" style="font-size:18px;color:var(--accent)"></i>
                        </div>
                    </div>
                    <h2 class="h6 fw-semibold mb-1" style="color:var(--text-1)">{{ $name }}</h2>
                    <p style="font-size:12px;color:var(--text-3);margin:0">{{ $desc }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @endproduction

</div>
</x-layouts.app>
