<!DOCTYPE html>
<html>
  <head>
     <style type="text/css">
    #map
    {
      width: 800px;
      height: 700px;
      float:left;
    }
     #informacion
    {
      margin-left: 2em;
      width:  800px;
      height: 800px;
      float:left;

    }

#bottom-panel {
  font-family: 'Roboto','sans-serif';
  line-height: 30px;
  padding-left: 10px;
}

#bottom-panel select, #bottom-panel input {
  font-size: 15px;
}

#bottom-panel select {
  width: 100%;
}

#bottom-panel i {
  font-size: 12px;
}
#bottom-panel {
  height: 100%;
  float: left;
  width: 100%;
  overflow: auto;
}
.controls {
  background-color: #fff;
  border-radius: 2px;
  border: 1px solid transparent;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
  box-sizing: border-box;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  height: 29px;
  margin-left: 17px;
  margin-top: 10px;
  outline: none;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 400px;
}

.controls:focus {
  border-color: #4d90fe;
}

#panel-flotante {
  position: absolute;
  top: 60px;
  left: 1%;
  z-index: 5;
  background-color: #fff;
  padding: 1px;
  border: 1px solid #999;
  text-align: center;
  outline-width: 30px;
  line-height: 30px;
  padding-left:4px;
}



  </style>
    <title>Geolocalización</title>
    <script type="text/javascript"></script>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
  
  </head>
  <body>
    <div id="map"></div>

    <div id="infowindow-content">
      <span id="place-address"></span>
    </div>
    <div class="informacion">
      <div id="panel-flotante">
        <button  name="calcular" type="button" onclick="calcular();">Calcular tiempos </button>
        <button id="ver_todas_organizaciones" name="ver_todas_organizaciones" onclick="load();">Ver todo</button>
      </div>
      
      <fieldset>
        <p>Instrucciones de uso para marcar rutas de acceso<p>
          <ol>
            <li>Buscar el lugar en donde desea poner el punto de partida</li>
            <li>Dar clic dentro del mapa para posicionar el punto de partida</li>
            <li>Dar clic en el botón (Calcular rutas) que se encuentra del lado izquierdo del mapa para que muestre la lista de organizaciones que se encunetran en el país en donde esta posicionado</li>
            <li>Dar clic en el link de ver ruta que se muestra en la lista de las organizaciones</li>
          </ol>
      </fieldset>
      <!--input guarda coordenadas de ubicacion actual-->
      <input type="hidden" name="ubicacionLat" id="ubicacionLat"> <input type="hidden" name="ubicacionLng" id="ubicacionLng">
      <form id="formulario" action="" method="post">
      <input id="pac-input" class="controls" type="text" placeholder="Ingresa un lugar">
      
    
      <input type="hidden" name="cx" id="cx" required=""> 
 
      <input type="hidden" name="cy" id="cy" required="">
 
       <select id = "pais1" onchange="search()">
        <option value="" selected="">Selecciona un pais</option>        
      <?php foreach ($paises as $pais) {?>
        <option value="<?php echo $pais->id;?>"><?php echo $pais->nombre_pais;?></option>
     <?php } ?> 
    </select>
    </form>
    <br/>

    <table border="1" id="organizaciones">   
    </table>
    
    </div>

    <div id="bottom-panel"></div>

<!--inicio de mapa-->
<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";
  var ubicacion = {lat: 24.6582542, lng: -13.149797};
  var punto_partida = [];
  var map;
  var markersArray = [];

  //var formulario = $("#formulario");
  function quitar_marcadores(lista)
  {
    for (i in lista) {
      lista[i].setMap(null);
    }//end for
  }//end funtion quitar marcadores

  function initMap() {
    ver_todas_organizaciones();
    //se inicializa el mapa
    map = new google.maps.Map(document.getElementById('map'), 
    {
      zoom: 2,      
      center: ubicacion,
      mapTypeId:'roadmap'
    });
    
     
    //inicia buscador de lugares
    var input = document.getElementById('pac-input');
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    autocomplete.setFields(['place_id', 'geometry', 'name', 'formatted_address']);

    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    var infowindow = new google.maps.InfoWindow();
    
    var infowindowContent = document.getElementById('infowindow-content');
    infowindow.setContent(infowindowContent);
    var geocoder = new google.maps.Geocoder;

    var marker = new google.maps.Marker({
      map: map, 
    });
      
    marker.addListener('click', function() {
      infowindow.open(map, marker);
    });

    autocomplete.addListener('place_changed', function() {
      infowindow.close();
      var place = autocomplete.getPlace();

      if (!place.place_id) {
        return;
      }
      geocoder.geocode({'placeId': place.place_id}, function(results, status) {
        if (status !== 'OK') {
          window.alert('Geocoder failed due to: ' + status);
          return;
        }

        map.setZoom(11);
        map.setCenter(results[0].geometry.location);

        marker.setPlace(
            {placeId: place.place_id, location: results[0].geometry.location});

        marker.setVisible(false);

        //var name_lugar = infowindowContent.children['place-name'].textContent = place.name;
        //var id_lugar = infowindowContent.children['place-id'].textContent = place.place_id;
        var direccion_lugar = infowindowContent.children['place-address'].textContent = results[0].formatted_address;

        var elarray = direccion_lugar.split(",");
        var pais = elarray[elarray.length - 1];

        var mensaje = confirm("Desas ver las organizaciones que pertenecen a:" + pais);
        if (mensaje) {          
          search_pais(pais);
        } //end if
        else{
          alert("Solo se mostrara la ubica del pais de:" + pais);
        }   
        infowindow.open(map, marker);
      });
    });

  }//end initmap

  //funcion para mostrar todos los puntos
  function ver_todas_organizaciones(){

    $.post(base_url+"Inicio/get_marcadores",
    function(data)
    {
      var p = JSON.parse(data);
      $.each(p, function(i, item){  
        var infowindow = new google.maps.InfoWindow
        ({
          content:item.abreviacion,
          maxWidth: 200
        });
        var posi = new google.maps.LatLng(item.latitud, item.longitud);     
        var marca = new google.maps.Marker
        ({       
          position:posi,
          animation: google.maps.Animation.DROP
        });
        map.setZoom(2);
        map.setCenter(ubicacion);
        google.maps.event.addListener(marca,"click", function()
        {
          infowindow.open(map, marca);
        });   
        marca.setMap(map); 

      });
    });
  }



    //funcion para buscar puntos por pais
    function search(){
      var pais  = document.getElementById('pais1').value;
      //Iniciamos mapa
      map = new google.maps.Map(document.getElementById('map'), 
      {     
        center: ubicacion,
        mapTypeId:'roadmap'
      });

      $("#organizaciones").html('');
      $('#organizaciones').html(
        '<tr>'+
          '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Organizacion</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">pais</th>'+
        '</tr>'
      );

      $.post(base_url+"Inicio/get_marcadores_pais",
      {
        id_pais:pais
      },
      function(data)
      {
        var p = JSON.parse(data);

        $.each(p, function(i, item){
       
          $('#organizaciones').append(
            `<tr>
              <td>${item.id_opp}</td>
              <td>${item.abreviacion}</td>
                
                <td>${item.latitud}</td>
              

            </tr>`
          );

       
          var infowindow = new google.maps.InfoWindow
          ({
            content:item.abreviacion,
            maxWidth: 200
          });
          var posi = new google.maps.LatLng(item.latitud, item.longitud); 
        
          var marca = new google.maps.Marker
          ({       
            position:posi,
            animation: google.maps.Animation.DROP,
            
            //icon:icon
          });  
          map.setZoom(5);
          var centrar = new google.maps.LatLng(item.latitude, item.longitude);
          map.setCenter(centrar);
          google.maps.event.addListener(marca,"click", function()
          {
            infowindow.open(map, marca);
          });
          marca.setMap(map); 

       

        });         
      });   
      }//end function search


    //function para mostrar organizaciones
     function search_pais(pais){

      var namePais = pais.trim();
      var formulario = $("#formulario");      
      //se inicializa el mapa   

      map = new google.maps.Map(document.getElementById('map'), 
      {     
        center: ubicacion,
        mapTypeId:'roadmap'
      });
      //funcion para marcar nueva posicion den marcador 
      map.addListener("click", function(event)
      {
        var coordenadas = event.latLng.toString();
        coordenadas = coordenadas.replace("(","");
        coordenadas = coordenadas.replace(")","");
        var lista = coordenadas.split(",");
        var direcion = new google.maps.LatLng(lista[0], lista[1]);
        var origen = 'https://chart.googleapis.com/chart?' +
        'chst=d_map_pin_letter&chld=O|FFFF00|000000'
        var marcador = new google.maps.Marker
        ({
          position:direcion,
          map:map,
          icon:origen,
          animation:google.maps.Animation.DROP,
          draggable:false
        });

    
        formulario.find("input[name='cx']").val(lista[0]);
        formulario.find("input[name='cy']").val(lista[1]);
        punto_partida.push(marcador);
        quitar_marcadores(punto_partida);
        marcador.setMap(map);      
      });// en function


       $("#organizaciones").html('');
        $('#organizaciones').html(
          '<tr>'+
            '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
            '<th style="width: 10%;background-color: #006699; color: white;">Organizacion</th>'+
            '<th style="width: 10%;background-color: #006699; color: white;">pais</th>'+
          '</tr>'
        );  
        alert(namePais);        
          $.post(base_url+"Inicio/get_name_pais",
          {
            namePais:namePais
          },

          function(data)
          {
            var p = JSON.parse(data);
            $.each(p, function(i, item){
               if (item.latitud != null && item.longitud != null ) {
                  var el_id_opp = item.id_opp;
                  var destino = new google.maps.LatLng(item.latitud,item.longitud);
                  $('#organizaciones').append(
                    `<tr>
                      <td>${item.id_opp}</td>
                      <td>${item.abreviacion}</td>
                      <td>${item.id_pais}</td>                      
                    </tr>`
                  );
                }//end if
           
              var infowindow = new google.maps.InfoWindow
              ({
                content:item.abreviacion,
                maxWidth: 200
              });
              var posi = new google.maps.LatLng(item.latitud, item.longitud);     
              var marca = new google.maps.Marker
              ({       
                position:posi,
                animation: google.maps.Animation.DROP
              });  
              var centrar = new google.maps.LatLng(item.latitude, item.longitude);
              map.setZoom(5);
              map.setCenter(centrar);
              google.maps.event.addListener(marca,"click", function()
              {
                infowindow.open(map, marca);
              });   
              marca.setMap(map); 
       
            });         
          });  //end funtion
        //end buscador de lugares


      calcular = function(){
        var origenLat = $("#cx").val();
        var origenLng = $("#cy").val();
 
        if (origenLng =="") {
          alert("Antes de continuar debes de ubicar el punto de partida dentro del mapa");
        }
        else{         
          var origen = new google.maps.LatLng(origenLat , origenLng);
        
          $("#organizaciones").html('');
          $('#organizaciones').html(
            '<tr>'+
              '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
              '<th style="width: 10%;background-color: #006699; color: white;">Organizacion</th>'+
              '<th style="width: 10%;background-color: #006699; color: white;">pais</th>'+
              '<th style="width: 10%;background-color: #006699; color: white;">KM</th>'+

              '<th style="width: 10%;background-color: #006699; color: white;">Ruta</th>'+
            '</tr>'
          );  
            alert(namePais);
            $.post(base_url+"Inicio/get_calcular_distancia",
            {
              namePais:namePais
            },

          function(data)
          {
            var p = JSON.parse(data);
            var destino2 = [];
            
            $.each(p, function(i, item){ 

                if (item.latitud != null && item.longitud != null ) {

                  var destino = new google.maps.LatLng(item.latitud,item.longitud);
                  destino2.push(destino);
   
                   $('#organizaciones').append(

                    `<tr>
                      <td>${item.id_opp}</td>
                      <td>${item.abreviacion}</td>
                      <td>${item.id_pais}</td>
                      <td id="output-${item.id_opp}"><p class="span_direccion"></p></td>                
                      <td><a href="#" onclick="trazar(${item.latitud}, ${item.longitud});">Ver ruta</a></td>
                    </tr>`);                
                }//end if
            var destinationIcon = '';
            var originIcon = '';
            var geocoder = new google.maps.Geocoder;
            var service = new google.maps.DistanceMatrixService;
            service.getDistanceMatrix({
              origins: [origen],
              destinations: destino2,
              travelMode: 'DRIVING',
              unitSystem: google.maps.UnitSystem.METRIC,
              avoidHighways: false,
              avoidTolls: false
            }, 

            function(response, status) {
              if (status !== 'OK') {} 
              else {
                var originList = response.originAddresses;
                var destinationList = response.destinationAddresses;
                deleteMarkers(markersArray);
             
                let spanObjetivo = document.getElementsByClassName("span_direccion");                
                for (var i = 0; i < originList.length; i++) {
                  var results = response.rows[i].elements;
                  

                  for (var j = 0; j < results.length; j++) {                
                    spanObjetivo[j].innerHTML = results[j].distance.text + ' EN ' +
                    results[j].duration.text; 
                  }//end for
                }//end for
              }
            });
          }); //end each
        }); //end function
      }//en else

    }//end function calcular

    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();
        var onChangeHandler = function() {
      calculateAndDisplayRoute(directionsService, directionsRenderer);
    };
    //funcion para trazar rutas
    trazar = function (latitud, longitud){
      var lat = $("#cx").val();
      var lon = $("#cy").val();
      var start = new google.maps.LatLng(lat,lon);
      var end = new google.maps.LatLng(latitud,longitud);
      directionsService.route
      ({
        origin: start,
        destination:end,
        travelMode: 'DRIVING'
      },
      function(response, status) {
        if (status === 'OK') {
          directionsRenderer.setDirections(response);
        } else {
          window.alert('No existe ruta ' + status);
        }
      });
    }//end function trazar rutas

    var transitLayer = new google.maps.TransitLayer();
    transitLayer.setMap(map);
    directionsRenderer.setMap(map);
    directionsRenderer.setPanel(document.getElementById('bottom-panel')); 
    }//end function search


      function deleteMarkers(markersArray) {
  for (var i = 0; i < markersArray.length; i++) {
    markersArray[i].setMap(null);
  }
  markersArray = [];
}

    function load(){
      
      location.reload();
    }
 
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqBKjIObP2dJsSZCMNOSgj_Jy2BGG18DA&libraries=places&callback=initMap">
    </script>
  </body>             
</html>