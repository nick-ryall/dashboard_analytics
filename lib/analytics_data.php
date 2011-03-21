<?php

// use session to store credentials and auth hash
session_start();

require 'analytics.class.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['password'])){

    // set username & password
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['password'] = $_POST['password'];

    header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));    
    exit;
}

if (isset($_SESSION['username'], $_SESSION['password'])){
    
    // construct the class
    $oAnalytics = new analytics($_SESSION['username'], $_SESSION['password']);
    
    // get an array with profiles (profileId => profileName)
    $aProfiles = $oAnalytics->getProfileList();  
      
    $aProfileKeys = array_keys($aProfiles);
    // set the profile tot the first account
    $oAnalytics->setProfileById($aProfileKeys[0]);
    $iSelectedMonth = date('n');
}

if (isset($_POST['profileId'])){
    // change profileId
    $oAnalytics->setProfileById($_POST['profileId']);     
}
if (isset($_POST['month'])){
    // change month
    $iSelectedMonth = $_POST['month'];     
}
// set the month
$oAnalytics->setMonth($iSelectedMonth, date('Y')); 

// alternativly set a date range:
// $oAnalytics->setDateRange('YYYY-MM-DD', 'YYYY-MM-DD');

/**
* Basic html table for displaying graphs
* 
* @param array $aData
*/
function graph($aData){
    
    $iMax = max($aData);
    if ($iMax == 0){
        echo 'No data';
        return;
    }
    echo '<table>
            <tr>
                <td>Metric</td>
                <td>#</td>
                <td>Graph</td>
            </tr>';
    foreach($aData as $sKey => $sValue){
        echo '  <tr>
                    <td>' . $sKey . '</td>
                    <td>' . $sValue . '</td>
                    <td><div class="bar" style="width: ' . intval(($sValue / $iMax) * 300) . 'px;"></div> 
                </tr>';
    }
    echo '</table>';
}


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
            body{font: 11px Arial, Helvetica, sans-serif}
            form{overflow:hidden}
            table{border:0;border-collapse:collapse;width:600px;}
            td{padding:5px;border-right:1px solid #ccc;}
            .bar{height:10px;background:#f00;}
        </style>
    </head>
    <body>
        <form method="post" action="analytics_data.php">
            <label for="profileId">Profile</label>
            <select id="profileId" name="profileId">
            <?php
            foreach ($aProfiles as $sKey => $sValue){
                echo '<option value="' . $sKey . '">' . $sValue . '</option>';
            }
            ?>
            </select>
            <label for="month">Month</label>
            <select name="month" id="month">
            <?php
            $aMonth = range(1, date('n'));
            foreach($aMonth as $iMonth){
                echo '<option ' . ($iMonth == $iSelectedMonth ? 'selected="selected" ' : '') . 'value="' . $iMonth . '">' . date('F', mktime(0, 0, 0, $iMonth, 1, date('Y'))) . '</option>'; 
            }
            ?>
            </select>
            <input type="submit" id="submit" value="Submit">
            <a href="./">Log out</a>
        </form>
        
        <h2>Visitors:</h2>
        <?php graph($oAnalytics->getVisitors()); ?>
        
        <h2>Pageviews:</h2>
        <?php graph($oAnalytics->getPageviews()); ?>
        
        <h2>Visits per Hour:</h2>
        <?php graph($oAnalytics->getVisitsPerHour()); ?>
        
        <h2>Browsers:</h2>
        <?php graph($oAnalytics->getBrowsers()); ?>
        
        <h2>Referrers:</h2>
        <?php graph($oAnalytics->getReferrers()); ?>
        
        <h2>Search words:</h2>
        <?php graph($oAnalytics->getSearchWords()); ?>
        
        <h2>Screen resolution:</h2>
        <?php graph($oAnalytics->getScreenResolution()); ?>
    </body>
</html>