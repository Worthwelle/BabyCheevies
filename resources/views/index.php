<!doctype html>
<html lang="en" ng-app="BabyCheevies">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo config('app.name') ?></title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.7/angular.js" crossorigin="anonymous"></script>
        <!--<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.7/angular.min.js" integrity="sha384-R6kAKgTgRiD5889XyzYD/aMryNA4Yr9EBnt6rIXuukLgVONifQDnHNaadrSNakQl" crossorigin="anonymous"></script>-->
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.7/angular-route.min.js" integrity="sha384-memfhCr3qU++lyUeAXqU4MzCrirdhXFyP6Fawut37YHvTSzCfZMjt4iXJ+ry+IZc" crossorigin="anonymous"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.7/angular-sanitize.min.js" integrity="sha384-KDyf7BSfs2TyNIlTqO/JYe5a7xO5C0DBp+I0sz9MFlkserxNnVzHst6S/ShCr1vG" crossorigin="anonymous"></script>
        
        <script src="https://cdn.jsdelivr.net/satellizer/0.15.5/satellizer.min.js" integrity="sha384-E8B5PyljpgEWn63mWF7QGzQXnqttR3juWUolbe9M1YcykRRUbBnOVhA8SOE6Lr5O" crossorigin="anonymous"></script>
        
        <script src="<?php echo config('app.url') ?>/js/cheevies.js"></script>
        <script src="js/services/authService.js"></script>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body ng-controller="mainController">
        <!-- MAIN CONTENT AND INJECTED VIEWS -->
        <div class="container">

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Static navbar -->
            <nav class="navbar navbar-default">
              <div class="container-fluid">
                <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="#">{{ appName }}</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                  <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                  </ul>
                  <ul class="nav navbar-nav navbar-right" ng-switch="loggedin">
                    <li class="dropdown" ng-switch-when="false">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">John Doe <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li class="dropdown-header">No Doe</li>
                        <li><a href="#">Edit Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Logout</a></li>
                      </ul>
                    </li>
                    <li class="dropdown" ng-switch-when="true">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">John Doe <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        <li class="dropdown-header">John Doe</li>
                        <li><a href="#">Edit Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Logout</a></li>
                      </ul>
                    </li>
                  </ul>
                </div><!--/.nav-collapse -->
              </div><!--/.container-fluid -->
            </nav>

            <div ng-view></div>
        <!-- angular templating -->
        <!-- this is where content will be injected -->
    </body>
    
</html>
