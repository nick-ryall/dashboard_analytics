<?php

session_start();
$_SESSION = array();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="nl">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <title>Google Analytics PHP API example - SWIS BV</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="language" content="nl">
        <style type="text/css">
            body{font: 12px Verdana,Arial, Helvetica, sans-serif;color:#333;}
            h1,h2{color:#516082;font-weight:bold}
            h1{font-size: 16px;}
            h2{font-size:14px;}
            form{overflow:hidden;border:1px solid #ccc;padding:10px;width:300px;}
            label{display:block;float:left;clear:left;width: 100px;margin: 3px;}
            input{display:block;float:left;width:200px;margin: 3px;}
            #submit{clear:left;width:100px;margin: 5px 0 0 106px;}
        </style>
    </head>
    <body>
    
        <h1>Google Analytics PHP API class</h1>
        
        <p>This class provides methods for retrieving data from the Google Analytics API.</p>
        
        <p>It provides a number of basic methods to directly get a PHP array with visitors, pageviews, referrers, etc.<br>
        You can also use the method <strong>getData()</strong> to use any number of metrics and dimensions to get an array with the results.</p>
        
        <p>It provides basic caching stored in a session for fast responses.</p>
        <p>A basic example is provided in the demo below and in the download.</p>
    
        <h2>Demo</h2>
        <p>Use your Google Analytics account credentials to see a demo.</p>
        <form method="post" action="analytics_data.php">
            <label for="username">E-mail</label><input type="text" name="username" id="username">
            <label for="password">Password</label><input type="password" name="password" id="password">
            <input type="submit" id="submit" value="Log in">
        </form>
        <p>Your login credentials are not stored anywhere but directly send to Google.</p>
        
        <h2>Download</h2>
        <a href="ga.zip">Download Google Analytics API class (demo script provided in download)</a>
        
        <h2>Usage</h2>
        
<div class="php" style="padding:5px;white-space: pre;border: 1px dotted #a0a0a0; font-family: 'Courier New', Courier, monospace; font-size: 110%; background-color: #f0f0f0;line-height: 110%;color: #0000bb;">
<?php

highlight_file('example.php');

?>
</div>        
        
<h2>Source</h2>
<div class="php" style="padding:5px;white-space: pre;border: 1px dotted #a0a0a0; font-family: 'Courier New', Courier, monospace; font-size: 110%; background-color: #f0f0f0;line-height: 110%;color: #0000bb;">
<?php

highlight_file('analytics.class.php');

?>
</div>        

    </body>
</html>