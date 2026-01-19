<?php

// Test if Laravel is loading
if (function_exists('app')) {
    echo "Laravel is working!";
    echo "<br>";
    echo "App URL: " . config('app.url');
    echo "<br>";
    echo "Environment: " . app()->environment();
} else {
    echo "Laravel is NOT loaded properly";
    echo "<br>";
    echo "This means the request is not going through Laravel's bootstrap process";
}

?>
