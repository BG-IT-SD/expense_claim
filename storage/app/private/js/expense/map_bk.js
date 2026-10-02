let map, directionsService, directionsRenderer;
let autocompleteOrigin, autocompleteDestination;
let originPlace = null;
let destinationPlace = null;

window.initMap = function() { // ต้องเป็น global เพื่อ Google Maps callback จะเห็น
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 13,
        center: {
            lat: 13.7563,
            lng: 100.5018
        }
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        map: map
    });

    const originInput = document.getElementById("origin");
    const destinationInput = document.getElementById("destination");

    autocompleteOrigin = new google.maps.places.Autocomplete(originInput);
    autocompleteDestination = new google.maps.places.Autocomplete(destinationInput);

    autocompleteOrigin.setComponentRestrictions({
        country: ["th"]
    });
    autocompleteDestination.setComponentRestrictions({
        country: ["th"]
    });

    // ✅ เก็บชื่อสถานที่ (name) ลง input
    autocompleteOrigin.addListener('place_changed', () => {
        originPlace = autocompleteOrigin.getPlace();
        document.getElementById("map_a_name").value = originPlace.name || originPlace.formatted_address;
    });

    autocompleteDestination.addListener('place_changed', () => {
        destinationPlace = autocompleteDestination.getPlace();
        document.getElementById("map_b_name").value = destinationPlace.name || destinationPlace.formatted_address;
    });
};

function calculateDistance() {
    const originInput = document.querySelector('#origin input');
    const destinationInput = document.querySelector('#destination input');

    const originAddress = originPlace?.formatted_address || originInput?.value;
    const destinationAddress = destinationPlace?.formatted_address || destinationInput?.value;

    if (!originAddress || !destinationAddress) {
        alert("กรุณากรอกที่ตั้งต้นทางและปลายทางให้ครบ");
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

            document.getElementById("distance").innerText = distanceText;
            const km = ((distanceValue / 1000) * 2).toFixed(2);
            document.getElementById("totaldistance_text").value = km;
            document.getElementById("totaldistance").value = km;
        } else {
            alert("ไม่สามารถคำนวณเส้นทางได้ กรุณาตรวจสอบชื่อสถานที่ให้ชัดเจน");
        }
    });
}

// New

// let map, directionsService, directionsRenderer;

// window.initMap = function() {
//     map = new google.maps.Map(document.getElementById("map"), {
//         zoom: 13,
//         center: {
//             lat: 13.7563,
//             lng: 100.5018
//         }
//     });

//     directionsService = new google.maps.DirectionsService();
//     directionsRenderer = new google.maps.DirectionsRenderer({
//         map: map
//     });
// };

// document.getElementById('origin-autocomplete')
//   .addEventListener('gmpx-placechange', (event) => {
//     const place = event.detail;
//     document.getElementById("map_a_name").value = place.displayName || place.formattedAddress;
//     document.getElementById("latitude").value = place.location.lat;
//     document.getElementById("longitude").value = place.location.lng;
//     window.originPlace = place;
//   });

// document.getElementById('destination-autocomplete')
//   .addEventListener('gmpx-placechange', (event) => {
//     const place = event.detail;
//     document.getElementById("map_b_name").value = place.displayName || place.formattedAddress;
//     document.getElementById("latitude_b").value = place.location.lat;
//     document.getElementById("longitude_b").value = place.location.lng;
//     window.destinationPlace = place;
//   });

// function calculateDistance() {
//     const origin = window.originPlace;
//     const destination = window.destinationPlace;

//     if (!origin || !destination) {
//         alert("กรุณากรอกที่ตั้งต้นทางและปลายทางให้ครบ");
//         return;
//     }

//     directionsService.route({
//         origin: { location: { lat: origin.location.lat, lng: origin.location.lng }},
//         destination: { location: { lat: destination.location.lat, lng: destination.location.lng }},
//         travelMode: google.maps.TravelMode.DRIVING,
//     }, (result, status) => {
//         if (status === "OK") {
//             directionsRenderer.setDirections(result);
//             const distanceValue = result.routes[0].legs[0].distance.value;
//             const distanceText = result.routes[0].legs[0].distance.text;

//             document.getElementById("distance").innerText = distanceText;
//             const km = ((distanceValue / 1000) * 2).toFixed(2);
//             document.getElementById("totaldistance_text").value = km;
//             document.getElementById("totaldistance").value = km;
//         } else {
//             alert("ไม่สามารถคำนวณเส้นทางได้ กรุณาตรวจสอบชื่อสถานที่ให้ชัดเจน");
//         }
//     });
// }