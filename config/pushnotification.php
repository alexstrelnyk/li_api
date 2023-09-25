<?php

return [
    'connection-string' => env('AZURE_NOTIFICATION_CONNECTION_STRING', 'Endpoint=sb://li-notice.servicebus.windows.net/;SharedAccessKeyName=DefaultFullSharedAccessSignature;SharedAccessKey=k1bm6BFR3GeO+tjZruFUaxLuveQWqI5QyGJ8sTcG4xQ='),
    'notification-hub' => env('AZURE_NOTIFICATION_HUB', 'li-dev')
];
