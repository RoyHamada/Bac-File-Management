<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">



    <title>Dashboard</title>
 
    <link rel="stylesheet" href="{{ url_for('static', filename='styles.css') }}">
    
</head>
<body>
    <nav class="navbar navbar-inverse visible-xs">
        <div class="container-fluid"  >
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>                        
            </button>
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
              <h4>Library</h4>
            
              <div style="overflow-x:auto;">
          <!-- Add Icon Button -->
           <div>
          
        </div>

            <form class="navbar-form navbar-right" role="search">
                <div class="input-group">
                  <input type="text" id="myInput" onkeyup="myFunction()" style=" height: 34px; border-radius: 4px 0 0 4px;" type="text" class="form-control" placeholder="Search">
                  <span class="input-group-btn" style="padding-top: 10px;">
                    <butto class="btn btn-default" type="submit">
                      <i class="fa fa-search"></i>
                    </button>
                  </span>
                </div>
              </form>

           
        <!-- Align button to the left on top of the table -->
        
        <div class="text-left mb-3" style="padding-top: 20px; padding-left: 10px; padding-bottom: 10 px;">
          <button class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
              <i class="fa fa-plus"></i> Add Document
          </button>
      </div>

        <!-- Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" style="text-align: left; padding: 20px; padding-right: 20px;">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                      <h4 class="modal-title" id="uploadModalLabel">Upload Document</h4>
                  </div>
                  <div class="modal-body">
                      <form action="/upload_document" method="POST" enctype="multipart/form-data">
                          <div class="form-group">
                              <label>Project Title</label>
                              <input type="text" name="project_title" class="form-control" required>
                          </div>
                          <div class="form-group">
                              <label>Contractor</label>
                              <input type="text" name="contractor" class="form-control" required>
                          </div>
                          <div class="form-group">
                              <label>Date (NTP)</label>
                              <input type="date" name="date_ntp" class="form-control" required>
                          </div>
                          <div class="form-group">
                              <label>Proprietress</label>
                              <input type="text" name="proprietress" class="form-control" required>
                          </div>
                          <div class="form-group">
                              <label>Upload Files</label>
                              <input type="file" name="files" class="form-control" multiple required>
                          </div>
                          <button type="submit" class="btn btn-primary">Submit</button>

                      </form>
                  </div>
              </div>
          </div>
      </div>

      <table id="myTable">
        <thead>
            <tr>
                <th>No.</th>
                <th>Project Title</th>
                <th>Contractor</th>
                <th>Date (NTP)</th>
                <th>Proprietress</th>
                <th>Files</th>
            </tr>
        </thead>
        <tbody>
          {% for doc in documents %}
          <tr data-id="{{ doc.id }}">
              <td>{{ loop.index }}</td>
              <td>{{ doc.project_title }}</td>
              <td>{{ doc.contractor }}</td>
              <td>{{ doc.date_ntp }}</td>
              <td>{{ doc.proprietress }}</td>
              <td>
                  <ul>
                      {% for file in doc.files %}
                          <li><a href="{{ url_for('download_file', file_path=file.file_path) }}">{{ file.file_name }}</a></li>
                      {% endfor %}
                  </ul>
              </td>
          </tr>
          {% endfor %}
      </tbody>
    </table>
    <script>
        function viewDocument(documentId) {
            window.location.href = "/document/" + documentId;
        }
    </script>
    
    </table>
    
        
      </div>
            </div>
            
            </div>
          </div>
        </div>
      </div>
      

      <script>
              function myFunction() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById("myInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("myTable");
                tr = table.getElementsByTagName("tr");

                for (i = 1; i < tr.length; i++) { // Start from index 1 to skip table headers
                    td = tr[i].getElementsByTagName("td");
                    let rowMatch = false;

                    for (j = 0; j < td.length; j++) { // Loop through all columns
                        if (td[j]) {
                            txtValue = td[j].textContent || td[j].innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                rowMatch = true;
                                break; // No need to check further if a match is found
                            }
                        }
                    }

                    tr[i].style.display = rowMatch ? "" : "none";
                }
            }
        </script>

<script>
  $(document).ready(function () {
    // Ensure click event works
    $(document).on("click", "#myTable tbody tr", function () {
        var documentId = $(this).data("id");
        if (documentId) {
            window.location.href = "/document/" + documentId;
        }
    });
});
</script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"></script>


</body>
</html>
