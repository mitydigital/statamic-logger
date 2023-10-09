<div><strong>{{ $error }}:</strong></div>
<pre class="text-xs mt-1">{{ json_encode(json_decode($json, true), JSON_PRETTY_PRINT) }}</pre>
@if ($message)
    <pre class="mt-1">{{ $message }}</pre>
@endif