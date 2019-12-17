<!DOCTYPE html>
<html>
  <head>
     <style type="text/css">
    #map
    {
      width: 1000px;
      height: 850px;
      float:left;
    }
     #informacion
    {
      margin-left: 2em;
      width: 600px;
      height: 600px;
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
      <button style="margin-left: 15px;" id="ubicación">Mi ubicación</button>
      <table>
        <tr>
          <td>Lugar:</td>
          <td></td>
        </tr>
        <tr>
          <td>Latitud:</td>
          <td></td>
        </tr>
        <tr>
          <td>Longitud:</td>
          <td></td>
        </tr>
        <tr>
          <td>Pais:</td>
          <td></td>
        </tr>
        <tr>
          <td>Ruta:</td>
          <td><button id="ruta">Ver ruta</button></td>
        </tr>


      </table>

    </div>
    <script>
    var base_url = "<?php echo base_url(); ?>";
    function initMap() 
    {

      $("#ubicación").click(function(){
        navigator.geolocation.getCurrentPosition(fn_ok, fn_error);

      var divMapa = document.getElementById('map');
      function fn_error(){
       divMapa.innerHTML='Permite dar a conocer tu ubicación';
      }
     function fn_ok(respuesta){
        var lat = respuesta.coords.latitude;
        var lon = respuesta.coords.longitude;
       
        var glatLon = new google.maps.LatLng(lat, lon);
        var iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
          var marker = new google.maps.Marker({
            position: glatLon,
            map: map,
            title:"Esta es tu ubicacion",
            icon: iconBase + 'parking_lot_maps.png'
          })
      }

      });//end funcion de ubicacion 
      $("#ruta").click(function(){
        alert("ok");
      });
      
        var ubicacion = {lat: 24.6582542, lng: -13.149797};
      map = new google.maps.Map(document.getElementById('map'), 
      {
        zoom: 3,      
        center: ubicacion,
        mapTypeId:'roadmap'
      });
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


   


      
    
      
      //}
   } //end function initMap

    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqBKjIObP2dJsSZCMNOSgj_Jy2BGG18DA&callback=initMap">
    </script>
  </body>
</html>