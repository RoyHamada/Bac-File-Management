<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
 
    <title>Document Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   
    <link rel="stylesheet" href="{{ url_for('static', filename='styles.css') }}">
  
</head>
<body>

    <nav class="navbar navbar-inverse visible-xs" >
        <div class="container-fluid"  >
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>                        
            </button>
            <div>
            <h4 style="padding-left: 20px; padding-top: 10px; color: azure;">{{ current_user.username }}!</h4>
           
          </div>
          <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav"><br>
              <li class="active"><a href="{{ url_for('dashboard') }}" class="btn btn-primary">Dashboard</a></li>
              <li><a href="#">Age</a></li>
              <li><a href="#">Gender</a></li>
              <li><a href="#">Geo</a></li>
              <li><a style="padding: 3px; font-size: 10px;" href="{{ url_for('logout') }}" class="btn btn-danger mt-3">Logout</a></li>
            </ul>
          </div>
        </div>
      </nav>
      
      <div class="container-fluid well" >
        <div class="row content" >
          <div class="col-sm-3 sidenav hidden-xs" >
            <div style="padding: 20px;">
                <h4>{{ current_user.username }}!</h4>
            <a style="padding: 3px; font-size: 10px;" href="{{ url_for('logout') }}" class="btn btn-danger mt-3">Logout</a>
            </div>
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="{{ url_for('dashboard') }}" class="btn btn-primary">Dashboard</a></li>
              <li><a href="#section2">Age</a></li>
              <li><a href="#section3">Gender</a></li>
              <li><a href="#section3">Geo</a></li>
            </ul><br>
          </div>
          <br>
          <div class="col-sm-9">
            <div class="well"  style="padding:5px;">
  
              <div style="overflow-x:auto;">


         <div class="container mt-5" style="background-color: #f2f2f2;">
         <h2 style="float: left;">Document Details</h2>
      <div style="padding-top: 20px;">
         
  <!-- Delete Button -->
        <button class="btn btn-danger delete-btn" data-id="{{ document.id }}">
          <i class="fa fa-trash"></i> Delete
      </button>
          
</div> 
          <table class="table table-bordered">
             <tr><th>Project Title</th><td>{{ document.project_title }}</td></tr>
            <tr><th>Contractor</th><td>{{ document.contractor }}</td></tr>
            <tr><th>Date (NTP)</th><td>{{ document.date_ntp }}</td></tr>
            <tr><th>Proprietress</th><td>{{ document.proprietress }}</td></tr>
        </table>
        <div>

        
          <h3 style="float: left;">Uploaded Files</h3> <br> <br> <br>
          <table style="width: 79%;" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Filename</th>
                <th>Function</th>
              </tr>
            </thead>
            <tbody>
              {% if files %}
                {% for file in files %}
                <tr>
                  <td>{{ file.filename }}</td>

                  <td>
                    <a href="{{ url_for('download_file', file_path=file.file_path) }}" class="btn btn-primary btn-sm" download>
                      Download
                    </a>
                    <a href="{{ url_for('doc_preview', filename=file.filename) }}" class="btn btn-info btn-sm">
                      Preview
                  </a>
                  </td>
                </tr>
                {% endfor %}
              {% else %}
                <tr>
                  <td colspan="3" class="text-center">No files uploaded.</td>
                </tr>
              {% endif %}
            </tbody>
          </table>

          <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {% for file in files %}
                <tr>
                    <td>{{ file.filename }}</td>
                    <td>
                        <!-- Preview Button -->
                        <button class="btn btn-info btn-sm" onclick="previewFile('{{ url_for('serve_file', filename=file.filename) }}')">
                            Preview
                        </button>
                    </td>
                </tr>
                {% endfor %}
            </tbody>
        </table>
          
         

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
        $(".delete-btn").click(function() {
            let docId = $(this).data("id");
    
            if (confirm("Are you sure you want to delete this document?")) {
                $.ajax({
                    url: "/delete/" + docId,
                    type: "DELETE",
                    success: function(response) {
                        alert(response.message);
                        window.location.href = "/dashboard"; // Redirect after delete
                    },
                    error: function(xhr) {
                        alert("Error deleting document: " + xhr.responseJSON.error);
                    }
                });
            }
        });
    });
    </script>
    
   
    </body>
</html>