<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดึงค่าพิกัดและคำนวณระยะทาง</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
    </style>
</head>
<body>

    <h2>แสดงค่าพิกัดจาก Google Maps URL และคำนวณระยะทาง</h2>

    @if(isset($latitude) && isset($longitude))
        <h3>🌍 ละติจูด (Latitude): {{ $latitude }}</h3>
        <h3>🌍 ลองจิจูด (Longitude): {{ $longitude }}</h3>
        <h3>🛣️ ระยะทาง: {{ $distance }}</h3>
        <h3>⏳ เวลาเดินทางโดยรถยนต์: {{ $duration }}</h3>
        <h3>🔗 <a href="{{ $google_maps_url }}" target="_blank">เปิดใน Google Maps</a></h3>
    @endif

</body>
</html>
