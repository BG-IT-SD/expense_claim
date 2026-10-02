let map, directionsService, directionsRenderer;
let autocompleteOrigin, autocompleteDestination;

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 13,
        center: { lat: 13.7563, lng: 100.5018 }, // Bangkok
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({ map: map });

    // ✅ เพิ่ม Autocomplete ต้นทางและปลายทาง
    const originInput = document.getElementById("origin");
    const destinationInput = document.getElementById("destination");

    autocompleteOrigin = new google.maps.places.Autocomplete(originInput);
    autocompleteDestination = new google.maps.places.Autocomplete(destinationInput);

    // Optional: จำกัดให้ autocomplete เฉพาะในประเทศไทย
    autocompleteOrigin.setComponentRestrictions({ country: ["th"] });
    autocompleteDestination.setComponentRestrictions({ country: ["th"] });
}


function calculateDistance() {
    const origin = document.getElementById("origin").value;
    const destination = document.getElementById("destination").value;

    directionsService.route({
        origin: origin,
        destination: destination,
        travelMode: google.maps.TravelMode.DRIVING,
    }, (result, status) => {
        if (status === "OK") {
            directionsRenderer.setDirections(result);
            const distanceText = result.routes[0].legs[0].distance.text;   // เช่น "5.2 km"
            const distanceValue = result.routes[0].legs[0].distance.value; // เช่น 5200 (เมตร)

            // แสดงผล
            document.getElementById("distance").innerText = distanceText;

            //ใส่ค่าใน input ทั้งสองช่อง
            document.getElementById("totaldistance_text").value = distanceText;
            document.getElementById("totaldistance").value = (distanceValue / 1000).toFixed(2); // เป็นกิโลเมตร
        } else {
            alert("ไม่สามารถคำนวณเส้นทางได้: " + status);
        }
    });
}

// function calculateDistance() {
//     const origin = document.getElementById("origin").value;
//     const destination = document.getElementById("destination").value;

//     directionsService.route({
//         origin: origin,
//         destination: destination,
//         travelMode: google.maps.TravelMode.DRIVING,
//     }, (result, status) => {
//         if (status === "OK") {
//             directionsRenderer.setDirections(result);
//             const distance = result.routes[0].legs[0].distance.text;
//             document.getElementById("distance").innerText = distance;
//         } else {
//             alert("ไม่สามารถคำนวณเส้นทางได้: " + status);
//         }
//     });
// }
