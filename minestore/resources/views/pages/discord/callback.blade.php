<!DOCTYPE html>
<html>
<head>
    <title>Discord Authentication</title>
</head>
<body>
<script>
    const appUrl = "{{ config('app.url') }}";
    const data = {!! json_encode($data ?? []) !!};

    console.log('[Discord Callback] Data to send:', data);

    if (window.opener) {
        console.log('[Discord Callback] Sending message to parent');
        window.opener.postMessage(data, appUrl);

        setTimeout(() => {
            console.log('[Discord Callback] Closing window');
            window.close();
        }, 1000);
    } else {
        console.log('[Discord Callback] No parent window found');
    }
</script>
</body>
</html>
