<!DOCTYPE html>
<html>
  <head>
     <style type="text/css">
    #map
    {
      width: 700px;
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



  </style>
    <title>Geolocalización</title>
    <script type="text/javascript"></script>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
  
  </head>
  <body>
    <div id="map"></div>

    <div id="infowindow-content">
      <!---<span id="place-name"  class="title"></span><br>
      <span id="place-id"></span><br>--><br>
      <span id="place-address"></span>
    </div>
    <div class="informacion">
      <label>Controles generales de mapa</label>
      <button style="margin-left: 15px;" id="ubicación">Mi ubicación</button>
      <input onclick = " clearMarkers (); " type = button value = "Ocultar marcadores" >
      <button id="ver">Ver todas las organizaciones</button>
      <br> 
      <input type="hidden" name="x" id="x"> <input type="hidden" name="y" id="y">
     <button id="calcular" name="calcular" type="button">Calcular</button>

    <br/>
    <form id="formulario" action="" method="post">
      <input id="pac-input" class="controls" type="text" placeholder="Ingresa un lugar">

      <input type="hidden" id="lugar_ubicacion"><br>
      
      <table class="table" id="organizaciones2"></table>
      <br>
      <label>Estas son tus coordenadas de punto de partida: </label><br>
      <label>Latitud: </label>
      <input type="text" name="cx" id="cx" required=""> 
      <label>Longitud: </label>
      <input type="text" name="cy" id="cy" required="">
      <br>
       <select id = "pais1" onchange="search1()">
      <?php foreach ($paises as $pais) {?>
        <option value="<?php echo $pais->id;?>"><?php echo $pais->nombre_pais;?></option>
     <?php } ?> 
    </select>
    <table border="1" id="organizaciones1"></table>
    </form>
    <br/>
    <label>Buscador de Organizaciones por pais:</label>
    <select id = "pais" onchange="search()">
      <?php foreach ($paises as $pais) {?>
        <option value="<?php echo $pais->id;?>"><?php echo $pais->nombre_pais;?></option>
     <?php } ?> 
    </select>

      <table border="1" id="organizaciones"></table>
    
    </div>

    <div id="bottom-panel"></div>

<!--inicio de mapa-->
<script>
  //variables globales
   var base_url = "<?php echo base_url(); ?>";
  var punto_partida = [];

  function quitar_marcadores(lista)
  {
    for (i in lista) {
      lista[i].setMap(null);
    }//end for
  }//end funtion quitar marcadores


  //funcion inicial de initMap
  function initMap() 
  { 
    var bounds = new google.maps.LatLngBounds;
    var markersArray = [];
    //funcion para iniciar la ubicacion actual
    var infoWindow = new google.maps.InfoWindow; 
    navigator.geolocation.getCurrentPosition(fn_ok, fn_error);
    var divMapa = document.getElementById('map');
  
    function fn_error(){
      divMapa.innerHTML='Permite dar a conocer tu ubicación';
    }//en function fn_error

    function fn_ok(respuesta)
    {
      var lat = respuesta.coords.latitude;
      var lon = respuesta.coords.longitude;
      var text_lat = $("#x").val(lat);
      var text_lon = $("#y").val(lon);
      glatLon = new google.maps.LatLng(lat, lon);
      infoWindow.setPosition(glatLon);
      infoWindow.setContent('esta es tu ubicacion.');
      infoWindow.open(map);
    }// end function fn_ok

    var formulario = $("#formulario");



    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();

    var ubicacion = {lat: 24.6582542, lng: -13.149797};
    //se inicializa el mapa
    var map = new google.maps.Map(document.getElementById('map'), 
    {
      zoom: 2,      
      center: ubicacion,
      mapTypeId:'roadmap'
    });

    //inicia buscador de lugares
     var input = document.getElementById('pac-input');

      var autocomplete = new google.maps.places.Autocomplete(input);

      autocomplete.bindTo('bounds', map);

      // Specify just the place data fields that you need.
      autocomplete.setFields(['place_id', 'geometry', 'name', 'formatted_address']);

      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      var infowindow = new google.maps.InfoWindow();
      var infowindowContent = document.getElementById('infowindow-content');
      infowindow.setContent(infowindowContent);

      var geocoder = new google.maps.Geocoder;

      var marker = new google.maps.Marker({
        map: map, 
        draggable: true

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

        // Set the position of the marker using the place ID and location.
        marker.setPlace(
            {placeId: place.place_id, location: results[0].geometry.location});

        marker.setVisible(false);

        //var name_lugar = infowindowContent.children['place-name'].textContent = place.name;
        //var id_lugar = infowindowContent.children['place-id'].textContent = place.place_id;
        var direccion_lugar = infowindowContent.children['place-address'].textContent = results[0].formatted_address;
        

        var elarray = direccion_lugar.split(",");
        var pais = elarray[elarray.length - 1];
        $("#lugar_ubicacion").val(pais.trim());
        var namePais = $("#lugar_ubicacion").val(); 
      
      $.post(base_url+"Inicio/get_name_pais",
      {
        namePais:namePais
      },

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
          google.maps.event.addListener(marca,"click", function()
          {
            infowindow.open(map, marca);
          });   
          marca.setMap(map); 
   
        });         
      });  //end funtion  
      infowindow.open(map, marker);
      });
    });

    //end buscador de lugares


    var transitLayer = new google.maps.TransitLayer();
    transitLayer.setMap(map);


    directionsRenderer.setMap(map);
    directionsRenderer.setPanel(document.getElementById('bottom-panel')); 


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
   
    $("#calcular").click(function(){
    //clearMarkers();

  var div_salida = [];

        var contador  = 0;
      var cx = $("#cx").val();
      var cy = $("#cy").val();
        var origin1 = new google.maps.LatLng(cx , cy);
      var pias_organizacion = $("#lugar_ubicacion").val();
      //alert(pias_organizacion);

           $('#organizaciones2').html(
        '<tr>'+
          '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Organizacion</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">KM</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Ruta</th>'+
        '</tr>'
      );

      $.post(base_url+"Inicio/get_calcular_distancia",
        {
        pias_organizacion:pias_organizacion
      },

      function(data)
      {
        var p = JSON.parse(data);
        var destino2 = [];
        $.each(p, function(i, item){ contador++;
            if (item.latitud != null && item.longitud != null ) {
              var el_id_opp = item.id_opp;
          var destino = new google.maps.LatLng(item.latitud,item.longitud);
          destino2.push(destino);
          $('#organizaciones2').append(
            `<tr>
              <td>${contador}</td>
              <td>${item.abreviacion}</td>
              <td id="output-${item.id_opp}"><p class="span_direccion"></p></td>
              <td><a href="#" onclick="trazar1(${item.latitud}, ${item.longitud});">Ver ruta</a></td>
            </tr>`
          );
        }//end if
          var destinationIcon = '';
    var originIcon = '';
    


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

    if(el_id_opp != undefined){

    /*  var div_salida = document.getElementById('output-'+el_id_opp).id;
      console.log("id:"+el_id_opp);
      
      console.log("div:"+div_salida);*/
    }

    if (status !== 'OK') {
      //alert('Error : ' + status);
    } else {



      var originList = response.originAddresses;
      var destinationList = response.destinationAddresses;
    

  

  
      deleteMarkers(markersArray);

      var showGeocodedAddressOnMap = function(asDestination) {
        var icon = asDestination ? destinationIcon : originIcon;
        /*return function(results, status) {
          if (status === 'OK') {
            map.fitBounds(bounds.extend(results[0].geometry.location));
            markersArray.push(new google.maps.Marker({
              map: map,
              position: results[0].geometry.location,
              icon: icon
            }));
          } 
        };*/
      };
      let spanObjetivo = document.getElementsByClassName("span_direccion");


      for (var i = 0; i < originList.length; i++) {
        var results = response.rows[i].elements;
      console.log('el total de results: '+results.length);
      
        geocoder.geocode({'address': originList[i]},
            showGeocodedAddressOnMap(false));

        for (var j = 0; j < results.length; j++) {
          geocoder.geocode({'address': destinationList[j]},
              showGeocodedAddressOnMap(true));

             spanObjetivo[j].innerHTML = results[j].distance.text + ' EN ' +
             results[j].duration.text; 

        }

      }

      
    }

  });

        }); //end each


    });
  });


    //funcion para mostrar todos los puntos 
    $("#ver").click(function(){
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
          google.maps.event.addListener(marca,"click", function()
          {
            infowindow.open(map, marca);
          });   
          marca.setMap(map);  
        });
      });
    });//end function poara ver todos los puntos


  
        //funcion para buscar puntos por pais
    search1 = function(){
      var pais  = document.getElementById('pais1').value;
      $('#organizaciones1').html(
        '<tr>'+
          '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Organizacion</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Ruta</th>'+
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
        if (item.latitud != null && item.longitud != null ) { 
          $('#organizaciones1').append(
            `<tr>
              <td>${item.id_opp}</td>
              <td>${item.abreviacion}</td>
              <td><a href="#" onclick="trazar1(${item.latitud}, ${item.longitud});">Ver ruta</a></td>
            </tr>`
          );
        }//end if
        if (pais) {
          var infowindow = new google.maps.InfoWindow
          ({
            content:item.abreviacion + item.latitud,
            maxWidth: 200
          });
          var posi = new google.maps.LatLng(item.latitud, item.longitud);     
          var marca = new google.maps.Marker
          ({       
            position:posi,
            animation: google.maps.Animation.DROP
          });  
          google.maps.event.addListener(marca,"click", function()
          {
            infowindow.open(map, marca);
          });   
          marca.setMap(map); 
        }

        });         
      });
    }//end function search


     //funcion para buscar puntos por pais
    search = function(){
      var pais  = document.getElementById('pais').value;
      $('#organizaciones').html(
        '<tr>'+
          '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Organizacion</th>'+
          '<th style="width: 10%;background-color: #006699; color: white;">Ruta</th>'+
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
        if (item.latitud != null && item.longitud != null ) { 
          $('#organizaciones').append(
            `<tr>
              <td>${item.id_opp}</td>
              <td>${item.abreviacion}</td>
              <td><a href="#" onclick="trazar1(${item.latitud}, ${item.longitud});">Ver ruta</a></td>
            </tr>`
          );
        }//end if
        if (pais) {
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
          google.maps.event.addListener(marca,"click", function()
          {
            infowindow.open(map, marca);
          });   
          marca.setMap(map); 
        }

        });         
      });
    }//end function search




    var onChangeHandler = function() {
      calculateAndDisplayRoute(directionsService, directionsRenderer);
    };

    //funcion para trazar rutas
    trazar = function (latitud, longitud){
      var lat = $("#x").val();
      var lon = $("#y").val();

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

      //funcion para trazar rutas
    trazar1 = function (latitud, longitud){
      var destino_lat = $("#cx").val();
      var destino_lon = $("#cy").val();

      var start = new google.maps.LatLng(destino_lat,destino_lon);
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


    //funcion para multi rutas
    $("#multiruta").click(function(){
      var paises = $("#paises").val();
   
      $.post(base_url+"Inicio/get_marcadores_pais",
      {
        id_pais:paises
      },
      function(data)
      {
        //alert(data);
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
          google.maps.event.addListener(marca,"click", function()
          {
            infowindow.open(map, marca);
          });   
          marca.setMap(map); 
          var destino_lati = $("#x").val();
          var destino_long = $("#y").val();

      var start = new google.maps.LatLng(destino_lati,destino_long);
      var end = new google.maps.LatLng(item.latitud,item.longitud);
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

        });         
      });

    });

    //funcion para limpiar el mapa
    clearMarkers = function(){ 
      initMap(null);
    }//end function limpiar el mapa

  } //end function initMap

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
  </body>             
</html>