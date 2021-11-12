<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Profile Frame</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{url('/')}}/css/croppie.css" rel="stylesheet" async="async" />
        <link href="{{url('/')}}/css/style.css" rel="stylesheet" async="async" />
      </head>
      <body>
        <div id="wrapper">
          <div id="content">
            <h1>Covid-19 Badge</h1>
            <p>Get your Badge</p>
           
        @if (isset($_GET["i"]))
          <div id="preview">
            <div id="crop-area">
              <img src="{{url('uploads/'.$_GET['i'])}}" />
            </div>
          </div>
          <div class="download_info">
            <a class="back" href="{{url('/')}}">Back</a>
            <a class="profile_download"  download href="{{url('uploads/'.$_GET['i'])}}">Download Profile Picture</a>
          </div>

            @else
            <div id="preview">
              <div id="crop-area">
                <img src="http://demos.subinsb.com/isl-profile-pic/image/person.png" id="profile-pic" />
              </div>
              <img  src="frames/frame-1.png" data-design="1" id="fg" data-design="0" />
            </div>
            <p>
              <button id="download" disabled>Edit & Upload</button>
            </p>
            <h2>Upload</h2>
              <input type="file" name="file" onchange="onFileChange(this)">
        @endif

        {{-- Footer --}}
        <div id="comments">
          <p>More than <span class="highlights"> {{$allusers}}+ users </span>are using our frame</p>
          
        </div>

          </div>
        </div>

        <script src="{{url('/js')}}/jquery-3.6.0.min.js"></script>
        <script src="{{url('/js')}}/croppie.min.js" async="async"></script>
        <script>
          function dataURItoBlob(dataURI) {
    // convert base64/URLEncoded data component to raw binary data held in a string
    var byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0)
        byteString = atob(dataURI.split(',')[1]);
    else
        byteString = unescape(dataURI.split(',')[1]);

    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

    // write the bytes of the string to a typed array
    var ia = new Uint8Array(byteString.length);
    for (var i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }

    return new Blob([ia], {type:mimeString});
}

window.uploadPicture = function(callback){
  croppie.result({
    size: "viewport"
  }).then(function(dataURI){
    
    var formData = new FormData();
    formData.append("design", $("#fg").data("design"));
    formData.append("image", dataURItoBlob(dataURI));
    $.ajax({
      type: "POST",
      url:"{{route('uploads.profile')}}",
      data: formData,
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      contentType: false,
      processData: false,
      success: callback,
      error: function(){
        document.getElementById("download").innerHTML = "Please Provide a Profile Picture";
      },
      xhr: function() {
        var myXhr = $.ajaxSettings.xhr();
        if(myXhr.upload){
            myXhr.upload.addEventListener('progress', function(e){
              if(e.lengthComputable){
                var max = e.total;
                var current = e.loaded;
                var percentage = Math.round((current * 100)/max);
                document.getElementById("download").innerHTML = "Uploading... Please Wait... " + percentage + "%";
              }
            }, false);
        }
        return myXhr;
      },
    });
  });
}

window.updatePreview = function(url) {
  document.getElementById("crop-area").innerHTML = "";
  window.croppie = new Croppie(document.getElementById("crop-area"), {
    "url": url,
    boundary: {
      height: 400,
      width: 400
    },
    viewport: {
      width: 400,
      height: 400
    },
  });

  $("#fg").on('mouseover touchstart', function(){
    document.getElementById("fg").style.zIndex = -1;
  });
  $(".cr-boundary").on('mouseleave touchend', function(){
    document.getElementById("fg").style.zIndex = 10;
  });

  document.getElementById("download").onclick = function(){
    this.innerHTML = "Uploading... Please wait...";
    uploadPicture(function(r){
      document.getElementById("download").innerHTML = "Uploaded";
      window.location = "{{url('/')}}?i=" + r;
    });
  };

  document.getElementById("download").removeAttribute("disabled");
};

window.onFileChange = function(input){
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      image = new Image();
      image.onload = function() {
        var width = this.width;
        var height = this.height;
        if(width >= 400 && height >= 400)
          updatePreview(e.target.result);
        else
          alert("Image should be atleast have 400px width and 400px height");
      };
      image.src = e.target.result; 
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$(document).ready(function(){
  $(".design").on("click", function(){
    $("#fg").attr("src", $(this).attr("src")).data("design", $(this).data("design"));
    $(".design.active").removeClass("active");
    $(this).addClass("active");
  });
});
        </script>

      </body>
</html>
