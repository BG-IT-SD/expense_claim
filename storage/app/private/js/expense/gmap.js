let map, directionsService, directionsRenderer;
let autocompleteOrigin, autocompleteDestination;
let originPlace = null;
let destinationPlace = null;
let distanceCalculated = false; // ✅ ใช้ตรวจว่ากดคำนวณแล้วหรือยัง

window.initMap = function () {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 13,
    center: { lat: 13.7563, lng: 100.5018 }, // Bangkok
  });

  directionsService = new google.maps.DirectionsService();
  directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

  const originInput = document.getElementById("origin");
  const destinationInput = document.getElementById("destination");

  autocompleteOrigin = new google.maps.places.Autocomplete(originInput);
  autocompleteDestination = new google.maps.places.Autocomplete(destinationInput);

  autocompleteOrigin.setComponentRestrictions({ country: ["th"] });
  autocompleteDestination.setComponentRestrictions({ country: ["th"] });

  // ✅ เก็บชื่อสถานที่ลง hidden input
  autocompleteOrigin.addListener('place_changed', () => {
    originPlace = autocompleteOrigin.getPlace();
    document.getElementById("map_a_name").value = originPlace.name || originPlace.formatted_address;
    distanceCalculated = false; // รีเซ็ตเมื่อเปลี่ยน
  });

  autocompleteDestination.addListener('place_changed', () => {
    destinationPlace = autocompleteDestination.getPlace();
    document.getElementById("map_b_name").value = destinationPlace.name || destinationPlace.formatted_address;
    distanceCalculated = false; // รีเซ็ตเมื่อเปลี่ยน
  });
};

// ✅ ฟังก์ชันคำนวณระยะทาง
function calculateDistance() {
  const originAddress = originPlace?.formatted_address || document.getElementById("origin").value;
  const destinationAddress = destinationPlace?.formatted_address || document.getElementById("destination").value;

  if (!originAddress || !destinationAddress) {
    alert("กรุณากรอกที่ตั้งต้นทางและปลายทางให้ครบ");
    distanceCalculated = false;
    return;
  }

  if (originPlace?.geometry?.location) {
    document.getElementById("latitude").value = originPlace.geometry.location.lat();
    document.getElementById("longitude").value = originPlace.geometry.location.lng();
  }

  if (destinationPlace?.geometry?.location) {
    document.getElementById("latitude_b").value = destinationPlace.geometry.location.lat();
    document.getElementById("longitude_b").value = destinationPlace.geometry.location.lng();
  }

  directionsService.route({
    origin: originAddress,
    destination: destinationAddress,
    travelMode: google.maps.TravelMode.DRIVING,
  }, (result, status) => {
    if (status === "OK") {
      directionsRenderer.setDirections(result);

      const distanceValue = result.routes[0].legs[0].distance.value;
      const distanceText = result.routes[0].legs[0].distance.text;
      const km = ((distanceValue / 1000) * 2).toFixed(2);

      document.getElementById("distance").innerText = distanceText;
      document.getElementById("totaldistance_text").value = km;
      document.getElementById("totaldistance").value = km;

      distanceCalculated = true; // ✅ บันทึกว่าคำนวณแล้ว
    } else {
      alert("ไม่สามารถคำนวณเส้นทางได้ กรุณาตรวจสอบชื่อสถานที่ให้ชัดเจน");
      distanceCalculated = false;
    }
  });
}
