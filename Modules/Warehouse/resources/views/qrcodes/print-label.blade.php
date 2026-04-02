<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR-код {{ $qrCode->variant?->product?->article ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: auto;
            margin: 5mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .label {
            text-align: center;
            padding: 10px;
        }
        
        .qr-code {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }
        
        @media print {
            body {
                min-height: auto;
            }
            .label {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="label">
        @php
            $qrPath = $qrCode->qr_code_path ?? null;
            $fileExists = $qrPath && file_exists(public_path($qrPath));
        @endphp
        
        @if($fileExists)
            <img src="{{ asset($qrPath) }}" alt="QR Code" class="qr-code">
        @endif
    </div>
    
    <script>
        // Автоматическая печать при загрузке
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
