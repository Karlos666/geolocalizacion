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
  </style>
    <title>Geolocalización</title>
    <script type="text/javascript"></script>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
  
  </head>
  <body>
    <div id="map"></div>
    <div class="informacion">
      <button style="margin-left: 15px;" id="ubicación">Mi ubicación</button><br> <input type="hidden" name="x" id="x"> <input type="hidden" name="y" id="y"><br>

<br/>
<form id="formulario" action="" method="post">
  <input type="text" name="cx" id="cx"> 
  <input type="text" name="cy" id="cy">
</form>
<br/>

 <select id = "pais" onchange="search()">
      <?php foreach ($paises as $pais) {?>
        <option value="<?php echo $pais->id;?>"><?php echo $pais->nombre_pais;?></option>
     <?php } ?> 
    </select>


      <input onclick = " clearMarkers (); " type = button value = "Ocultar marcadores" >
      <button id="ver">Ver todo</button>
      <button id="ver_rutas">Ver todas las rutas</button>

      <table border="1" id="organizaciones">
   


      </table>

    </div>
    <script>

    var base_url = "<?php echo base_url(); ?>";


var punto_partida = [];
function quitar_marcadores(lista){
  for (i in lista) {
    lista[i].setMap(null);
  }

}

function initMap() 
{ 
  //funcion para iniciar la ubicacion actual



  navigator.geolocation.getCurrentPosition(fn_ok4, fn_error);

  var divMapa = document.getElementById('map');
  function fn_error(){
    divMapa.innerHTML='Permite dar a conocer tu ubicación';
  }

  function fn_ok4(respuesta)
  {
 
    var lat = respuesta.coords.latitude;
    var lon = respuesta.coords.longitude;
    var text_lat = $("#x").val(lat);
    var text_lon = $("#y").val(lon);
    glatLon = new google.maps.LatLng(lat, lon);
    var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
    var marker = new google.maps.Marker({
    position: glatLon,
    map: map,
    title:"Esta es tu ubicacion",
    icon: iconBase + 'parking_lot_maps.png'
    })

  }// end function

  
  var formulario = $("#formulario");
  var directionsService = new google.maps.DirectionsService();
  var directionsRenderer = new google.maps.DirectionsRenderer();
  var ubicacion = {lat: 24.6582542, lng: -13.149797};
   var map = new google.maps.Map(document.getElementById('map'), 
  {
    zoom: 2,      
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
  var marcador = new google.maps.Marker
  ({
    position:direcion,
    map:map,
    animation:google.maps.Animation.DROP,
    draggable:false
  });
  
  formulario.find("input[name='cx']").val(lista[0]);
  formulario.find("input[name='cy']").val(lista[1]);
  punto_partida.push(marcador);
  google.maps.event.addListener(marcador,"click",function()
  {
    alert(marcador.titulo);
  });
  quitar_marcadores(punto_partida);
  marcador.setMap(map);      
});
   

  directionsRenderer.setMap(map);

  var onChangeHandler = function() {
    calculateAndDisplayRoute(directionsService, directionsRenderer);
  };


///funcion para mostrar todos los puntos 
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
});
  
  //funcion para buscar puntos por pais
  search = function(){

    var pais  = document.getElementById('pais').value;
    $('#organizaciones').html(
    '<tr>'+
              '<th style="width: 10%;background-color: #006699; color: white;">#</th>'+
              '<th style="width: 10%;background-color: #006699; color: white;">id_pais</th>'+

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
                $('#organizaciones').append(
                  `<tr>
                  <td>${item.id_opp}</td>
                  <td>${item.fk_id_pais}</td>
                  <td>${item.abreviacion}</td>
                  <td><a href="#" onclick="trazar(${item.latitud}, ${item.longitud});">Ver ruta</a></td>
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

                    animation: google.maps.Animation.DROP
                  });  
                  
                google.maps.event.addListener(marca,"click", function()

                {

                  infowindow.open(map, marca);
                });   

               
                marca.setMap(map);  

              });         
            });
 


  }//end function search
    //funcion para trazar rutas
    trazar = function (latitud, longitud){
        var destino_lat = $("#x").val();
        var destino_lon = $("#y").val();
        var start = new google.maps.LatLng(destino_lat,destino_lon);
        var end = new google.maps.LatLng(latitud,longitud);

            directionsService.route(
                {
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
          

      }

      //funcion para limpiar el mapa
      clearMarkers = function(){ 
        initMap(null);
      }

        ver_rutas = function(){
          alert("okjisdf");
        }


   } //end function initMap

  



    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqBKjIObP2dJsSZCMNOSgj_Jy2BGG18DA&callback=initMap">
    </script>
  </body>             
</html>