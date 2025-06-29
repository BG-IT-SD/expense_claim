{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Distance Calculator</title>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script>
        let map, originMarker, destinationMarker;
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 13.736717, lng: 100.523186 }, // Default: Bangkok
                zoom: 12
            });

            const originInput = document.getElementById("origin");
            const destinationInput = document.getElementById("destination");
            const autocompleteOrigin = new google.maps.places.Autocomplete(originInput);
            const autocompleteDestination = new google.maps.places.Autocomplete(destinationInput);

            autocompleteOrigin.addListener("place_changed", function() {
                const place = autocompleteOrigin.getPlace();
                if (place.geometry) {
                    if (originMarker) originMarker.setMap(null);
                    originMarker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map
                    });
                    map.setCenter(place.geometry.location);
                }
            });

            autocompleteDestination.addListener("place_changed", function() {
                const place = autocompleteDestination.getPlace();
                if (place.geometry) {
                    if (destinationMarker) destinationMarker.setMap(null);
                    destinationMarker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map
                    });
                }
            });
        }
    </script>
</head>
<body onload="initMap()">
    <h2>เลือกต้นทางและปลายทาง</h2>
    <form action="{{ route('calculate.distance') }}" method="POST">
        @csrf
        <label>ต้นทาง:</label>
        <input type="text" id="origin" name="origin" required>
        <label>ปลายทาง:</label>
        <input type="text" id="destination" name="destination" required>
        <button type="submit">คำนวณระยะทาง</button>
    </form>
    <div id="map" style="width: 100%; height: 500px;"></div>
</body>
</html> --}}

<!doctype html>
<!--
 @license
 Copyright 2019 Google LLC. All Rights Reserved.
 SPDX-License-Identifier: Apache-2.0
-->
<html>
  <head>
    <title>Place Autocomplete and Directions</title>

    <!-- jsFiddle will insert css and js -->
  </head>
  <style>
    /**
 * @license
 * Copyright 2019 Google LLC. All Rights Reserved.
 * SPDX-License-Identifier: Apache-2.0
 */
/*
 * Always set the map height explicitly to define the size of the div element
 * that contains the map.
 */
#map {
  height: 100%;
}

/*
 * Optional: Makes the sample page fill the window.
 */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}

.controls {
  margin-top: 10px;
  border: 1px solid transparent;
  border-radius: 2px 0 0 2px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  height: 32px;
  outline: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

#origin-input,
#destination-input {
  background-color: #fff;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  margin-left: 12px;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 200px;
}

#origin-input:focus,
#destination-input:focus {
  border-color: #4d90fe;
}

#mode-selector {
  color: #fff;
  background-color: #4d90fe;
  margin-left: 12px;
  padding: 5px 11px 0px 11px;
}

#mode-selector label {
  font-family: Roboto;
  font-size: 13px;
  font-weight: 300;
}


  </style>
  <body>
    <div style="display: none">
      <input
        id="origin-input"
        class="controls"
        type="text"
        placeholder="Enter an origin location"
      />

      <input
        id="destination-input"
        class="controls"
        type="text"
        placeholder="Enter a destination location"
      />

      <div id="mode-selector" class="controls">
        <input
          type="radio"
          name="type"
          id="changemode-walking"
          checked="checked"
        />
        <label for="changemode-walking">Walking</label>

        <input type="radio" name="type" id="changemode-transit" />
        <label for="changemode-transit">Transit</label>

        <input type="radio" name="type" id="changemode-driving" />
        <label for="changemode-driving">Driving</label>
      </div>
    </div>

    <div id="map"></div>

    <!--
      The `defer` attribute causes the script to execute after the full HTML
      document has been parsed. For non-blocking uses, avoiding race conditions,
      and consistent behavior across browsers, consider loading using Promises. See
      https://developers.google.com/maps/documentation/javascript/load-maps-js-api
      for more information.
      -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABibhL6u-A5s_G40-9tKSBNqT5P6s_iKU&callback=initMap&libraries=places&v=weekly"
      defer
    ></script>
  </body>
</html>

<script>

// function initMap() {
//   const map = new google.maps.Map(document.getElementById("map"), {
//     mapTypeControl: false,
//     center: { lat: 13.736717, lng: 100.523186 }, // Default: Bangkok
//     zoom: 13,
//   });

//   new AutocompleteDirectionsHandler(map);
// }

// class AutocompleteDirectionsHandler {
//   map;
//   originPlaceId;
//   destinationPlaceId;
//   travelMode;
//   directionsService;
//   directionsRenderer;
//   constructor(map) {
//     this.map = map;
//     this.originPlaceId = "";
//     this.destinationPlaceId = "";
//     this.travelMode = google.maps.TravelMode.WALKING;
//     this.directionsService = new google.maps.DirectionsService();
//     this.directionsRenderer = new google.maps.DirectionsRenderer();
//     this.directionsRenderer.setMap(map);

//     const originInput = document.getElementById("origin-input");
//     const destinationInput = document.getElementById("destination-input");
//     const modeSelector = document.getElementById("mode-selector");
//     // Specify just the place data fields that you need.
//     const originAutocomplete = new google.maps.places.Autocomplete(
//       originInput,
//       { fields: ["place_id"] },
//     );
//     // Specify just the place data fields that you need.
//     const destinationAutocomplete = new google.maps.places.Autocomplete(
//       destinationInput,
//       { fields: ["place_id"] },
//     );

//     this.setupClickListener(
//       "changemode-walking",
//       google.maps.TravelMode.WALKING,
//     );
//     this.setupClickListener(
//       "changemode-transit",
//       google.maps.TravelMode.TRANSIT,
//     );
//     this.setupClickListener(
//       "changemode-driving",
//       google.maps.TravelMode.DRIVING,
//     );
//     this.setupPlaceChangedListener(originAutocomplete, "ORIG");
//     this.setupPlaceChangedListener(destinationAutocomplete, "DEST");
//     this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
//     this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(
//       destinationInput,
//     );
//     this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
//   }
//   // Sets a listener on a radio button to change the filter type on Places
//   // Autocomplete.
//   setupClickListener(id, mode) {
//     const radioButton = document.getElementById(id);

//     radioButton.addEventListener("click", () => {
//       this.travelMode = mode;
//       this.route();
//     });
//   }
//   setupPlaceChangedListener(autocomplete, mode) {
//     autocomplete.bindTo("bounds", this.map);
//     autocomplete.addListener("place_changed", () => {
//       const place = autocomplete.getPlace();

//       if (!place.place_id) {
//         window.alert("Please select an option from the dropdown list.");
//         return;
//       }

//       if (mode === "ORIG") {
//         this.originPlaceId = place.place_id;
//       } else {
//         this.destinationPlaceId = place.place_id;
//       }

//       this.route();
//     });
//   }
//   route() {
//     if (!this.originPlaceId || !this.destinationPlaceId) {
//       return;
//     }

//     const me = this;

//     this.directionsService.route(
//       {
//         origin: { placeId: this.originPlaceId },
//         destination: { placeId: this.destinationPlaceId },
//         travelMode: this.travelMode,
//       },
//       (response, status) => {
//         if (status === "OK") {
//           me.directionsRenderer.setDirections(response);
//         } else {
//           window.alert("Directions request failed due to " + status);
//         }
//       },
//     );
//   }
// }

// window.initMap = initMap;

</script>

