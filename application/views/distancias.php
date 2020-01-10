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
        <input type="text" name="mexico" id="mexico" value="México">
        <strong>Results</strong>

        <?php
        foreach ($org as $row_organizaciones) {
          if($row_organizaciones->latitud != null){
          ?>
          <input type="text" class="numero" id="latitud" name="latitud[]" value="<?= $row_organizaciones->latitud; ?>">
          <input type="text" class="longitud" id="longitud" name="longitud[]" value="<?= $row_organizaciones->longitud; ?>">
          <?php
          }
        }
        ?>
        
  <input type="text" id="input-json" value="<?= $json_localizacion; ?>">
        <?= $json_localizacion; ?>
      </div>
      <div id="output"></div>
    </div>
    <div id="map"></div>
    <?php
   
      foreach ($org as $orga) {;

        echo $orga->abreviacion,'<br>' ;
      }

    ?>
<!-- Replace the value of the key parameter with your own API key. -->
<script type="text/javascript">
  var informacion = document.getElementsByClassName("numero");
  var informacionLongitud = document.getElementsByClassName("longitud");
  console.log(informacion);
   arrayGuardar = [];

        for (var i = 0; i < informacion.length; i++) {    
            arrayGuardar[i] = {'lat': informacion[i], 'lng': informacionLongitud[i]};
            console.log (informacion[i].value);     
            }    

            console.log('el array'+ arrayGuardar[3]);   
        


  var base_url = "<?php echo base_url(); ?>";

  function initMap() {
  var bounds = new google.maps.LatLngBounds;
  var markersArray = [];
  var namePais  = $("#mexico").val();

    

          var origin1 = {lat:16.814782261639657 ,lng:-96.7842015}; 

          var cepco = {lat: 17.0778641, lng: -96.7108364};
          var capim = {lat: 19.451732, lng: -99.095514};
          var uciri = {lat: 16.634355, lng: -96.0500836};
          var comon = {lat: 16.1164988, lng: -92.7024021};
          var uesf = {lat:16.8633 , lng: -93.2121277};
          var cirsa = {lat: 17.1408121, lng: -92.7159518};

          //var origen = new google.maps.LatLng(item.latitud, item.longitud);
          
          var destinationIcon = 'https://chart.googleapis.com/chart?' +
              'chst=d_map_pin_letter&chld=D|FF0000|000000';
          var originIcon = 'https://chart.googleapis.com/chart?' +
              'chst=d_map_pin_letter&chld=O|FFFF00|000000';
          var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 24.6582542, lng: -13.149797},
            zoom: 3
          });
          var geocoder = new google.maps.Geocoder;

          var service = new google.maps.DistanceMatrixService;
          service.getDistanceMatrix({
            origins: [origin1],
            destinations: [<?= $json_localizacion; ?>],
            travelMode: 'DRIVING',
            unitSystem: google.maps.UnitSystem.METRIC,
            avoidHighways: false,
            avoidTolls: false
          }, 

          function(response, status) {
            if (status !== 'OK') {
              alert('Error: ' + status);
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
                    alert('Error: ' + status);
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

                  outputDiv.innerHTML += originList[i] + ' -A- ' + destinationList[j] +
                      ' [Distancia] ' + results[j].distance.text + ' [Tiempo] ' + results[j].duration.text +  '<br><br>';
                }
              }
            }
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
