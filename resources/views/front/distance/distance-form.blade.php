<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำนวณระยะทางจาก Google Maps URL</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        input, button { padding: 10px; margin: 5px; width: 400px; }
    </style>
</head>
<body>

    <h2>คำนวณระยะทางจากลิงก์ Google Maps</h2>

    @if ($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
    @endif

    <form action="/distance" method="POST">
        @csrf
        <input type="text" name="google_maps_url" placeholder="วางลิงก์ Google Maps ที่นี่" value="{{ old('google_maps_url', $google_maps_url ?? '') }}" required>
        <button type="submit">คำนวณระยะทาง</button>
    </form>

    @if(isset($distance))
        <h3>ระยะทาง: {{ $distance }}</h3>
        <h3>เวลาเดินทางโดยรถยนต์: {{ $duration }}</h3>
    @endif

</body>
</html>
