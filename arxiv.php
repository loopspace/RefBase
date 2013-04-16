<?php
// Project:    Web Reference Database (refbase) <http://www.refbase.net>
// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
//             original author(s).
//
//             This code is distributed in the hope that it will be useful,
//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
//             License for more details.
//
// File:       ./import.php
// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/import.php $
// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
//
// Created:    17-Feb-06, 20:57
// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
//             $Author: msteffens $
//             $Revision: 1214 $

// Import form that offers to import records from Reference Manager (RIS), CSA Illumina,
// RefWorks Tagged Format, SciFinder Tagged Format, ISI Web of Science, PubMed MEDLINE, PubMed XML, MODS XML,
// Endnote Tagged Text, BibTeX or COPAC. Import of the latter five formats is provided via use of bibutils.


// Incorporate some include files:
include 'includes/header.inc.php'; // include header
include 'includes/footer.inc.php'; // include footer
include 'includes/include.inc.php'; // include common functions
include 'initialize/ini.inc.php'; // include common variables

include_once('includes/classes/org/simplepie/simplepie.inc');

// --------------------------------------------------------------------

// START A SESSION:
// call the 'start_session()' function (from 'include.inc.php') which will also read out available session variables:
start_session(true);

// --------------------------------------------------------------------

// Initialize preferred display language:
// (note that 'locales.inc.php' has to be included *after* the call to the 'start_session()' function)
include 'includes/locales.inc.php'; // include the locales

// --------------------------------------------------------------------

// Extract session variables:
if (isset($_SESSION['errors']))
  {
    $errors = $_SESSION['errors']; // read session variable (only necessary if register globals is OFF!)

    // Note: though we clear the session variable, the current error message is still available to this script via '$errors':
    deleteSessionVariable("errors"); // function 'deleteSessionVariable()' is defined in 'include.inc.php'
  }
else
  $errors = array(); // initialize the '$errors' variable in order to prevent 'Undefined variable...' messages

foreach($_REQUEST as $varname => $value)
  {
    $formVars[$varname] = stripSlashesIfMagicQuotes($value); // function 'stripSlashesIfMagicQuotes()' is defined in 'include.inc.php'
  }

// --------------------------------------------------------------------

// Initialize preferred display language:
// (note that 'locales.inc.php' has to be included *after* the call to the 'start_session()' function)
include 'includes/locales.inc.php'; // include the locales

// --------------------------------------------------------------------

// If there's no stored message available:
if (!isset($_SESSION['HeaderString']))
  {
    if (empty($errors)) // provide one of the default messages:
      {
	$HeaderString = ""; // Provide the default message
      }
    else // -> there were errors validating the user's data input
      $HeaderString = "<b><span class=\"warning\">There were validation errors regarding the data you entered:</span></b><ul><li>" . implode('</li><li>', $errors) . '</li></ul>';
  }
else // there is already a stored message available
  {
    $HeaderString = $_SESSION['HeaderString']; // extract 'HeaderString' session variable (only necessary if register globals is OFF!)
    
    // Note: though we clear the session variable, the current message is still available to this script via '$HeaderString':
    deleteSessionVariable("HeaderString"); // function 'deleteSessionVariable()' is defined in 'include.inc.php'
  }

// Adopt the page title & some labels according to the user's permissions:
if (isset($_SESSION['user_permissions']) AND !ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does NOT contain 'allow_batch_import'...
  {
    $pageTitle = " -- Import new records from the arXiv"; // adopt page title
    $textEntryFormLabel = "Record"; // adopt the label for the text entry form
    $rowSpan = ""; // adopt table row span parameter
  }
else
  {
    $pageTitle = " -- New records from the arXiv";
    $textEntryFormLabel = "Records";
    $rowSpan = " rowspan=\"2\"";
  }

// Extract the view type requested by the user (either 'Mobile', 'Print', 'Web' or ''):
// ('' will produce the default 'Web' output style)
if (isset($_REQUEST['viewType']))
  $viewType = $_REQUEST['viewType'];
else
  $viewType = "";

// Show the login status:
showLogin(); // (function 'showLogin()' is defined in 'include.inc.php')

if (isset($formVars['submit']))
  {
    $keywords = array();
    // Loop through twice to make sure we know the ids before we assign the keywords, just in case the user_agent mucks up the order
    foreach ($formVars as $varname => $value)
      {
	if (preg_match('/^arXivImport/',$varname))
	  {
	    $ids[preg_replace('/^arXivImport/','',$varname)] = $value;
	  }
      }

    foreach ($formVars as $varname => $value)
      {
	if (preg_match('/Keywords$/',$varname) and $value)
	  {
	    $id = preg_replace('/Keywords$/','',$varname);
	    if (isset($ids[$id]))
	      $keywords[] = $ids[$id] . " " . implode("; $ids[$id] ",explode(";",$value));
	  }
      }

    $postData = array(
		      'formType' => 'importID',
		      'submit '=> 'Import',
		      'sourceIDs' => implode(" ", $ids),
		      'showSource' => '1'
		      );

    if (count($keywords) > 0) 
      $postData['Keywords'] = implode(";", $keywords);


    saveSessionVariable("formVars", $postData);
    header("Location: import_modify.php");

  }
else
  {

// (2a) Display header:
// call the 'displayHTMLhead()' and 'showPageHeader()' functions (which are defined in 'header.inc.php'):
displayHTMLhead(encodeHTML($officialDatabaseName) . $pageTitle, "index,follow", "Import records into the " . encodeHTML($officialDatabaseName), "", false, "", $viewType, array());
showPageHeader($HeaderString);

$arxivNamespace = 'http://arxiv.org/schemas/atom';

// Didn't seem to work when embedded into an extension of the SimplePie class

// this is for sorting the 'new submissions' for the current day
function arxiv_sort($a,$b)
  {
    $status[''] = 0;
    $status['CROSS LISTED'] = 1;
    $status['UPDATED'] = 2;

    $astatus = preg_replace('#.*\]\s*([A-Z ]*)\s*\)$#','$1',$a->get_title());
    $bstatus = preg_replace('#.*\]\s*([A-Z ]*)\s*\)$#','$1',$b->get_title());

    if ($astatus != $bstatus) {
      return $status[$astatus] >= $status[$bstatus];
    } else {
      $aid = preg_replace('#^http://arxiv.org/abs/#','',$a->get_link());
      $bid = preg_replace('#^http://arxiv.org/abs/#','',$b->get_link());

      return $aid >= $bid;
    }

  }

// this is for sorting the output of the API
function arxiv_sort_date($a,$b)
{
  global $arxivNamespace;

  $pacat = $a->get_item_tags($arxivNamespace, 'primary_category');
  $pbcat = $b->get_item_tags($arxivNamespace, 'primary_category');

  $acat = $pacat[0]["attribs"][""]["term"];
  $bcat = $pbcat[0]["attribs"][""]["term"];

  // if exactly _one_ of acat and bcat is math, that wins
  if ((strpos($acat,"math") === 0) and (strpos($bcat,"math") !== 0))
    {
      // $a preceeds $b
      return -1;
    }
  elseif  ((strpos($acat,"math") !== 0) and (strpos($bcat,"math") === 0))
    {
      // $b preceeds $a
      return 1;
    }
  else
    {
      // either both are math or both are not math
      // order by arxiv ID
      // (note: at the moment, on earlier IDs this will not be quite right)
      $aid = preg_replace('#^http://arxiv.org/abs/#','',$a->get_link());
      $bid = preg_replace('#^http://arxiv.org/abs/#','',$b->get_link());
      
      return $aid >= $bid;
    }
}


// defaults:
$catchup = 0;
$url = "http://arxiv.org/rss/math?version=2.0";
$sort_method = 'arxiv_sort';
$arxiv_tz = new DateTimeZone('America/New_York');
$gmt = new DateTimeZone('GMT');

if (isset($formVars['date']))
  {
    // date was specified, try to parse it.
    $date = new DateTime($formVars['date']);
    // submission times are determined by local time
    $date->setTimezone($arxiv_tz);
    $date->setTime(16,00,00);
    // but api responses are determined by GMT/UTC
    $date->setTimezone($gmt);
    if ($date)
      {
	// try to work out what the catch-up link would give
	// that day's announcement is the previous day's submissions
	// with the modification that weekends are subsummed into monday
	$day = $date->format('w');
	if ($day === "0")
	  $date->modify("+1 day");
	if ($day === "6")
	  $date->modify("+2 days");
	// $date now points to a weekday no earlier than the given date
	$enddate = clone $date;
	$enddate->modify("-1 day");
	$endday = $enddate->format('w');
	if ($endday === "0")
	  $enddate->modify("-2 days");
	if ($endday === "6")
	  $enddate->modify("-1 day");
	// $enddate now points to the previous weekday
	$startdate = clone $enddate;
	$startdate->modify("-1 day");
	$startday = $startdate->format('w');
	if ($startday === "0")
	  $startdate->modify("-2 days");
	if ($startday === "6")
	  $startdate->modify("-1 day");
	// $startdate now points to the weekday before $enddate

	$arxivTitle = "Catch-up for arXiv Submissions";
	$sort_method = 'arxiv_sort_date';
	$arxivDate = $date->format('D, d M Y')
	  . ' (Submitted between '
	  . $startdate->format('D, d M Y Hi (T)')
	  . " and "
	  . $enddate->format('D, d M Y Hi (T)') 
	  . ')';

	$catchup = 1;

	$url = "http://export.arxiv.org/api/query?search_query=submittedDate:["
	  . $startdate->format('YmdHi')
	  . "+TO+"
	  . $enddate->format('YmdHi') 
	  . "]+AND+cat:math.*";
      }

  }
else
  {
    $arxivTitle = "math updates on arXiv.org";
    $date = new DateTime();
    $arxivDate = "Published on:" . $date->format('D, d M Y');
  }

$now = new DateTime();
$now->setTimezone($arxiv_tz);
$now->setTime(16,00,00);
$nowsec = $now->format('U');

// which dates to offer?
$date_options = array(
		      "-1 month",
		      "-2 weeks",
		      "-1 week",
		      "-2 days",
		      "-1 day",
		      "+1 day",
		      "+2 days",
		      "+1 week",
		      "+2 weeks",
		      "+1 month",
		      );

$date_opts_count = count($date_options);

$date_form = '<p><form action="'
  . $_SERVER['PHP_SELF']
  . '" method="post">'
  . '<input type="submit" name="dateSubmit" value="Skip to:" />'
  . '<select name="date">';

// comparing dates is not yet available in this version of php
$datesec = $date->format('U');

for ($i = 0; $i < $date_opts_count; ++$i)
  {
    $thisdate = clone $date;
    $thisdate->modify($date_options[$i]);
    $thisday = $thisdate->format('w');
    $thissec = $thisdate->format('U');
    if ($thisday === "0")
      {
	if ($thissec > $datesec)
	  {
	    $thisdate->modify("+1 day");
	  }
	else
	  {
	    $thisdate->modify("-2 days");
	  }
      }
    if ($thisday === "6")
      {
	if ($thissec > $datesec)
	  {
	    $thisdate->modify("+2 day");
	  }
	else
	  {
	    $thisdate->modify("-1 days");
	  }
      }

    if ($thisdate->format('U') <= $nowsec)
      {
	$date_form .= '<option value="'
	  . $thisdate->format('c')
	  . '">'
	  . $thisdate->format('D, d M Y')
	  . ' (c. '
	  . $date_options[$i]
	  . ')'
	  . '</option>'
	  . "\n";
      }
  }


$date_form .= '</select></form>'
  . "\n Or "
  . '<form action="'
  . $_SERVER['PHP_SELF']
  . '" method="post">'
  . '<input type="submit" name="dateSubmit" value="Select date:" /><input type="text" name="date" title="Enter date to skip to (in a reasonable format)" size="30" /></form></p>';

$tomorrow = clone $date;
$tomorrow->modify("+1 day");
$tomorrowday = $tomorrow->format('w');
if ($tomorrowday === "0")
  $tomorrow->modify("+1 day");
if ($tomorrowday === "6")
  $tomorrow->modify("+2 days");

$next_form = "";
if ($tomorrow->format('U') <= $nowsec)
  {
    $next_form = '<form action="'
      . $_SERVER['PHP_SELF']
      . '" method="post">'
      . '<button type="submit" name="date" value="'
      . $tomorrow->format('c')
      . '">Tomorrow (ish)</button></form>';
  }

$arxivArray = array();

$maxresults = 40;
$totalresults = 10; // actually, $totalresult * $maxresults


print '<script type="text/javascript">
//<![CDATA[
var importButtons = [];

function checkChecked() {
form = document.getElementById("arxivForm");
if (importButtons.length == 0)
{
for (var i = 0; i < form.elements.length; i++)
{
if (form.elements[i].type == "submit" && form.elements[i].value == "Import")
{
importButtons.push(form.elements[i]);
}
}
}
if (isOneChecked()) {
for (var i = 0; i < importButtons.length; i++)
{
importButtons[i].disabled = false;
}
} else {
for (var i = 0; i < importButtons.length; i++)
{
importButtons[i].disabled = true;
}
}
}

function isOneChecked() {
form = document.getElementById("arxivForm");
for (var i = 0; i < form.elements.length; i++)
{
if (form.elements[i].checked == true)
{
return true;
}
}
return false;
}
//]]>
</script>';

print "<div id=\"arxiv\">\n";

print "<h2>$arxivTitle</h2>\n";
print "Published on: <i>$arxivDate</i>\n";

print $date_form;

print "<dl>\n";

if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
{
  print "<form id=\"arxivForm\" action=\"arxiv.php\" method=\"POST\">\n"
  . "<input type=\"submit\" name=\"submit\" value=\"Import\" title=\"Press this button to import the selected records into the database.\" disabled=\"true\">\n";
  if (isset($formVars['SelectAll']))
    {
      print "<input type=\"submit\" name=\"DeSelectAll\" value=\"Deselect All\" title=\"Press this button to deselect all records.\">\n";
    }
  else
    {
      print "<input type=\"submit\" name=\"SelectAll\" value=\"Select All\" title=\"Press this button to select all records for importing.\">\n";
    }
}


print "<h3>New submissions</h3>"; 
$cross =0;

$urls = array();
if ($catchup)
  {
    for ($i = 0; $i < $totalresults; $i++)
      {
	array_push($urls, $url . "&start=" . ($i*$maxresults) . "&max_results=" . $maxresults);
      }
  }
else
  {
    array_push($urls,$url);
  }

foreach ($urls as $urltoget)
  {

	$arxivfeed = new SimplePie();
	$arxivfeed->set_feed_url($urltoget);
	$arxivfeed->set_input_encoding('UTF-8');
	$arxivfeed->set_output_encoding('ISO-8859-1');
	$arxivfeed->enable_cache(true);
// Cache is located in the same directory as this file
	$arxivfeed->set_cache_location('/home/www/stacey/RefBase/Cache');
	$arxivfeed->set_cache_duration(43200); // 12 hours
	$arxivfeed->enable_order_by_date(false);
	$arxivfeed->init();
	$arxivArray = $arxivfeed->get_items();

usort($arxivArray, $sort_method);
$arxivCount = count($arxivArray);

if (!arxivCount)
  break;


for($i = 0; $i < $arxivCount; $i++)
  {
    $article=$arxivArray[$i];
    $link=$article->get_link();
    $id = preg_replace('#^http://arxiv.org/abs/#','',$link);

    if ($catchup)
      {
	$ismath = 0;
	foreach ($article->get_categories() as $cat)
	  {
	    if (strpos($cat->get_term(),"math") === 0)
	      {
		$ismath = 1;
		break;
	      }
	  }
	if (!$ismath)
	  continue;
	$num++;
	$status = "";
	$title = $article->get_title();
	$primcat = $article->get_item_tags($arxivNamespace, 'primary_category');
	$subject = $primcat[0]["attribs"][""]["term"];
	if (strpos($subject,"math") !== 0)
	  $status = "CROSS LISTED";
	$abstract = '<p>' . $article->get_description() . '</p>';
	$authors = "";
	foreach ($article->get_authors() as $author)
	  {
	    if ($authors)
	      $authors .= " and ";
	    $authors .= $author->get_name();
	  }
	$id = preg_replace('/v\d+$/','',$id);
      }
    else
      {
	$longtitle =$article->get_title();
	$description=$article->get_description();

	$title = preg_replace('#\s*\([^\)]*\)$#','',$longtitle);
	$subject = preg_replace('#.*\[(.*)\]\s*[A-Z ]*\s*\)$#','$1',$longtitle);
	$status = preg_replace('#.*\]\s*([A-Z ]*)\s*\)$#','$1',$longtitle);
	$desc = explode('</p>',$description,2);
	$authors = preg_replace('#<p>Authors:\s*#','',$desc[0]);
	$abstract = $desc[1];
	$num = $i + 1;
      }

    if ($status == "UPDATED") 
      {
	$updates[] = $id;
	continue;
      }

    if ($status == "CROSS LISTED")
      {
	if ($catchup)
	  {
	    $title .= " <span class=\"arxivCross\">(cross-listed)</span>";
	  }
	else
	  {
	    if (!$cross)
	      {
		print '<h3>Cross-Lists</h3>';
		$cross=1;
	      }
	  }
      }

    print "<dt>";

    if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
      {
	print "<input type=\"checkbox\" name=\"arXivImport"
	  . $id
	  . "\" value=\""
	  . $id
	  . "\" title=\"Click to select "
	  . $id
	  . " for importing\"";
	if (isset($formVars['SelectAll']))
	  print "checked";
	print " onclick=\"checkChecked();\">";
      }

    print "["
      . $num
      . "]"
      . "&nbsp;<span class=\"list-identifier\"><a href=\""
      . $link
      . "\" title=\"Abstract\">"
      . $id
      . "</a> [<a href=\"http://arxiv.org/ps/"
      . $id
      . "\" title=\"Download PostScript\">ps</a>, <a href=\"http://arxiv.org/pdf/"
      . $id 
      . "\" title=\"Download PDF\">pdf</a>, <a href=\"http://arxiv.org/format/"
      . $id
      . "\" title=\"Other formats\">other</a>]</span>\n";
  
    print "</dt>\n";

    print '<dd>
<div class="meta clickDown" tabindex="0">
<div class="list-title">
<span class="descriptor">Title:</span>';
    
    print $title;

    print '</div>
<div class="list-authors">
<span class="descriptor">Authors: </span>';

    print $authors;

    print '</div>';
    print '<div class="list-subjects">
<span class="descriptor">Primary Subject: </span>';

    print $subject;

    print '</div>';
    print '<div class="list-abstract hide">';

    print $abstract;

    print '</div>';

    if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
      print "Keywords:&nbsp;"
	. "<input type=\"text\" name=\""
	. $id
	. "Keywords\" title=\"Keywords to apply to "
	. $id
	. " (delimited by semi-colons)\" size=\"30\">\n";


    print '</div>
</dd>';

  }
$arxivfeed->__destruct();
unset($arxivArray);
unset($arxivfeed);
sleep(3);
  }



if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
  print "<input type=\"submit\" name=\"submit\" value=\"Import\" title=\"Press this button to import the selected records into the database.\" disabled=\"true\">\n</form>";


if (!$catchup)
  {
    print '<h3>Updates</h3>';

    if ($updates)
      {
	connectToMySQLDatabase();

	$query = "SELECT summary_language,title,serial FROM $tableRefs WHERE summary_language RLIKE \"" . implode("\" OR summary_language RLIKE \"",$updates) . "\"";
	
	$result = queryMySQLDatabase($query);

	$rowsFound = @ mysql_num_rows($result);

	if ($rowsFound)
	  {
	    print '<p>The following record';
	    if ($rowsFound == 1) 
	      {
		print ' has an update ';
	      }
	    else
	      {
		print 's have updates ';
	      }
	    print 'on the arXiv.</p>';

	    for ($i = 0; $i<$rowsFound; $i++) {
	      $row = mysql_fetch_array($result);

// FIX ME: link target isn't right
	      print "<dt><span class=\"list-identifier\"><a href=\""
		. $row['summary_language']
		. "\" title=\"Abstract\">"
		. $row['summary_language']
		. "</a> [<a href=\"http://arxiv.org/ps/"
		. $row['summary_language']
		. "\" title=\"Download PostScript\">ps</a>, <a href=\"http://arxiv.org/pdf/"
		. $row['summary_language']
		. "\" title=\"Download PDF\">pdf</a>, <a href=\"http://arxiv.org/format/"
		. $row['summary_language']
		. "\" title=\"Other formats\">other</a>, <a href=\"http://www.math.ntnu.no/~stacey/RefBase/show.php?record="
		. $row['serial']
		. "\">local record</a>]</span></dt>\n";

	      print '<dd>
<div class="meta">
<div class="list-title">
<span class="descriptor">Title:</span>';
    
	      print $row['title'];

	      print '</div></div>
</dd>';
	      
	    }
	  }
	else
	  {
	    print 'No records currently in the database were updated yesterday on the arXiv.';
	  }
      }
  }    
print '</dl>';

print $next_form;

print '</div>';
    
 

// --------------------------------------------------------------------

// DISPLAY THE HTML FOOTER:
// call the 'showPageFooter()' and 'displayHTMLfoot()' functions (which are defined in 'footer.inc.php')
showPageFooter($HeaderString);

displayHTMLfoot();

// --------------------------------------------------------------------
// TODO:
//  1. Add a form to select date
//  2. Add a highlighting facility: to highlight entries that match a given search and to highlight entries already in the database.
  }
?>
