<!DOCTYPE HTML>
<html lang="en">
    <head>
	<meta charset="UTF-8">
	<title>Top Books widget</title>
    <link href="style.css" rel="stylesheet">

    <!-- Important Owl stylesheet -->
    <link rel="stylesheet" href="owl-carousel/owl.carousel.css">
     
    <!-- Default Theme -->
    <link rel="stylesheet" href="owl-carousel/owl.theme.css">
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
     
    <!-- Include js plugin -->
    <script src="assets/owl-carousel/owl.carousel.js"></script>
    </head>
	<body>
<div id="topbooks-widget">
<?php
// Set API Report path
$path = '%2Fshared%2FPortland%20Community%20College%2FReports%2FTopBooks';

// Set API Key (configured in Developer Network)
$apikey = 'l7xxb19cb21eddcb4fd5a0805a145a279abb';

// Set upper limit of records
$limit = 25; // Must be between 25 and 1000, in multiples of 25

// Construct report URL
$report = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports?path='.$path.'&apikey='.$apikey.'&limit='.$limit;

// Parse XML
function get_items($report) {
  if ($xml = simplexml_load_file($report)) {
    $xml->registerXPathNamespace('rowset', 'urn:schemas-microsoft-com:xml-analysis:rowset');
    return $xml->xpath('/report/QueryResult/ResultXml/rowset:rowset/rowset:Row');
  }
  else {
    return false;
  }
}

function print_items($result) {
  $prev_mmsids = array();
  $to_print = '<div class="owl-carousel">';
  
  foreach ($result as $item) {
    // Column3 = MMS ID
    $mmsid = (string) $item->Column3;
    
    if (!in_array($mmsid, $prev_mmsids)) {
      // Track MMS ID to prevent duplicates
      $prev_mmsids[] = $mmsid;
      
      // Column1 = Author
      $author = (string) $item->Column1;       
      
      // Column2 = ISBNs
      $isbns = (string) $item->Column2;
      $isbn = substr($isbns, 0, strpos($isbns, ';'));
	  
	  // Column4 = Subjects
	  $subjects = (string) $item->Column4;
      
      // Column5 = Title
      $title = (string) $item->Column5;
      // removes " /" from ends of the titles
      $trimmed_title = rtrim($title, " /");
   
      $to_print .= 
            '<div class="topbooks-inner"> 
                  <div class="topbooks-result" data-isbn="'.$isbn.'">
                  <div class="topbooks-img-container">
                    <img src="http://books.google.com/books?vid=ISBN'.$isbn.'&amp;printsec=frontcover&amp;img=1&amp;zoom=1" alt="'.$trimmed_title.'" width="128" />
                  </div>
                      <div class="topbooks-info">
                        <p>
                          <span class="topbooks-title">
                              <a href="http://primo.pcc.edu/'.$mmsid.'">'.$trimmed_title.'</a>
                          </span>
                          -
                          <span class="topbooks-author">'.$author.'</span>
                        </p>
                          <div class="topbooks-summary">'.$subjects.'</div>
                      </div>
                  </div>
              </div>';
      }
	  
    }
  
    $to_print .= '</div>';
  return mb_convert_encoding($to_print, "UTF-8");
}

// Get items
if (!($result = get_items($report))) {
  // If failed, try one more time
  // For some reason, ExL returns an empty ResultXML node the first time a filter is tried, but it works the second time
  $result = get_items($report);
}
if ($result) {
  // Print list
  // echo $report;
  echo print_items($result);
}
else {
   //Give up
   // echo $report;
  echo '<p>No results.</p>';
}
?>
            <script type="text/javascript" src="ajaxgooglebooks.js"></script>
</div>
	</body>
</html>
