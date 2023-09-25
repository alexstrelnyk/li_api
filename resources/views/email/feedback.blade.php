<html lang="{{ app()->getLocale() }}">
    <head>
        <title>New user feedback</title>
    </head>
    <body>
        <div>
            User Name: <b>{{ $userName }}</b><br>
            User Email: <b>{{ $userEmail }}</b><br>
            Client ID: <b>{{ $clientId }}</b><br>
            Text: <b>{{ $text }}</b><br>
        </div>
    </body>
</html>
