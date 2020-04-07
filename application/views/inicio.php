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


.waypoints{
  margin-top: 150px;
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
    

        <table border="1" id="organizaciones">   
        </table>

        <div class="waypoints">
          <h1>Marcar rutas con puntos intermedios</h1>
            <h2>Paso N°. 1:</h2>    
           <label>Selecciona un pais para poder ver que organizaciones tiene:</label>
          <select id = "pais_waypoints" onchange="search_waypoints()">
            <option value="" selected="">:::Paises:::</option>        
            <?php foreach ($paises as $pais) {?>
              <?php if ($pais->total > 3) {?>
                <option value="<?php echo $pais->id;?>"><?php echo $pais->nombre_pais;?></option>
              <?php }?>
              
            <?php } ?> 
          </select><br><br>

            <h2>Paso N°. 2:</h2>    
          <label>Ubica dentro del mapa el punto de partida:</label><br/>
          <form id="form-star">
            <input type="hidden" name="latitudStar" id="latitudStar">
            <input type="hidden" name="longitudStar" id="longitudStar">
          </form>
          <h2>Paso N°. 3:</h2>    
          <label>Selecciona las organizaciones que deseas visitar como puntos intermedios:</label><br/>
          <select multiple name="waypoints[]" id="waypoints">
            <option value="">Selecciona puntos intermedios</option>
          </select>
          <button id="enviarWay" name="enviarWay" onclick="enviarWay();">Selecionar</button>
          <table id="tableWay"></table>

          <h2>Paso N°. 4:</h2>           
          <label>Selecciona la organizacion que se maracara como destino de la ruta:</label>
          <select name="destination" id="destination" onchange="destination();">
            <option value="">Selecciona tu destino</option>
          </select>

          <table id="tableDestination"></table>
          <h2>Paso N°. 5:</h2> 
          <label>Da clic en el boton (buscar ruta), para buscar la ruta de accesos entre todos los punto que marcaste anteriormente:</label>        
          <button onclick="calcular_waypoints();  ">Buscar Ruta</button>
        </div>        
    </div>
    <div id="right-panel">
  <p>Distancia Total: <span id="total"></span></p>
   <div id="directions-panel"></div>
</div>

    <div id="bottom-panel"></div>

<!--inicio de mapa-->
<script type="text/javascript">
  //variables iniciales 
  var base_url = "<?php echo base_url(); ?>";
  var ubicacion = {lat: 24.6582542, lng: -13.149797};
  var punto_partida = [];
  var map;
  var markersArray = [];
  var icon_SPP = "<?php echo base_url('img/icon-SPP.png')?>";
  var wayglobal = [];
  var way;
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
          animation: google.maps.Animation.DROP,
          icon:icon_SPP
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
            icon:icon_SPP
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
                animation: google.maps.Animation.DROP,
                icon:icon_SPP
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
                  else
                  {
                var originList = response.originAddresses;
                var destinationList = response.destinationAddresses;
                deleteMarkers(markersArray);
             
                let spanObjetivo = document.getElementsByClassName("span_direccion"); 

                for (var i = 0; i < originList.length; i++) {
                  var results = response.rows[i].elements;
              

                  for (var j = 0; j < results.length; j++) {                
                    spanObjetivo[j].innerHTML = results[j].distance.text + ' EN ' + results[j].duration.text; 
                  
                  }//end for
                }//end for
                
              }
              
            });
          }); //end each
        }); //end function
      }//en else

    }//end function calcular

    var directionsService = new google.maps.DirectionsService;

    var directionsRenderer = new google.maps.DirectionsRenderer({             
      map: map,
      suppressMarkers: true,
      panel: document.getElementById('right-panel')
    });
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
        travelMode: 'DRIVING',

      },
      function(response, status) {
        if (status === 'OK') {

          directionsRenderer.setDirections(response);
 

        } else {
          window.alert('No existe ruta ');
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


      search_waypoints = function(){
        var form_star = $("#form-star");
        var pais_waypoints = document.getElementById('pais_waypoints').value;

        if(wayglobal == "")
        {
           //Iniciamos mapa
        map = new google.maps.Map(document.getElementById('map'), 
        {     
          center: ubicacion,
          mapTypeId:'roadmap'
        });

        $("#tableWay").html('');
        $("#tableDestination").html('');
        $("#started").html('');
        $("#started").html('<option value="">Seleciona tu origen</option>');
        $("#waypoints").html('');
        $("#waypoints").html('<option value="">Seleciona puntos intermedios</option>');
        $("#destination").html('');
        $("#destination").html('<option value="">Seleciona tu destino</option>');
        $.post(base_url+"Inicio/get_marcadores_pais",
        {
          id_pais:pais_waypoints
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
                animation: google.maps.Animation.DROP,
                icon:icon_SPP
              });  
              map.setZoom(5);
              var centrar = new google.maps.LatLng(item.latitude, item.longitude);
              map.setCenter(centrar);
              google.maps.event.addListener(marca,"click", function()
              {
                infowindow.open(map, marca);
              });
              marca.setMap(map); 
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

            
                form_star.find("input[name='latitudStar']").val(lista[0]);
                form_star.find("input[name='longitudStar']").val(lista[1]);
                punto_partida.push(marcador);
                quitar_marcadores(punto_partida);
                marcador.setMap(map);      
              });// en function


       
                if (item.latitud != null) {
                  $('#waypoints').append(
                     `<option value="${item.id_opp}">
                                         
                        ${item.abreviacion}        
                     </option>`
                    );
               }  
                if (item.latitud != null) {
                  $('#destination').append(
                     `<option value="${item.id_opp}">
          
                        ${item.abreviacion}        
                     </option>`
                    );
               }       

                });         
              });

          
        }
        else{
          
          wayglobal = [];
           //Iniciamos mapa
        map = new google.maps.Map(document.getElementById('map'), 
        {     
          center: ubicacion,
          mapTypeId:'roadmap'
        });

        $("#tableWay").html('');
        $("#tableDestination").html('');
        $("#started").html('');
        $("#started").html('<option value="">Seleciona tu origen</option>');
        $("#waypoints").html('');
        $("#waypoints").html('<option value="">Seleciona puntos intermedios</option>');
        $("#destination").html('');
        $("#destination").html('<option value="">Seleciona tu destino</option>');
        $.post(base_url+"Inicio/get_marcadores_pais",
        {
          id_pais:pais_waypoints
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
                animation: google.maps.Animation.DROP,
                icon:icon_SPP
              });  
              map.setZoom(5);
              var centrar = new google.maps.LatLng(item.latitude, item.longitude);
              map.setCenter(centrar);
              google.maps.event.addListener(marca,"click", function()
              {
                infowindow.open(map, marca);
              });
              marca.setMap(map); 
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

            
                form_star.find("input[name='latitudStar']").val(lista[0]);
                form_star.find("input[name='longitudStar']").val(lista[1]);
                punto_partida.push(marcador);
                quitar_marcadores(punto_partida);
                marcador.setMap(map);      
              });// en function


       
                if (item.latitud != null) {
                  $('#waypoints').append(
                     `<option id="way_${item.id_opp}" value="${item.id_opp}">
                                         
                        ${item.abreviacion}        
                     </option>`
                    );
               }  
                if (item.latitud != null) {
                  $('#destination').append(
                     `<option value="${item.id_opp}">
          
                        ${item.abreviacion}        
                     </option>`
                    );
               }   

                });         
              });
        }
        
       
            }
          //multi select de waypoint
            enviarWay = function(){
              var waypoints = $('#waypoints').val(); 
       

                if(waypoints == ""){
                  alert("Selecciona almenos una organizacion como punto intermedio");

                }   
                else{     
              
                   $.ajax({
                      type: 'POST',
                      url: base_url+"Inicio/get_way",
                      data:
                      {
                        id_organizacion:waypoints    
                      },
                      success: function(data)
                      {
             
                       var p = JSON.parse(data);

                    $.each(p, function(i, item){
                
                      $('#tableWay').append(
                      `
                      <tr id="${item.id_opp}" class="tr-way">
                          <td><input type="text" name="" id="" value="${item.abreviacion}"/></td>                  
                          <td><input type="text" name="latitudWay[]" id="latitudWay" value="${item.latitud}"/></td>
                          <td><input type="text" name="longitudWay[]" id="longitudWay" value="${item.longitud}"/></td>
                          <td><button id="quitar" onclick="quitar(${item.id_opp},${item.latitud},${item.longitud});">Quitar</button></td>

                      </tr>`
                     );               
                    //se crean las cooredanadas de objetos para los waypoints 
                      way = new google.maps.LatLng(item.latitud,item.longitud);
                      wayglobal.push(way);
                      $('#waypoints option[value="'+item.id_opp+'"]').remove();
                    }); 
                      }
                    });//end Ajax
              }//end else
            } //enn function enviarway

            quitar = function(id_opp,latitud,longitud){

                    var quitarway = new google.maps.LatLng(latitud,longitud);

                    for (var i = 0; i < wayglobal.length; i++) {
                      if(JSON.stringify(wayglobal[i]) === JSON.stringify(quitarway)){
                        wayglobal.splice(i, 1);
                     
                      }

            } 
            $("#"+id_opp).remove();

            $.post(base_url+"Inicio/get_organizaciones",
              {
                id_organizacion:id_opp
              },
      
              function(data)
              {
                var p = JSON.parse(data);

                $.each(p, function(i, item){
                  $("#waypoints").append('<option value="'+id_opp+'">'+item.abreviacion+'</option>');
               

                });         
              });
            

            }

            destination = function(){
                var destination = document.getElementById('destination').value;
            $("#tableDestination").html('');
              $.post(base_url+"Inicio/get_organizaciones",
              {
                id_organizacion:destination
              },
      
              function(data)
              {
                var p = JSON.parse(data);

                $.each(p, function(i, item){
                  $('#tableDestination').append(
                  `
                  <tr>
                      <td><input type="text" name="" id="" value="${item.abreviacion}"/></td>           
                      <td><input type="hidden" name="latitudEnd" id="latitudEnd" value="${item.latitud}"/></td>
                      <td><input type="hidden" name="longitudEnd" id="longitudEnd" value="${item.longitud}"/></td>
                  </tr>`
                 );

                });         
              });

            }

        calcular_waypoints = function(){

  
          var pais_waypoints = $("#pais_waypoints");
          var latitudStar = $("#latitudStar").val();
          var longitudStar = $("#longitudStar").val();
          var latitudEnd = $("#latitudEnd").val();
          var longitudEnd = $("#longitudEnd").val();
         
          if(pais_waypoints === undefined){
            alert("No has seleccionado ningun pais");
          }
         else if (latitudStar == "") {
            alert("error no has marcado tu punto de partida");
          }
           else if(wayglobal == ""){
            alert("Debes de seleccionar almenos un punto intermedio para poder marcar una ruta");
          }
          else if(latitudEnd == undefined){ 
            alert("error no has marcado tu destino");  
          }

          else{
            map = new google.maps.Map(document.getElementById('map'), 
              {     
                center: ubicacion,
                mapTypeId:'roadmap'
              });
              var directionsService = new google.maps.DirectionsService;
            var directionsRenderer = new google.maps.DirectionsRenderer({             
              map: map,
              //suppressMarkers:true,
              panel: document.getElementById('right-panel')
            });

              directionsRenderer.addListener('directions_changed', function() {
              computeTotalDistance(directionsRenderer.getDirections());
            });
            var origen = new google.maps.LatLng(latitudStar,longitudStar);
            var destination = new google.maps.LatLng(latitudEnd,longitudEnd);
            calculateAndDisplayRoute(directionsService, directionsRenderer, origen, destination, wayglobal);

          }
            //Iniciamos mapa
             


          
        } //end calcular way     


        function calculateAndDisplayRoute(directionsService, directionsRenderer, origen, destination, wayglobal) {
          var form_star1 = $("#form-star");
           var waypts = [];
            for (var i = 0; i < wayglobal.length; i++) {
              waypts.push({
                    location: wayglobal[i],
                    stopover: true
                  });
            }
  
            directionsService.route({
              origin: origen,
              destination: destination,
              waypoints: waypts,
              optimizeWaypoints: true,
              travelMode: 'DRIVING'
            }, function(response, status) {
              if (status === 'OK') {
                alert("se encontro la mejor ruta para su viaje");
                directionsRenderer.setDirections(response);
                var route = response.routes[0];
                var summaryPanel = document.getElementById('directions-panel');
                summaryPanel.innerHTML = '';
                // For each route, display summary information.
                for (var i = 0; i < route.legs.length; i++) {
                  var routeSegment = i + 1;
                  summaryPanel.innerHTML += '<b>Ruta de segmento: ' + routeSegment +
                      '</b><br>';
                  summaryPanel.innerHTML += route.legs[i].start_address + ' A ';
                  summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                  summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                }
              } else {
                alert('No hemos encontrado una ruta Intenta de nuevo con nuevas ubicaciones');

            var pais_waypoints = document.getElementById('pais_waypoints').value;
                     //Iniciamos mapa
            map = new google.maps.Map(document.getElementById('map'), 
            {     
              center: ubicacion,
              mapTypeId:'roadmap'
            });

            $("#started").html('');
            $("#started").html('<option value="">Seleciona tu origen</option>');
            $("#waypoints").html('');
            $("#waypoints").html('<option value="">Seleciona puntos intermedios</option>');
            $("#destination").html('');
            $("#destination").html('<option value="">Seleciona tu destino</option>');
            $.post(base_url+"Inicio/get_marcadores_pais",
            {
              id_pais:pais_waypoints
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
                    animation: google.maps.Animation.DROP,
                    icon:icon_SPP
                  });  
                  map.setZoom(5);
                  var centrar = new google.maps.LatLng(item.latitude, item.longitude);
                  map.setCenter(centrar);
                  google.maps.event.addListener(marca,"click", function()
                  {
                    infowindow.open(map, marca);
                  });
                  marca.setMap(map); 

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

                
                    form_star1.find("input[name='latitudStar']").val(lista[0]);
                    form_star1.find("input[name='longitudStar']").val(lista[1]);
                    punto_partida.push(marcador);
                    quitar_marcadores(punto_partida);
                    marcador.setMap(map);      
                  });// en function


           
                    if (item.latitud != null) {
                      $('#waypoints').append(
                         `<option value="${item.id_opp}">
                                             
                            ${item.abreviacion}        
                         </option>`
                        );
                   }  
                    if (item.latitud != null) {
                      $('#destination').append(
                         `<option value="${item.id_opp}">
              
                            ${item.abreviacion}        
                         </option>`
                        );
                   }       

                    });         
                  });
                  }
                });
          }
      function computeTotalDistance(result) {
        var total = 0;
        var myroute = result.routes[0];

        for (var i = 0; i < myroute.legs.length; i++) {
          total += myroute.legs[i].distance.value;
        }
        total = total / 1000;
        document.getElementById('total').innerHTML = total + ' km';
      }
</script>
   <div id="directions-panel"></div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqBKjIObP2dJsSZCMNOSgj_Jy2BGG18DA&libraries=places&callback=initMap">
    </script>
  </body>             
</html>