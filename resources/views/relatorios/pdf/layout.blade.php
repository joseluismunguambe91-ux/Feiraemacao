<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color: #241b10; }
    h1 { font-size: 16px; margin-bottom: 2px; }
    p.subtitulo { color: #6c6558; margin-top: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
    th { background: #f2f4ee; }
</style>
</head>
<body>
    <h1>@yield('titulo')</h1>
    <p class="subtitulo">{{ $feira->tema }} — gerado em {{ now()->format('d/m/Y H:i') }}</p>
    @yield('conteudo')
</body>
</html>
