<?php

function is_https() {
    // Detect if we are running HTTPS or proxied HTTPS
    if (server('HTTPS') == 'on') {
        // Web server is running native HTTPS
        return true;
    } elseif (server('HTTP_X_FORWARDED_PROTO') == "https") {
        // Web server is running behind a proxy which is running HTTPS
        return true;
    } elseif (request_header('HTTP_X_FORWARDED_PROTO') == "https") {
        return true;
    }
    return false;
}

function get_application_path($manual_domain=false)
{
    if (is_https()) {
        $proto = "https";
    } else {
        $proto = "http";
    }
    
    if ($manual_domain) {
        return "$proto://".$manual_domain."/";
    }

    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $path = dirname("$proto://" . server('HTTP_X_FORWARDED_HOST') . server('SCRIPT_NAME')) . "/";
    } else {
        $path = dirname("$proto://" . server('HTTP_HOST') . server('SCRIPT_NAME')) . "/";
    }

    return $path;
}

function request_header($index)
{
   $val = null;
   $headers = apache_request_headers();
   if (isset($headers[$index])) {
        $val = $headers[$index];
  }
  return $val;
}


function server($index)
{
    $val = null;
    if (isset($_SERVER[$index])) {
        $val = $_SERVER[$index];
    }
    return $val;
}

function get($index,$error_if_missing=false,$default=null)
{
    $val = $default;
    if (isset($_GET[$index])) {
        $val = rawurldecode($_GET[$index]);
    } else if ($error_if_missing) {
        header('Content-Type: text/plain');
        die("missing $index parameter");
    }
    if(!is_null($val)){
    $val = stripslashes($val);
	}
    return $val;
}

function post($index,$error_if_missing=false,$default=null)
{
    $val = $default;
    if (isset($_POST[$index])) {
        // PHP automatically converts POST names with brackets `field[]` to type array
        if (!is_array($_POST[$index])) {
            $val = rawurldecode($_POST[$index]); // does not decode the plus symbol into spaces
        } else {
            // sanitize the array values
            $SANTIZED_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (!empty($SANTIZED_POST[$index])) {
                $val = $SANTIZED_POST[$index];
            }
        }
    } else if ($error_if_missing) {
        header('Content-Type: text/plain');
        die("missing $index parameter");
    }
    
    if (is_array($val)) {
        $val = array_map("stripslashes", $val);
    }	
	if(!is_null($val)){
        $val = stripslashes($val);
	}
    return $val;
}

function view($filepath, array $args = array())
{
    global $path;
    $args['path'] = $path;
    $content = '';
    if (file_exists($filepath)) {
        extract($args);
        ob_start();
        include "$filepath";
        $content = ob_get_clean();
    }
    return $content;
}