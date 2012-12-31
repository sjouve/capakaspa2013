<?php

/*
 *  pgn4web javascript chessboard
 *  copyright (C) 2009-2012 Paolo Casaschi
 *  see README file and http://pgn4web.casaschi.net
 *  for credits, license and more details
 */

error_reporting(E_ERROR | E_PARSE);

$pgnDebugInfo = "";

$tmpDir = "viewer";
$fileUploadLimitBytes = 4194304;
$fileUploadLimitText = round(($fileUploadLimitBytes / 1048576), 0) . "MB";
$fileUploadLimitIniText = ini_get("upload_max_filesize");
if ($fileUploadLimitIniText === "") { $fileUploadLimitIniText = "unknown"; }

// it would be nice here to evaluate ini_get('allow_fopen_url') and flag the issue (possibly disabling portions of the input forms), but the return values of ini_get() for boolean values are totally unreliable, so we have to leave with the generic server error message when trying to load a remote URL while allow_fopen_url is disabled in php.ini

$zipSupported = function_exists('zip_open');
if (!$zipSupported) { $pgnDebugInfo = $pgnDebugInfo . "ZIP support unavailable from server, missing php ZIP library<br/>"; }

$debugHelpText = "a flashing chessboard signals errors in the PGN data, click on the top left chessboard square for debug messages";

if (!($goToView = get_pgn())) { $pgnText = $krabbeStartPosition = get_krabbe_position(); }


$presetURLsArray = array();
function addPresetURL($label, $javascriptCode) {
  global $presetURLsArray;
  array_push($presetURLsArray, array('label' => $label, 'javascriptCode' => $javascriptCode));
}

// modify the viewer-preset-URLs.php file to add preset URLs for the viewer's form
include 'viewer-preset-URLs.php';


$headlessPage = strtolower(get_param("headlessPage", "hp", ""));

print_header();
print_form();
check_tmpDir();
print_chessboard();
print_footer();


function get_krabbe_position() {

  $krabbePositions = array('',
    '[Round "1"][FEN "rnq2rk1/1pn3bp/p2p2p1/2pPp1PP/P1P1Pp2/2N2N2/1P1B1P2/R2QK2R b KQ - 1 16"] 16... Nc6',
    '[Round "2"][FEN "8/8/4kpp1/3p1b2/p6P/2B5/6P1/6K1 b - - 2 47"] 47... Bh3',
    '[Round "3"][FEN "5rk1/pp4pp/4p3/2R3Q1/3n4/2q4r/P1P2PPP/5RK1 b - - 1 23"] 23. Qg3',
    '[Round "4"][FEN "1r6/4k3/r2p2p1/2pR1p1p/2P1pP1P/pPK1P1P1/P7/1B6 b - - 0 48"] 48... Rxb3+',
    '[Round "5"][FEN "2k2b1r/pb1r1p2/5P2/1qnp4/Npp3Q1/4B1P1/1P3PBP/R4RK1 w - - 4 21"] 21. Qg7',
    '[Round "6"][FEN "r1bq1rk1/1p3ppp/p1pp2n1/3N3Q/B1PPR2b/8/PP3PPP/R1B3K1 w - - 0 14"] 14. Rxh4',
    '[Round "7"][FEN "r4k1r/1b2bPR1/p4n2/3p4/4P2P/1q2B2B/PpP5/1K4R1 w - - 0 26"] 26. Bh6',
    '[Round "8"][FEN "r1b2r1k/4qp1p/p2ppb1Q/4nP2/1p1NP3/2N5/PPP4P/2KR1BR1 w - - 4 18"] 18. Nc6',
    '[Round "9"][FEN "8/5B2/6Kp/6pP/5b2/p7/1k3P2/8 b - - 3 69"] 69... Be3',
    '[Round "10"][FEN "4r1k1/q6p/2p4P/2P2QP1/1p6/rb2P3/1B6/1K4RR w - - 1 38"] 38. Qxh7+',
    '[Round "11"][FEN "6k1/3Q4/5p2/5P2/8/1KP5/PP4qp/2B5 w - - 0 99"] 99. Bg5',
    '[Round "12"][FEN "k4b1r/p3pppp/B1p2n2/3rB1N1/7q/8/PPP2P2/R2Q1RK1 w - - 1 18"] 18. c4',
    '[Round "13"][FEN "1nbk1b1r/r3pQpp/pq2P3/1p1P2B1/2p5/2P5/5PPP/R3KB1R b KQ - 0 15"] 15... Rd7',
    '[Round "14"][FEN "5r2/7k/1pPP3P/8/4p3/3p4/P4R1P/7K b - - 0 48"] 48... e3',
    '[Round "15"][FEN "rnb1kr2/pp1p1pQp/6q1/4PpB1/1P6/8/1PP2PPP/2KR3R w q - 2 15"] 15. e6',
    '[Round "16"][FEN "7k/1p1P2pp/p7/3P4/1Q5P/5pPK/PP3r2/1q5B b - - 1 37"] 37... h5',
    '[Round "17"][FEN "r2q1rk1/pp2bpp1/4p2p/2pPB2P/2P3n1/3Q2N1/PP3PP1/2KR3R w - - 1 17"] 17. Bxg7',
    '[Round "18"][FEN "r2qk2r/1b3ppp/p2p1b2/2nNp3/1R2P3/2P5/1PN2PPP/3QKB1R w Kkq - 3 17"] 17. Rxb7',
    '[Round "19"][FEN "r3kbnr/p1pp1qpp/b1n1P3/6N1/1p6/8/Pp3PPP/RNBQR1K1 b kq - 0 12"] 12... O-O-O',
    '[Round "20"][FEN "r2qkb1r/pb1p1p1p/1pn2np1/2p1p3/2P1P3/2NP1NP1/PP3PBP/R1BQ1RK1 w kq - 0 9"] 9. Nxe5',
    '');

  return $krabbePositions[rand(0, count($krabbePositions)-1)];
}

function get_param($param, $shortParam, $default) {
  if (isset($_REQUEST[$param]) && stripslashes(rawurldecode($_REQUEST[$param]))) { return stripslashes(rawurldecode($_REQUEST[$param])); }
  if (isset($_REQUEST[$shortParam]) && stripslashes(rawurldecode($_REQUEST[$shortParam]))) { return stripslashes(rawurldecode($_REQUEST[$shortParam])); }
  return $default;
}

function get_pgn() {

  global $pgnText, $pgnTextbox, $pgnUrl, $pgnFileName, $pgnFileSize, $pgnStatus, $tmpDir, $debugHelpText, $pgnDebugInfo;
  global $fileUploadLimitIniText, $fileUploadLimitText, $fileUploadLimitBytes, $krabbeStartPosition, $goToView, $zipSupported;

  $pgnDebugInfo = $pgnDebugInfo . get_param("debug", "d", "");

  $pgnText = get_param("pgnText", "pt", "");

  $pgnUrl = get_param("pgnData", "pd", "");
  if ($pgnUrl == "") { $pgnUrl = get_param("pgnUrl", "pu", ""); }

  if ($pgnText) {
    $pgnStatus = "PGN games from textbox input";
    $pgnTextbox = $pgnText = str_replace("\\\"", "\"", $pgnText);

    $pgnText = preg_replace("/\[/", "\n\n[", $pgnText);
    $pgnText = preg_replace("/\]/", "]\n\n", $pgnText);
    $pgnText = preg_replace("/([012\*])(\s*)(\[)/", "$1\n\n$3", $pgnText);
    $pgnText = preg_replace("/\]\s*\[/", "]\n[", $pgnText);
    $pgnText = preg_replace("/^\s*\[/", "[", $pgnText);
    $pgnText = preg_replace("/\n[\s*\n]+/", "\n\n", $pgnText);

    $pgnTextbox = $pgnText;

    return TRUE;
  } else if ($pgnUrl) {
    $pgnStatus = "PGN games from URL: <a href='" . $pgnUrl . "'>" . $pgnUrl . "</a>";
    $isPgn = preg_match("/\.(pgn|txt)$/i",$pgnUrl);
    $isZip = preg_match("/\.zip$/i",$pgnUrl);
    if ($isZip) {
      if (!$zipSupported) {
        $pgnStatus = "unable to open zipfile&nbsp; &nbsp;<span style='color: gray;'>please <a style='color: gray;' href='" . $pgnUrl. "'>download zipfile locally</a> and submit extracted PGN</span>";
        return FALSE;
      } else {
        $zipFileString = "<a href='" . $pgnUrl . "'>zip URL</a>";
        $tempZipName = tempnam($tmpDir, "pgn4webViewer_");
        // $pgnUrlOpts tries forcing following location redirects
        // depending on server configuration, the script might still fail if the ZIP URL is redirected
        $pgnUrlOpts = array("http" => array("follow_location" => TRUE, "max_redirects" => 20));
        $pgnUrlHandle = fopen($pgnUrl, "rb", false, stream_context_create($pgnUrlOpts));
        $tempZipHandle = fopen($tempZipName, "wb");
        $copiedBytes = stream_copy_to_stream($pgnUrlHandle, $tempZipHandle, $fileUploadLimitBytes + 1, 0);
        fclose($pgnUrlHandle);
        fclose($tempZipHandle);
        if (($copiedBytes > 0) && ($copiedBytes <= $fileUploadLimitBytes)) {
          $pgnSource = $tempZipName;
        } else {
          $pgnStatus = "failed to get " . $zipFileString . ": file not found, file size exceeds " . $fileUploadLimitText . " form limit, " . $fileUploadLimitIniText . " server limit or server error";
          if (($tempZipName) && (file_exists($tempZipName))) { unlink($tempZipName); }
          return FALSE;
        }
      }
    } else {
      $pgnSource = $pgnUrl;
    }
  } elseif (count($_FILES) == 0) {
    $pgnStatus = "please enter chess games in PGN format&nbsp; &nbsp;<span style='color: gray;'></span>";
    return FALSE;
  } elseif ($_FILES['pgnFile']['error'] === UPLOAD_ERR_OK) {
    $pgnFileName = $_FILES['pgnFile']['name'];
    $pgnStatus = "PGN games from file: " . $pgnFileName;
    $pgnFileSize = $_FILES['pgnFile']['size'];
    if ($pgnFileSize == 0) {
      $pgnStatus = "failed uploading PGN games: file not found, file empty or upload error";
      return FALSE;
    } elseif ($pgnFileSize > $fileUploadLimitBytes) {
      $pgnStatus = "failed uploading PGN games: file size exceeds " . $fileUploadLimitText . " limit";
      return FALSE;
    } else {
      $isPgn = preg_match("/\.(pgn|txt)$/i",$pgnFileName);
      $isZip = preg_match("/\.zip$/i",$pgnFileName);
      $pgnSource = $_FILES['pgnFile']['tmp_name'];
    }
  } else {
    $pgnStatus = "failed uploading PGN games: ";
    switch ($_FILES['pgnFile']['error']) {
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        $pgnStatus = $pgnStatus . "file size exceeds " . $fileUploadLimitText . " form limit or " . $fileUploadLimitIniText . " server limit";
        break;
      case UPLOAD_ERR_PARTIAL:
      case UPLOAD_ERR_NO_FILE:
        $pgnStatus = $pgnStatus . "file missing or truncated";
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
      case UPLOAD_ERR_CANT_WRITE:
      case UPLOAD_ERR_EXTENSION:
        $pgnStatus = $pgnStatus . "server error";
        break;
      default:
        $pgnStatus = $pgnStatus . "unknown upload error";
        break;
    }
    return FALSE;
  }

  if ($isZip) {
    if ($zipSupported) {
      if ($pgnUrl) { $zipFileString = "<a href='" . $pgnUrl . "'>zip URL</a>"; }
      else { $zipFileString = "zip file"; }
      $pgnZip = zip_open($pgnSource);
      if (is_resource($pgnZip)) {
        while (is_resource($zipEntry = zip_read($pgnZip))) {
          if (zip_entry_open($pgnZip, $zipEntry)) {
            if (preg_match("/\.pgn$/i",zip_entry_name($zipEntry))) {
              $pgnText = $pgnText . zip_entry_read($zipEntry, zip_entry_filesize($zipEntry)) . "\n\n\n";
            }
            zip_entry_close($zipEntry);
          } else {
            $pgnStatus = "failed reading " . $zipFileString . " content";
            zip_close($pgnZip);
            if (($tempZipName) && (file_exists($tempZipName))) { unlink($tempZipName); }
            return FALSE;
          }
        }
        zip_close($pgnZip);
        if (($tempZipName) && (file_exists($tempZipName))) { unlink($tempZipName); }
        if (!$pgnText) {
          $pgnStatus = "PGN games not found in " . $zipFileString;
         return FALSE;
        } else {
          return TRUE;
        }
      } else {
        if (($tempZipName) && (file_exists($tempZipName))) { unlink($tempZipName); }
        $pgnStatus = "failed opening " . $zipFileString;
        return FALSE;
      }
    } else {
      $pgnStatus = "ZIP support unavailable from this server&nbsp; &nbsp;<span style='color: gray;'>only PGN files are supported</span>";
      return FALSE;
    }
  }

  if ($isPgn) {
    if ($pgnUrl) { $pgnFileString = "<a href='" . $pgnUrl . "'>pgn URL</a>"; }
    else { $pgnFileString = "pgn file"; }
    $pgnText = file_get_contents($pgnSource, NULL, NULL, 0, $fileUploadLimitBytes + 1);
    if (!$pgnText) {
      $pgnStatus = "failed reading " . $pgnFileString . ": file not found or server error";
      return FALSE;
    }
    if ((strlen($pgnText) == 0) || (strlen($pgnText) > $fileUploadLimitBytes)) {
      $pgnStatus = "failed reading " . $pgnFileString . ": file size exceeds " . $fileUploadLimitText . " form limit, " . $fileUploadLimitIniText . " server limit or server error";
      return FALSE;
    }
    return TRUE;
  }

  if ($pgnSource) {
    if ($zipSupported) {
      $pgnStatus = "only PGN and ZIP (zipped pgn) files are supported";
    } else {
      $pgnStatus = "only PGN files are supported&nbsp; &nbsp;<span style='color: gray;'>ZIP support unavailable from this server</span>";
    }
    return FALSE;
  }

  return TRUE;
}

function check_tmpDir() {

  global $pgnText, $pgnTextbox, $pgnUrl, $pgnFileName, $pgnFileSize, $pgnStatus, $tmpDir, $debugHelpText, $pgnDebugInfo;
  global $fileUploadLimitIniText, $fileUploadLimitText, $fileUploadLimitBytes, $krabbeStartPosition, $goToView, $zipSupported;

  $tmpDirHandle = opendir($tmpDir);
  while($entryName = readdir($tmpDirHandle)) {
    if (($entryName !== ".") && ($entryName !== "..") && ($entryName !== "index.html")) {
      if ((time() - filemtime($tmpDir . "/" . $entryName)) > 3600) {
        $unexpectedFiles = $unexpectedFiles . " " . $entryName;
      }
    }
  }
  closedir($tmpDirHandle);

  if ($unexpectedFiles) {
    $pgnDebugInfo = $pgnDebugInfo . "clean temporary directory " . $tmpDir . "(" . $unexpectedFiles . ")<br>";
  }
}

function print_header() {

  global $headlessPage;

  if (($headlessPage == "true") || ($headlessPage == "t")) {
     $headClass = "display: none;";
  } else {
     $headClass = "";
  }

  print <<<END
<html>

<head>

<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">

<title>pgn4web games viewer</title>

<style type="text/css">

body {
  color: black;
  background: white;
  font-family: 'pgn4web Liberation Sans', sans-serif;
  line-height: 1.4em;
  padding: 20px;
  $bodyFontSize
  overflow-x: hidden;
}

div, span, table, tr, td {
  font-family: 'pgn4web Liberation Sans', sans-serif; /* fixes IE9 body css issue */
}

a:link, a:visited, a:hover, a:active {
  color: black;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

.formControl {
  font-size: smaller;
  margin: 0;
}

.headClass {
  $headClass
}

</style>

</head>

<body>

<table class="headClass" border="0" cellpadding="0" cellspacing="0" width="100%"><tbody><tr>
<td align="left" valign="middle">
<h1 name="top" style="font-family: sans-serif; color: red;"><a style="color: red;" href=.>pgn4web</a> games viewer</h1>
</td>
<td align="right" valign="middle">
<a href=.><img src=pawns.png border=0></a>
</td>
</tr></tbody></table>

<div style="height: 1em;" class="headClass">&nbsp;</div>

END;
}


function print_form() {

  global $pgnText, $pgnTextbox, $pgnUrl, $pgnFileName, $pgnFileSize, $pgnStatus, $tmpDir, $debugHelpText, $pgnDebugInfo;
  global $fileUploadLimitIniText, $fileUploadLimitText, $fileUploadLimitBytes, $krabbeStartPosition, $goToView, $zipSupported;
  global $headlessPage, $presetURLsArray;

  $thisScript = $_SERVER['SCRIPT_NAME'];
  if (($headlessPage == "true") || ($headlessPage == "t")) { $thisScript .= "?hp=t"; }

  print <<<END

<script type="text/javascript">

  function setPgnUrl(newPgnUrl) {
    if (!newPgnUrl) { newPgnUrl = ""; }
    document.getElementById("urlFormText").value = newPgnUrl;
    return false;
  }

  function checkPgnUrl() {
    theObj = document.getElementById("urlFormText");
    if (theObj === null) { return false; }
    if (!checkPgnExtension(theObj.value)) { return false; }
    else { return (theObj.value !== ""); }
  }

  function checkPgnFile() {
    theObj = document.getElementById("uploadFormFile");
    if (theObj === null) { return false; }
    if (!checkPgnExtension(theObj.value)) { return false; }
    else { return (theObj.value !== ""); }
  }

END;

  if ($zipSupported) { print <<<END

  function checkPgnExtension(uri) {
    if (uri.match(/\\.(zip|pgn|txt)\$/i)) {
      return true;
    } else if (uri !== "") {
      alert("only PGN and ZIP (zipped pgn) files are supported");
    }
    return false;
  }

END;

  } else { print <<<END

  function checkPgnExtension(uri) {
    if (uri.match(/\\.(pgn|txt)\$/i)) {
      return true;
    } else if (uri.match(/\\.zip\$/i)) {
      alert("ZIP support unavailable from this server, only PGN files are supported\\n\\nplease submit locally extracted PGN");
    } else if (uri !== "") {
      alert("only PGN files are supported (ZIP support unavailable from this server)");
    }
    return false;
  }

END;

  }

  print <<<END

  function checkPgnFormTextSize() {
    document.getElementById("pgnFormButton").title = "PGN textbox size is " + document.getElementById("pgnFormText").value.length;
    if (document.getElementById("pgnFormText").value.length == 1) {
      document.getElementById("pgnFormButton").title += " char;";
    } else {
      document.getElementById("pgnFormButton").title += " chars;";
    }
    document.getElementById("pgnFormButton").title += " $debugHelpText";
    document.getElementById("pgnFormText").title = document.getElementById("pgnFormButton").title;
  }

  function loadPgnFromForm() {
    theObjPgnFormText = document.getElementById('pgnFormText');
    if (theObjPgnFormText === null) { return; }
    if (theObjPgnFormText.value === "") { return; }

    theObjPgnText = document.getElementById('pgnText');
    if (theObjPgnText === null) { return; }

    theObjPgnText.value = theObjPgnFormText.value;

    theObjPgnText.value = theObjPgnText.value.replace(/\\[/g,'\\n\\n[');
    theObjPgnText.value = theObjPgnText.value.replace(/\\]/g,']\\n\\n');
    theObjPgnText.value = theObjPgnText.value.replace(/([012\\*])(\\s*)(\\[)/g,'\$1\\n\\n\$3');
    theObjPgnText.value = theObjPgnText.value.replace(/\\]\\s*\\[/g,']\\n[');
    theObjPgnText.value = theObjPgnText.value.replace(/^\\s*\\[/g,'[');
    theObjPgnText.value = theObjPgnText.value.replace(/\\n[\\s*\\n]+/g,'\\n\\n');

    document.getElementById('pgnStatus').innerHTML = "PGN games from textbox input";
    document.getElementById('uploadFormFile').value = "";
    document.getElementById('urlFormText').value = "";

    firstStart = true;
    start_pgn4web();
    if (window.location.hash == "view") { window.location.reload(); }
    else { window.location.hash = "view"; }

    return;
  }

  function urlFormSelectChange() {
    theObj = document.getElementById("urlFormSelect");
    if (theObj === null) { return; }

    targetPgnUrl = "";
    switch (theObj.value) {

END;

  foreach($presetURLsArray as $value) {
    print("\n" . '      case "' . $value['label'] . '":' . "\n" . '        targetPgnUrl = (function(){ ' . $value['javascriptCode'] . '})();' . "\n" . '      break;' . "\n");
  }

  $formVariableColspan = $presetURLsArray ? 2: 1;
  print <<<END

      default:
      break;
    }
    setPgnUrl(targetPgnUrl);
    theObj.value = "header";
  }

function reset_viewer() {
   document.getElementById("uploadFormFile").value = "";
   document.getElementById("urlFormText").value = "";
   document.getElementById("pgnFormText").value = "";
   checkPgnFormTextSize();
   document.getElementById("pgnStatus").innerHTML = "please enter chess games in PGN format&nbsp; &nbsp;<span style='color: gray;'></span>";
   document.getElementById("pgnText").value = '$krabbeStartPosition';

   firstStart = true;
   start_pgn4web();
   if (window.location.hash == "top") { window.location.reload(); }
   else {window.location.hash = "top"; }
}

// fake functions to avoid warnings before pgn4web.js is loaded
function disableShortcutKeysAndStoreStatus() {}
function restoreShortcutKeysStatus() {}

</script>

<table width="100%" cellspacing="0" cellpadding="3" border="0"><tbody>

  <tr>
    <td align="left" valign="top">
      <form id="uploadForm" action="$thisScript" enctype="multipart/form-data" method="POST" style="display: inline;">
        <input id="uploadFormSubmitButton" type="submit" class="formControl" value="show games from PGN (or zipped PGN) file" style="width:100%" title="PGN and ZIP files must be smaller than $fileUploadLimitText (form limit) and $fileUploadLimitIniText (server limit); $debugHelpText" onClick="this.blur(); return checkPgnFile();">
    </td>
    <td colspan="$formVariableColspan" width="100%" align="left" valign="top">
        <input type="hidden" name="MAX_FILE_SIZE" value="$fileUploadLimitBytes">
        <input id="uploadFormFile" name="pgnFile" type="file" class="formControl" style="width:100%" title="PGN and ZIP files must be smaller than $fileUploadLimitText (form limit) and $fileUploadLimitIniText (server limit); $debugHelpText" onClick="this.blur();">
      </form>
    </td>
  </tr>

  <tr>
    <td align="left" valign="top">
      <form id="urlForm" action="$thisScript" method="POST" style="display: inline;">
        <input id="urlFormSubmitButton" type="submit" class="formControl" value="show games from PGN (or zipped PGN) URL" title="PGN and ZIP files must be smaller than $fileUploadLimitText (form limit) and $fileUploadLimitIniText (server limit); $debugHelpText" onClick="this.blur(); return checkPgnUrl();">
    </td>
    <td width="100%" align="left" valign="top">
        <input id="urlFormText" name="pgnUrl" type="text" class="formControl" value="" style="width:100%" onFocus="disableShortcutKeysAndStoreStatus();" onBlur="restoreShortcutKeysStatus();" title="PGN and ZIP files must be smaller than $fileUploadLimitText (form limit) and $fileUploadLimitIniText (server limit); $debugHelpText">
      </form>
    </td>
END;

  if ($presetURLsArray) {
    print('    <td align="right" valign="top">' . "\n" . '        <select id="urlFormSelect" class="formControl" title="select the download URL from the preset options; please support the sites providing the PGN downloads" onChange="this.blur(); urlFormSelectChange();">' . "\n" . '          <option value="header">preset URL</option>' . "\n");
    foreach($presetURLsArray as $value) {
      print('          <option value="' . $value['label'] . '">' . $value['label'] . '</option>' . "\n");
    }
    print('          <option value="clear">clear URL</option>' . "\n" . '        </select>' . "\n" . '    </td>' . "\n");
  }

  print <<<END
  </tr>

  <tr>
    <td align="left" valign="top">
      <form id="textForm" style="display: inline;">
        <input id="pgnFormButton" type="button" class="formControl" value="show games from PGN textbox" style="width:100%;" onClick="this.blur(); loadPgnFromForm();">
    </td>
    <td colspan="$formVariableColspan" rowspan="2" width="100%" align="right" valign="top">
        <textarea id="pgnFormText" class="formControl" name="pgnTextbox" rows=4 style="width:100%;" onFocus="disableShortcutKeysAndStoreStatus();" onBlur="restoreShortcutKeysStatus();" onChange="checkPgnFormTextSize();">$pgnTextbox</textarea>
      </form>
    </td>
  </tr>

  <tr>
  <td align="left" valign="bottom">
    <input id="clearButton" type="button" class="formControl" value="reset PGN viewer" onClick="this.blur(); if (confirm('reset PGN viewer, current games and inputs will be lost')) { reset_viewer(); }" title="reset PGN viewer, current games and inputs will be lost">
  </td>
  </tr>

</tbody></table>

END;
}

function print_chessboard() {

  global $pgnText, $pgnTextbox, $pgnUrl, $pgnFileName, $pgnFileSize, $pgnStatus, $tmpDir, $debugHelpText, $pgnDebugInfo;
  global $fileUploadLimitIniText, $fileUploadLimitText, $fileUploadLimitBytes, $krabbeStartPosition, $goToView, $zipSupported;

  $pieceSize = 38;
  $pieceType = "merida";
  $pieceSizeCss = $pieceSize . "px";

  print <<<END

<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td valign="top" align="left">
<a name="view">&nbsp;</a><div id="pgnStatus" style="font-weight: bold; margin-top: 2em; margin-bottom: 3em;">$pgnStatus</div>
</td><td valign="top" align="right">
<div style="padding-top: 1em;">
&nbsp;&nbsp;&nbsp;<a href="#moves" style="color: gray; font-size: 66%;">moves</a>&nbsp;&nbsp;&nbsp;<a href="#view" style="color: gray; font-size: 66%;">board</a>&nbsp;&nbsp;&nbsp;<a href="#top" style="color: gray; font-size: 66%;">form</a>
</div>
</tr></table>

<link href="fonts/pgn4web-font-LiberationSans.css" type="text/css" rel="stylesheet" />
<link href="fonts/pgn4web-font-ChessSansUsual.css" type="text/css" rel="stylesheet" />
<style type="text/css">

.boardTable {
  border-style: solid;
  border-color: #663300;
  border-width: 3;
  box-shadow: 0px 0px 20px #663300;
  width: 374px;
  height: 374px;
}

.pieceImage {
  width: $pieceSizeCss;
  height: $pieceSizeCss;
}

.whiteSquare,
.blackSquare,
.highlightWhiteSquare,
.highlightBlackSquare {
  width: 42px;
  height: 42px;
  border-style: solid;
  border-width: 2px;
}

.whiteSquare,
.highlightWhiteSquare {
  border-color: #ffcc99;
  background: #ffcc99;
}

.blackSquare,
.highlightBlackSquare {
  border-color: #cc9966;
  background: #cc9966;
}

.highlightWhiteSquare,
.highlightBlackSquare {
  border-color: #663300;
}

.selectControl {
/* a "width" attribute here must use the !important flag to override default settings */
  width: 99% !important;
}

.optionSelectControl {
}

.buttonControl {
/* a "width" attribute here must use the !important flag to override default settings */
}

.buttonControlSpace {
/* a "width" attribute here must use the !important flag to override default settings */
}

.searchPgnButton {
/* a "width" attribute here must use the !important flag to override default settings */
  width: 9.5% !important;
  margin-right: 0.5%;
}

.searchPgnExpression {
/* a "width" attribute here must use the !important flag to override default settings */
  width: 89% !important;
}

.move,
.variation,
.comment {
  font-weight: normal;
  line-height: 1.4em;
}

.move,
.variation,
.commentMove {
  font-family: 'pgn4web ChessSansUsual', 'pgn4web Liberation Sans', sans-serif;
}

.move,
.variation {
  text-decoration: none;
}

.move {
  color: black !important;
}

.comment,
.variation {
  color: gray !important;
}

a.variation {
  color: gray !important;
}

.moveOn,
.variationOn {
  background: #ffcc99;
}

.label {
  color: gray;
  padding-right: 10;
  text-align: right;
}

.normalItem {
  white-space: nowrap;
  line-height: 1.4em;
}

.boldItem {
  font-weight: bold;
}

.rowSpace {
  height: 8px;
}

</style>

<link rel="shortcut icon" href="pawn.ico" />

<script src="pgn4web.js" type="text/javascript"></script>
<script src="chess-informant-NAG-symbols.js" type="text/javascript"></script>
<script src="engine.js" type="text/javascript"></script>

<script src="fide-lookup.js" type="text/javascript"></script>

<script type="text/javascript">
  pgn4web_engineWindowUrlParameters = "pf=$pieceType";
  SetImagePath("$pieceType/$pieceSize");
  SetImageType("png");
  SetHighlightOption(false);
  SetCommentsIntoMoveText(true);
  SetCommentsOnSeparateLines(true);
  SetInitialGame(1);
  SetInitialVariation(0);
  SetInitialHalfmove(0);
  SetGameSelectorOptions(" Event         Site          Rd  White            Black            Res  Date", true, 12, 12, 2, 15, 15, 3, 10);
  SetAutostartAutoplay(false);
  SetAutoplayDelay(2000);
  SetShortcutKeysEnabled(true);

  function customFunctionOnPgnTextLoad() {
    if (theObj = document.getElementById('numGm')) { theObj.innerHTML = numberOfGames; }
  }

  function customFunctionOnPgnGameLoad() {
    if (theObj = document.getElementById('currGm')) { theObj.innerHTML = currentGame+1; }
    if (theObj = document.getElementById('numPly')) { theObj.innerHTML = PlyNumber; }
    customPgnHeaderTag('ECO', 'GameECO');
    customPgnHeaderTag('Opening', 'GameOpening');
    if (theObj = document.getElementById('GameOpening')) { theObj.innerHTML = fixCommentForDisplay(theObj.innerHTML); }
    customPgnHeaderTag('Variation', 'GameVariation');
    if (theObj = document.getElementById('GameVariation')) { theObj.innerHTML = fixCommentForDisplay(theObj.innerHTML); }
    customPgnHeaderTag('Annotator', 'GameAnnotator');
    customPgnHeaderTag('Result', 'ResultAtGametextEnd');
  }

  function customFunctionOnMove() {
    if (theObj = document.getElementById('currPly')) { theObj.innerHTML = CurrentPly; }
  }

  // customShortcutKey_Shift_1 defined by fide-lookup.js
  // customShortcutKey_Shift_2 defined by fide-lookup.js

  // customShortcutKey_Shift_8 defined by engine.js
  // customShortcutKey_Shift_9 defined by engine.js
  // customShortcutKey_Shift_0 defined by engine.js

</script>

<!-- paste your PGN below and make sure you dont specify an external source with SetPgnUrl() -->
<form style="display: inline"><textarea style="display:none" id="pgnText">

$pgnText

</textarea></form>
<!-- paste your PGN above and make sure you dont specify an external source with SetPgnUrl() -->

<table width="100%" cellspacing="0" cellpadding="0" border="0">

  <tr valign="bottom">
    <td align="center" colspan="2">

      <div id="GameSelector"></div>

      <div id="GameSearch"></div>

      <div style="padding-top: 2em;">&nbsp;</div>

    </td>
  </tr>

  <tr valign="top">
    <td valign="top" align="center" width="50%">
      <span id="GameBoard"></span>
      <p></p>
      <div id="GameButtons"></div>
    </td>
    <td valign="top" align="left" width="50%">

      <table valign="bottom">
      <tr><td class="label">date</td><td class="normalItem"><span id="GameDate"></span>&nbsp;</td></tr>
      <tr><td class="label">site</td><td class="normalItem"><span id="GameSite"></span>&nbsp;</td></tr>
      <tr><td colspan="2" class="rowSpace"></td></tr>
      <tr><td class="label">event</td><td class="normalItem"><span id="GameEvent"></span>&nbsp;</td></tr>
      <tr><td class="label">round</td><td class="normalItem"><span id="GameRound"></span>&nbsp;</td></tr>
      <tr><td colspan="2" class="rowSpace"></td></tr>
      <tr><td class="label">white</td><td class="boldItem"><span id="GameWhite"></span>&nbsp;</td></tr>
      <tr><td class="label">black</td><td class="boldItem"><span id="GameBlack"></span>&nbsp;</td></tr>
      <tr><td colspan="2" class="rowSpace"></td></tr>
      <tr><td class="label">result</td><td class="boldItem"><span id="GameResult"></span>&nbsp;</td></tr>
      <tr><td colspan="2" class="rowSpace"></td></tr>
      <tr><td class="label">eco</td><td class="normalItem"><span id="GameECO"></span>&nbsp;</td></tr>
      <tr><td class="label">opening</td><td class="normalItem"><span id="GameOpening"></span>&nbsp;</td></tr>
      <tr><td class="label">variation</td><td class="normalItem"><span id="GameVariation"></span>&nbsp;</td></tr>
      <tr><td colspan="2" class="rowSpace"></td></tr>
      <tr><td class="label">last</td><td class="normalItem move"><span id="GameLastMove"></span>&nbsp; &nbsp;<span id="GameLastVariations"></span></td></tr>
      <tr><td class="label">next</td><td class="normalItem move"><span id="GameNextMove"></span>&nbsp; &nbsp;<span id="GameNextVariations"></span></td></tr>
      <tr><td colspan="2" class="rowSpace"></td></tr>
      <tr><td class="label">annotator</td><td class="normalItem"><span id="GameAnnotator"></span>&nbsp;</td></tr>
      </table>

    </td>
  </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td valign="bottom" align="right">
&nbsp;&nbsp;&nbsp;<a name="moves" href="#moves" style="color: gray; font-size: 66%;">moves</a>&nbsp;&nbsp;&nbsp;<a href="#view" style="color: gray; font-size: 66%;">board</a>&nbsp;&nbsp;&nbsp;<a href="#top" style="color: gray; font-size: 66%;">form</a>
</tr></table>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td colspan="2">
      <div style="padding-top: 2em; padding-bottom: 1em; text-align: justify;"><span id="GameText"></span>&nbsp;<span class="move" id="ResultAtGametextEnd"></span></div>
    </td>
  </tr>
</table>

END;
}

function print_footer() {

  global $pgnText, $pgnTextbox, $pgnUrl, $pgnFileName, $pgnFileSize, $pgnStatus, $tmpDir, $debugHelpText, $pgnDebugInfo;
  global $fileUploadLimitIniText, $fileUploadLimitText, $fileUploadLimitBytes, $krabbeStartPosition, $goToView, $zipSupported;

  if ($goToView) { $hashStatement = "window.location.hash = 'view';"; }
  else { $hashStatement = ""; }

  if (($pgnDebugInfo) != "") { $pgnDebugMessage = "message for sysadmin: " . $pgnDebugInfo; }
  else {$pgnDebugMessage = ""; }

  print <<<END

<div>&nbsp;</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td valign=bottom align=left>
<div style="color: gray; margin-top: 1em; margin-bottom: 1em;">$pgnDebugMessage</div>
</td><td valign=bottom align="right">
&nbsp;&nbsp;&nbsp;<a href="#moves" style="color: gray; font-size: 66%;">moves</a>&nbsp;&nbsp;&nbsp;<a href="#view" style="color: gray; font-size: 66%;">board</a>&nbsp;&nbsp;&nbsp;<a href="#top" style="color: gray; font-size: 66%;">form</a>
</td></tr></table>

<script type="text/javascript">

function pgn4web_onload(e) {
  setPgnUrl("$pgnUrl");
  checkPgnFormTextSize();
  start_pgn4web();
  $hashStatement
}

</script>

</body>

</html>
END;
}

?>
