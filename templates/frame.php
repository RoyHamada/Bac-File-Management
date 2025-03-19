<?php 
function getContent() {
    // This function will be overridden in each page
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
 
    <title><?php echo $pageTitle ?? 'Document Management System'; ?></title>
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

              <main>
    <?php getContent(); // This will load page-specific content ?>
</main>
</body>
</html>