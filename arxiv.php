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
      $HeaderString = "<b><span class=\"warning\">There were validation errors regarding the data you entered:</span></b>";
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
		      'Keywords' => implode(";", $keywords),
		      'showSource' => '1'
		      );

    saveSessionVariable("formVars", $postData);

    header("Location: import_modify.php");

  }
else
  {

// (2a) Display header:
// call the 'displayHTMLhead()' and 'showPageHeader()' functions (which are defined in 'header.inc.php'):
displayHTMLhead(encodeHTML($officialDatabaseName) . $pageTitle, "index,follow", "Import records into the " . encodeHTML($officialDatabaseName), "", false, "", $viewType, array());
showPageHeader($HeaderString);


$arxivfeed = new SimplePie();
$arxivfeed->set_feed_url("http://arxiv.org/rss/math?version=2.0");
//$arxivfeed->set_feed_url("http://www.math.ntnu.no/~stacey/arxiv_test");
$arxivfeed->set_input_encoding('UTF-8');
$arxivfeed->enable_cache(true);
// Cache is located in the same directory as this file
$arxivfeed->set_cache_location('/amd/abel/home/www/stacey/RefBase/Cache');
$arxivfeed->set_cache_duration(43200); // 12 hours
$arxivfeed->enable_order_by_date(false);
$arxivfeed->init();

//$atomNamespace = 'http://www.w3.org/2005/Atom';
//$opensearchNamespace = 'http://a9.com/-/spec/opensearch/1.1/';
//$arxivNamespace = 'http://arxiv.org/schemas/atom';

$arxivArray = $arxivfeed->get_items();
$arxivCount = count($arxivArray);

print "<div id=\"arxiv\">\n";


$arxivChannel = $arxivfeed->get_feed_tags('', 'channel');
$arxivDate = $arxivChannel[0]['child']['']['pubDate']['0']['data'];
$arxivTitle = $arxivChannel[0]['child']['']['title']['0']['data'];

print "<h2>$arxivTitle</h2>\n";
print "Published on: <i>$arxivDate</i>\n";

print "<dl>\n";

// if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
{
  print "<form action=\"arxiv.php\" method=\"POST\">\n"
  . "<input type=\"submit\" name=\"submit\" value=\"Import\" title=\"Press this button to import the selected records into the database.\">\n";
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

for($i = 0; $i < $arxivCount; $i++)
  {
    $article=$arxivArray[$i];
    $link=$article->get_link();
    $id = preg_replace('#^http://arxiv.org/abs/#','',$link);
    $longtitle =$article->get_title();
    $description=$article->get_description();

    $title = preg_replace('#\s*\([^\)]*\)$#','',$longtitle);
    $subject = preg_replace('#.*\[(.*)\]\s*[A-Z ]*\s*\)$#','$1',$longtitle);
    $status = preg_replace('#.*\]\s*([A-Z ]*)\s*\)$#','$1',$longtitle);

    if ($status == "UPDATED") 
      {
	$updates[] = $id;
	continue;
      }

    if ($status == "CROSS LISTED" and !$cross)
      {
      print '<h3>Cross-Lists</h3>';
      $cross=1;
      }

    $desc = explode('</p>',$description,2);
    $authors = preg_replace('#<p>Authors:\s*#','',$desc[0]);

    print "<dt>";

    //    if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
      {
	print "<input type=\"radio\" name=\"arXivImport"
	  . $id
	  . "\" value=\""
	  . $id
	  . "\" title=\"Click to select "
	  . $id
	  . " for importing\"";
	if (isset($formVars['SelectAll']))
	  print "checked";
	print ">";
      }

    print "["
      . ($i + 1)
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
<div class="meta">
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

    print $desc[1];

    //    if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
      print "Keywords:&nbsp;"
	. "<input type=\"text\" name=\""
	. $id
	. "Keywords\" title=\"Keywords to apply to "
	. $id
	. " (delimited by semi-colons)\" size=\"30\">\n";


    print '</div>
</dd>';

  }

// if (isset($_SESSION['user_permissions']) AND ereg("(allow_batch_import)", $_SESSION['user_permissions'])) // if the 'user_permissions' session variable does contain 'allow_batch_import'...
  print "<input type=\"submit\" name=\"submit\" value=\"Import\" title=\"Press this button to import the selected records into the database.\">\n</form>";

print '<h3>Updates</h3>';


connectToMySQLDatabase();

$query = "SELECT summary_language,title,serial FROM $tableRefs WHERE summary_language RLIKE \"" . implode("\" OR summary_language RLIKE \"",$updates) . "\"";

$result = queryMySQLDatabase($query);

$rowsFound = @ mysql_num_rows($result);

if ($rowsFound) {
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

print '</dl>';
print '</div>';

  }
	// --------------------------------------------------------------------

	// DISPLAY THE HTML FOOTER:
	// call the 'showPageFooter()' and 'displayHTMLfoot()' functions (which are defined in 'footer.inc.php')
	showPageFooter($HeaderString);

	displayHTMLfoot();

	// --------------------------------------------------------------------
?>
