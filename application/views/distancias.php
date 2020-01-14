<style type="text/css">
#right-panel {
  font-family: 'Roboto','sans-serif';
  line-height: 30px;
  padding-left: 10px;
}

#right-panel select, #right-panel input {
  font-size: 15px;
}

#right-panel select {
  width: 100%;
}

#right-panel i {
  font-size: 12px;
}
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
#map {
  height: 100%;
  width: 50%;
}
#right-panel {
  float: right;
  width: 48%;
  padding-left: 2%;
}
#output {
  font-size: 11px;
}   
</style> 
    <div id="right-panel">
      <div id="inputs">
        <pre>

        </pre>
      </div>
      <div>
        <input type="text" name="mexico" id="mexico" value="Colombia">
        <strong>Results</strong><br>
            <?php
        foreach ($org as $row_organizaciones) {
          if($row_organizaciones->latitud != null){
          ?>
          <label name="abreviacion" value=""><?= $row_organizaciones->abreviacion; ?></label>
          <input type="text" class="latitud" id="latitud" name="latitud[]" value="<?= $row_organizaciones->latitud; ?>">
          <input type="text" class="longitud" id="longitud" name="longitud[]" value="<?= $row_organizaciones->longitud; ?>"><br>
          <?php
          }
        }
        ?>

        <input type="text" name="origen_latitud" id="origen_latitud" value="3.8151748866268482">
        <input type="text" name="origen_longitud" id="origen_longitud" value=" -75.13146472723231">

        _
      </div>
      <div id="output"></div>
    </div>
    <div id="map"></div>

<!-- Replace the value of the key parameter with your own API key. -->
<script type="text/javascript">

        
function initMap() {
 /*var informacionLatitud = document.getElementsByClassName("latitud");
  var informacionLongitud = document.getElementsByClassName("longitud");
  var destinoArray = [];
  for (var i = 0; i < informacionLatitud.length; i++) {    
    destinoArray[i] = new google.maps.LatLng (informacionLatitud[i],informacionLatitud[i]);
    console.log("resultado" + destinoArray[i]);
  }    

            //console.log('el destino'+ destino); */ 

  var base_url = "<?php echo base_url()?>";
  var bounds = new google.maps.LatLngBounds;
  var markersArray = [];
  var origen_longitud = $("#origen_longitud").val();
  var origen_latitud = $("#origen_latitud").val();
  var origin1 = new google.maps.LatLng(origen_latitud , origen_longitud);
  //var origin1 = {lat: 16.853138360355686, lng: -96.78096747380502};

console.log("orige"+origin1)
  var namePais = $("#mexico").val();

   $.post(base_url+"Inicio/get_name_pais",
      {
        namePais:namePais
      },

      function(data)
      {
        var p = JSON.parse(data);
        var destino2 = [];
        $.each(p, function(i, item){
          var org = item.abreviacion;
          console.log(org);
          if (item.latitud != null && item.longitud != null ) {
            var destino = new google.maps.LatLng(item.latitud,item.longitud);
            //let destino = {lat: item.latitud, lng: item.longitud};
            destino2.push(destino);
            console.log(destino)
            //alert(destino);
          }
          console.log("destino"+ destino2)
       



  var destinationIcon = 'https://chart.googleapis.com/chart?' +
      'chst=d_map_pin_letter&chld=D|FF0000|000000';
  var originIcon = 'https://chart.googleapis.com/chart?' +
      'chst=d_map_pin_letter&chld=O|FFFF00|000000';
  var map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: 24.6582542, lng: -13.149797},
    zoom: 4
  });
  var geocoder = new google.maps.Geocoder;

  var service = new google.maps.DistanceMatrixService;
  service.getDistanceMatrix({
    origins: [origin1],
    destinations: destino2,
    travelMode: 'DRIVING',
    unitSystem: google.maps.UnitSystem.METRIC,
    avoidHighways: false,
    avoidTolls: false
  }, 

  function(response, status) {
    if (status !== 'OK') {
      //alert('Error : ' + status);
    } else {
      var originList = response.originAddresses;
      var destinationList = response.destinationAddresses;
      var outputDiv = document.getElementById('output');
      outputDiv.innerHTML = '';
      deleteMarkers(markersArray);

      var showGeocodedAddressOnMap = function(asDestination) {
        var icon = asDestination ? destinationIcon : originIcon;
        return function(results, status) {
          if (status === 'OK') {
            map.fitBounds(bounds.extend(results[0].geometry.location));
            markersArray.push(new google.maps.Marker({
              map: map,
              position: results[0].geometry.location,
              icon: icon
            }));
          } else {
            //alert('Geocode was not successful due to: ' + status);
          }
        };
      };

      for (var i = 0; i < originList.length; i++) {
        var results = response.rows[i].elements;
        geocoder.geocode({'address': originList[i]},
            showGeocodedAddressOnMap(false));

        for (var j = 0; j < results.length; j++) {
          geocoder.geocode({'address': destinationList[j]},
              showGeocodedAddressOnMap(true));
          outputDiv.innerHTML += originList[i] + 'A' + destinationList[j] + item.latitud +
              ': ' + results[j].distance.text + 'EN' +
              results[j].duration.text + '<br>';
        }
      }
    }
  });
 });
      });
}

function deleteMarkers(markersArray) {
  for (var i = 0; i < markersArray.length; i++) {
    markersArray[i].setMap(null);
  }
  markersArray = [];
}

  
  
</script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqBKjIObP2dJsSZCMNOSgj_Jy2BGG18DA&libraries=places&callback=initMap">
    </script>
