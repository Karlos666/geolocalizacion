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
    <script>
    var base_url = "<?php echo base_url(); ?>";
    function initMap() 
    {
   
      //navigator.geolocation.getCurrentPosition(fn_ok, fn_error);;

     // var divMapa = document.getElementById('map');
     // function fn_error(){
       // divMapa.innerHTML='Permite dar a conocer tu ubicación';
      //}
     // function fn_ok(respuesta){
        //var lat = respuesta.coords.latitude;
        //var lon = respuesta.coords.longitude;
       
        //var glatLon = new google.maps.LatLng(lat, lon);
        var ubicacion = {lat: 24.6582542, lng: -13.149797};
      map = new google.maps.Map(document.getElementById('map'), 
      {
        zoom: 1.5,      
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
          content:item.name,
          maxWidth: 200
        });
        var posi = new google.maps.LatLng(item.latitude, item.longitude);     
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