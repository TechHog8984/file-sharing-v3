<?php
$req_uri = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];
$servername = $_SERVER["SERVER_NAME"];
$serverport = $_SERVER["SERVER_PORT"];

class route {
  private static $routes = array();

  public static function add($uri, $func, $method) {
    self::$routes[$uri] = array('uri' => $uri, 'func' => $func, 'method' => $method);
  }
  public static function list() {
    print_r(self::$routes);
  }
  public static function run() {
    $routefound = false;
    foreach(self::$routes as $uri => $a) {
      $real_uri = $_SERVER["REQUEST_URI"];
      $real_method = $_SERVER["REQUEST_METHOD"];

      $uri = $a['uri'];
      $func = $a['func'];
      $method = $a['method'];
      //print(get_func_args());
      if ($uri != '/' and str_starts_with($real_uri, $uri)) {
        $routefound = true;
        if ($real_method == $method) {
          $sub = substr($real_uri, strlen($uri), strlen($real_uri));
          $args = explode('/', $sub);
          unset($args[0]);
          call_user_func_array($func, $args);
        } else {
          echo sprintf(file_get_contents('error.html'), "<p>[$uri] $real_method is not a valid method, try $method</p>");
        }
      }
    }
    if (!$routefound) {
      if (self::$routes['/']) {
        $real_uri = $_SERVER['REQUEST_URI'];
        $real_method = $_SERVER['REQUEST_METHOD'];

        $uri = self::$routes['/']['uri'];
        $func = self::$routes['/']['func'];
        $method = self::$routes['/']['method'];

        if ($real_method == $method) {
          $sub = substr($real_uri, strlen($uri), strlen($real_uri));
          $args = explode('/', $sub);
          unset($args[0]);
          call_user_func_array($func, $args);
        } else {
          echo sprintf(file_get_contents('error.html'), "<p>[$uri] $real_method is not a valid method, try $method</p>");
        }
      } else {
        echo sprintf(file_get_contents('error.html'), "<p>route not found.</p>");
      }
    }
  }
}
$r = new route;

$r::add('/', function(){
  echo file_get_contents('home.html');
}, 'GET');
$r::add('/upload', function(){
  foreach($_FILES as $file => $data) {
    if (move_uploaded_file($data['tmp_name'], 'files/' . $data['name'])) {
      echo 'success';
    } else {
      echo 'fail';
    }
  }
}, 'POST');

$r::run();
?>
