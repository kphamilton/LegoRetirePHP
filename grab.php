<?php
/* Copyright Kevin Hamilton 2012 */


require_once('config.php');

$retired = 0;
$total = 0;

echo "START TIME IS: " . date("c");

$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD, array(
    PDO::ATTR_PERSISTENT => true
));

$query = "SELECT lego_id, name, year  
            FROM lego 
            WHERE retired = False 
            ORDER BY lego_id";
//Grab all current Lego sets that aren't retired (retired = false).


foreach ($dbh->query($query) as $row) {
    $lego_id = $row['lego_id'];
    $set_name = $row['name'];
	$year = $row['year'];
	$us_price = $row['us_price'];
    $url = "http://shop.lego.com/en-US/Diagon-Alley-$lego_id?p=$lego_id";
	echo "Checking $url\n";
    /* Lego's servers will automatically redirect you to the appropriate page using the above url format.
     * It will either send you to the appropriate page (changing "Diagon-Alley" to the right name), or
     * it will give send you to the lego shop homepage and give you a 404 error.
     */

    $total++;
    //keeping track of how many lego sets were checked
    
    $file_headers = @get_headers($url);
    
    
    if ($file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[11] == "HTTP/1.1 404 Not Found") {
        $exists = false;
    } 
    //if there is a 404 status code in the headers in position 0 or position 11 of the headers array, it isn't available.
    
    else {
        $exists = true;
    }

    if (!$exists) {
        echo "SET ABOVE ($set_name - $lego_id) RETIRED";
		echo "Set name is: $set_name ($year) - $lego_id with price $$us_price is retired now. Get it.";
        //terminal output to notify of retired set if you are actually watching the terminal
        
        $retired++;
        //Keep track of how many new retirees have occurred this time around.
        
        //$dbh2 = new PDO("mysql:host=".DB_HOST.";dbname=". DB_NAME, DB_USER, DB_PASSWORD);
        $query = "UPDATE lego set retired = True where lego_id = :lego_id";
		$stmt2 = $dbh->prepare($query);
		$stmt2->bindValue(':lego_id', $lego_id);
        $stmt2->execute();
        //Update this set to be retired in your database

        mail(EMAIL_ADDRESS."; ".TEXT_ADDRESS, "", "Lego set $lego_id ($year) - $set_name is not available on the LEGO Shop anymore. Get it while it's good.");
    }
}
$dbh = null;
//Close connection

echo "TOTAL RETIRED: $retired";
echo "TOTAL CHECKED: $total";
echo "END TIME IS: " . date("c");
//Output some information regarding how many were retired this time around and how many were checked.
?>