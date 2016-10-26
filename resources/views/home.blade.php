@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 ">
            <div class="panel panel-default">
                <div class="panel-heading">Areas</div>

                <div class="panel-body">
                  <div style="height: 450px;" id="map">

                  </div>
                </div>
            </div>
            <div class="panel panel-warning">
              <div class="panel-heading">API Information (All Polygons)</div>
              <div class="panel-body">
                <div class="well" >
                  <h4>POST Request</h4>
                  <pre><code id="post_url_all"></code></pre>
                  <pre><code id="post_info_all">
{
  "latitude": "19.418690",
  "longitude": "-99.185987"
}
                  </code></pre>
                  <p>*Replace with your location</p>
                </div>
                <div class="well">
                  <h4>GET Request</h4>
                  <pre><code id="get_url_all">

                  </code></pre>
                  <p>*Replace with your location</p>
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-4">
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">Create</div>
              <div class="panel-body">
                <a type="button" href="/create" class="col-md-12 btn btn-primary ">Create New Area</a><br>
              </div>
            </div>
            <div class="panel panel-info">
              <div class="panel-heading">Information</div>
              <div class="panel-body">
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon3">Name</span>
                  <input type="text" class="form-control" id="polygon-name" aria-describedby="basic-addon3">
                  <input type="hidden" class="form-control" id="polygon-id" aria-describedby="basic-addon3">
                </div><br>
                <button class="btn btn-success col-md-12" id="save-polygon" role="button" disabled>Save Name</button><br><br>
                <button class="btn btn-danger col-md-12" id="delete-polygon" role="button" disabled>Delete Polygon</button><br>
              </div>
            </div>
            <div class="panel panel-warning">
              <div class="panel-heading">API Information</div>
              <div class="panel-body">
                <div class="well" >
                  <h4>POST Request</h4>
                  <pre><code id="post_url"></code></pre>
                  <pre><code id="post_info">
{
	"latitude": "19.418690",
	"longitude": "-99.185987"
}
                  </code></pre>
                  <p>*Replace with your location</p>
                </div>
                <div class="well">
                  <h4>GET Request</h4>
                  <pre><code id="get_url"></code></pre>
                  <p>*Replace with your location</p>
                </div>
              </div>
            </div>
          </div> <!-- End Panel Group -->
        </div>
    </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}" />


<script type="text/javascript">

var map;
var infoWindow;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 14,
    center: {lat: 19.434381178461, lng: -99.1637134552},
    mapTypeId: google.maps.MapTypeId.TERRAIN

  });

  $("#post_url_all").text(location.protocol + "//" + location.host + base_url + "/user/{{Auth::id()}}" );
  $("#get_url_all").text(location.protocol + "//" + location.host + base_url + "/user/{{Auth::id()}}"  + "?latitude={latitude}&longitude={longitude}") ;

  $("#post_info").hide();

  $("#delete-polygon").click(function(){
    if(polygon_selected){
      deletePolygon($("#polygon-id").val());
      infoWindow.open(null);
      polygon_selected.setMap(null);
      $("#polygon-id").val("");
      $("#polygon-name").val("");
      $("#get_url").text("");
      $("#post_url").text("");
      $("#delete-polygon").prop( "disabled", true );
      $("#save-polygon").prop( "disabled", true );
      $("#post_info").hide();

    }else{
      toastr.info("Empty");
    }
  });

  $("#save-polygon").click(function(){
    if(polygon_selected){
      updatePolygon($("#polygon-id").val(), $("#polygon-name").val());
      polygon_selected.name = $("#polygon-name").val();
      infoWindow.setContent(polygon_selected.name);
    }else{
      toastr.info("Empty");
    }
  });

  var polygon_selected;
  var base_url = "/api/v1/"

  var addListenersOnPolygon = function(polygon) {
    google.maps.event.addListener(polygon, 'click', function (event) {
      infoWindow.setContent(polygon.name);
      infoWindow.setPosition(event.latLng);
      infoWindow.open(map);
      $("#polygon-name").val(polygon.name);
      $("#polygon-id").val(polygon.id);
      $("#post_url").text(location.protocol + "//" + location.host + base_url + "/polygon/" + polygon.id);
      $("#get_url").text(location.protocol + "//" + location.host + base_url + "/polygon/" + polygon.id + "?latitude={latitude}&longitude={longitude}") ;
      $("#delete-polygon").prop( "disabled", false );
      $("#save-polygon").prop( "disabled", false );
      $("#post_info").show();
      polygon_selected = polygon;

    });
  }

  @foreach($polygons as $polygon)
    var p = new google.maps.Polygon({
        paths: {!! $polygon["points"] !!},
        strokeWeight: 0,
        fillColor: '#FF0000',
        fillOpacity: 0.6,
        name: "{{ $polygon["name"] }}",
        id: "{{ $polygon["id"] }}"
    });
    p.setMap(map);
    addListenersOnPolygon(p);
  @endforeach

  infoWindow = new google.maps.InfoWindow;
}

function deletePolygon(id){
  $.ajaxSetup({
    headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    $.ajax({
       type:'POST',
       url:'/polygon/delete',
       data: {polygon_id: id},
       dataType: 'json',
       success:function(data){
         toastr.info(data.msg);
         console.log(data.msg);
       },
       error: function(error){
         console.log(error);
       }
    });
 }

 function updatePolygon(id, name){
   $.ajaxSetup({
     headers: {
             'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
         }
     })
     $.ajax({
        type:'POST',
        url:'/polygon/update',
        data: {polygon_id: id, name: name},
        dataType: 'json',
        success:function(data){
          toastr.info(data.msg);
          console.log(data.msg);
        },
        error: function(error){
          console.log(error);
        }
     });
  }


</script>
@endsection
