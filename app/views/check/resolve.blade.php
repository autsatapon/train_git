<html>
    <head>
        <title>Health Check</title>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <style>
            body { margin-top: 30px; }
        </style>
    </head>
    <body>
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>URL</th>
                        <th>PING TIME</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                    <tr>
                        <td>{{ $service['name'] }}</td>
                        <td>{{ $service['url'] }}</td>
                        <td>{{ $service['time'] }}</td>
                        <td class="@if ($service['status'] == 'OK') success @else danger @endif">{{ $service['status'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    </body>
</html>