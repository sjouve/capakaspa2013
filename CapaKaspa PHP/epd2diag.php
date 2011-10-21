<?
	session_start();
	$titre_page = 'EPD/FEN en diagramme';
    require 'page_header.php';
?>
<SCRIPT language="JavaScript">
<!-- Begin JavaScript
// History
//   V1.01  19-APR-1998
//          Change HTML output from new window into a text area in the
//              same window, due to problems in creating new windows in
//              some browsers.
//   V1.00  18-APR-1998
//          First release

bbb     = new Image(38,38);
bbb.src = "../images/epd2diag/bbb.gif";
bbw     = new Image(38,38);
bbw.src = "../images/epd2diag/bbw.gif";

bkb     = new Image(38,38);
bkb.src = "../images/epd2diag/bkb.gif";
bkw     = new Image(38,38);
bkw.src = "../images/epd2diag/bkw.gif";

bnb     = new Image(38,38);
bnb.src = "../images/epd2diag/bnb.gif";
bnw     = new Image(38,38);
bnw.src = "../images/epd2diag/bnw.gif";

bpb     = new Image(38,38);
bpb.src = "../images/epd2diag/bpb.gif";
bpw     = new Image(38,38);
bpw.src = "../images/epd2diag/bpw.gif";

bqb     = new Image(38,38);
bqb.src = "../images/epd2diag/bqb.gif";
bqw     = new Image(38,38);
bqw.src = "../images/epd2diag/bqw.gif";

brb     = new Image(38,38);
brb.src = "../images/epd2diag/brb.gif";
brw     = new Image(38,38);
brw.src = "../images/epd2diag/brw.gif";

efb     = new Image(38,38);
efb.src = "../images/epd2diag/efb.gif";
efw     = new Image(38,38);
efw.src = "../images/epd2diag/efw.gif";

wbb     = new Image(38,38);
wbb.src = "../images/epd2diag/wbb.gif";
wbw     = new Image(38,38);
wbw.src = "../images/epd2diag/wbw.gif";

wkb     = new Image(38,38);
wkb.src = "../images/epd2diag/wkb.gif";
wkw     = new Image(38,38);
wkw.src = "../images/epd2diag/wkw.gif";

wnb     = new Image(38,38);
wnb.src = "../images/epd2diag/wnb.gif";
wnw     = new Image(38,38);
wnw.src = "../images/epd2diag/wnw.gif";

wpb     = new Image(38,38);
wpb.src = "../images/epd2diag/wpb.gif";
wpw     = new Image(38,38);
wpw.src = "../images/epd2diag/wpw.gif";

wqb     = new Image(38,38);
wqb.src = "../images/epd2diag/wqb.gif";
wqw     = new Image(38,38);
wqw.src = "../images/epd2diag/wqw.gif";

wrb     = new Image(38,38);
wrb.src = "../images/epd2diag/wrb.gif";
wrw     = new Image(38,38);
wrw.src = "../images/epd2diag/wrw.gif";

var Browser = "";
var MasterSite = false;
var board;
var ValidPosition = false;
var iold     = -1;
var jold     = -1;
var cold     = -1;
var pold     = ' ';
var inew     = -1;
var jnew     = -1;
var cnew     = -1;
var pnew     = ' ';
var SelPiece = ' ';
var validate = true;



function showInfo()
{
  alert(
        "maro's EPD2diag JavaScript\n" +
        "V1.01  (19-APR-1998)\n" +
        "(c) 1998 maro\n" +
        "marochess@geocities.com"
       );
  return;
}


function makeArray(dim)
{
  var i = 0;

  this.length = dim;
  for (i = 0; i < dim; i++)
   {
    this[i] = '';
   }
  return this;
}


function makeLetters()
{
  this.length = 8;
  this[0] = "a";
  this[1] = "b";
  this[2] = "c";
  this[3] = "d";
  this[4] = "e";
  this[5] = "f";
  this[6] = "g";
  this[7] = "h";

  return this;
}

function makeBoard()
{
  var letters    = null;
  var i          = 0;
  var j          = 0;
  var color      = 0;
  var navvendor  = "";
  var navversion = "";
  var runOK      = false;

  nav_vendor  = navigator.appName.substring(0,8).toUpperCase();
  nav_version = parseInt(navigator.appVersion.substring(0,1));

  if (((nav_vendor == "NETSCAPE") && (nav_version >= 4)) || ((nav_vendor == "MICROSOF") && (nav_version >= 4)))
    runOK = true;

  if (!runOK)
   {
    document.writeln("<PRE>");
    document.writeln("&nbsp;");
    document.writeln("</PRE>");
    document.writeln("<P align=\"center\"><CENTER><B><FONT color=\"blue\">");
    document.writeln("This JavaScript run only for Internet Explorer &gt;= V4.0 and " +
                     "Netscape Navigator &gt;= V4.0 versions");
    document.writeln("</FONT></B></CENTER></P>");
    return;
   }

  if (nav_vendor == "NETSCAPE")
    Browser = "N";  // Netscape Navigator
  else
    Browser = "E";  // Microsoft Internet Explorer

  if (window.location == "http://www.geocities.com/CapeCanaveral/Launchpad/2640/jsEPD2diag.html")
    MasterSite = true;

  letters = new makeLetters();
  board   = new makeArray(8);
  for (i = 0; i < board.length; i++)
   {
    board[i] = new makeArray(8);
   }

  for (i = 7; i >= 0; i--)
   {
    for (j = 0; j < 8; j++)
     {
      board[i][j] = ' ';
     }
   }

  document.writeln("<FORM name=\"frmBoard\">");
  document.writeln("<P align=\"center\"><CENTER>");

  document.writeln("<TABLE border=\"0\"  cellspacing=\"10\" cellpadding=\"0\">");
  document.writeln("<TR>");
  document.writeln("<TD>");

  document.writeln("<TABLE border=\"3\" bordercolor=\"#000000\" cellspacing=\"0\" cellpadding=\"0\">");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('K')\">");
  document.write("<IMG src=\"" + wkb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('Q')\">");
  document.write("<IMG src=\"" + wqb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('R')\">");
  document.write("<IMG src=\"" + wrb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('B')\">");
  document.write("<IMG src=\"" + wbb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('N')\">");
  document.write("<IMG src=\"" + wnb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('P')\">");
  document.write("<IMG src=\"" + wpb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.writeln("</TABLE>");


  document.writeln("</TD>");
  document.writeln("<TD>");


  document.writeln("<TABLE border=\"5\" bgcolor=\"#C0C0C0\" bordercolor=\"#000000\">");
  document.write("<TR>");
  document.write("<TD>");
  document.writeln("<TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"0\">");
  document.write("<TR>");
  document.write("<TD>&nbsp;</TD>");
  for (i = 0; i <= 7; i++)
    document.write("<TD align=\"center\"><B>" + letters[i] + "</B></TD>");
  document.write("</TR>");
  for (i = 7; i >= 0; i--)
   {
    document.write("<TR>");
    document.write("<TD>");
    document.write("&nbsp;<B>" + (i + 1) + "</B>&nbsp;");
    document.write("</TD>");
    for (j = 0; j < 8; j++)
     {
      color = ((i % 2) + j) % 2;
      document.write("<TD height=\"38\" width=\"38\">");
      if (color != 0)
       {
        document.write("<A href=\"javascript:doPick(" + i + "," + j + ")\">");
        document.write("<IMG name=\"cbi_" + i + "_" + j + "\" src=\"" + efw.src + "\" width=\"38\" height=\"38\" ")
        document.write("border=\"0\"></A>");
       }
      else
       {
        document.write("<A href=\"javascript:doPick(" + i + "," + j + ")\">");
        document.write("<IMG name=\"cbi_" + i + "_" + j + "\" src=\"" + efb.src + "\" width=\"38\" height=\"38\" ");
        document.write("border=\"0\"></A>");
       }
      document.write("</TD>");
     }
    document.write("<TD>");
    document.write("&nbsp;<B>" + (i + 1) + "</B>&nbsp;");
    document.write("</TD>");
    document.write("</TR>");
   }
  document.write("<TR>");
  document.write("<TD>&nbsp;</TD>");
  for (i = 0; i <= 7; i++)
    document.write("<TD align=\"center\"><B>" + letters[i] + "</B></TD>");
  document.write("</TR>");
  document.write("</TABLE>");
  document.write("</TD>");
  document.write("</TR>");
  document.writeln("</TABLE>");

  document.writeln("</TD>");
  document.writeln("<TD>");


  document.writeln("<TABLE border=\"3\" bordercolor=\"#000000\" cellspacing=\"0\" cellpadding=\"0\">");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('k')\">");
  document.write("<IMG src=\"" + bkb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('q')\">");
  document.write("<IMG src=\"" + bqb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('r')\">");
  document.write("<IMG src=\"" + brb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('b')\">");
  document.write("<IMG src=\"" + bbb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('n')\">");
  document.write("<IMG src=\"" + bnb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.write("<TR>");
  document.write("<TD height=\"38\" width=\"38\">");
  document.write("<A href=\"javascript:selPiece('p')\">");
  document.write("<IMG src=\"" + bpb.src + "\" width=\"38\" height=\"38\" border=\"0\"></A>");
  document.write("</TD>");
  document.write("</TR>");
  document.writeln("</TABLE>");


  document.writeln("</TD>");
  document.writeln("</TR>");
  document.writeln("</TABLE>");

  document.writeln("</CENTER></P>");

  document.writeln("<P align=\"center\"><CENTER>");
  document.write("<B><FONT color=\"blue\">Entrer une chaîne EPD</FONT></B><BR>");
  if (Browser == "N")
    document.writeln("<INPUT type=\"text\" value=\"\" size=\"60\" name=\"epd\"><BR>");
  else
    document.writeln("<INPUT type=\"text\" value=\"\" size=\"70\" name=\"epd\"><BR>");
  document.writeln("&nbsp;<BR>");
  document.writeln("<INPUT type=\"button\" value=\"  Afficher  \" onclick=\"setBoard(this.form)\">");
  document.writeln("&nbsp;&nbsp;");
  document.writeln("<SELECT name=\"selOptions\" onchange=\"doSelOption(this.form)\">");
  document.writeln("<OPTION selected value=\"none\">--Choisir une option--</OPTION>");
  document.writeln("<OPTION value=\"clear\">Vider l'échiquier</OPTION>");
  document.writeln("<OPTION value=\"init\">Position initiale</OPTION>");
  document.writeln("<OPTION value=\"html\">Créer un tableau HTML</OPTION>");
  document.writeln("<OPTION value=\"valid\">Valider la position</OPTION>");
  if (MasterSite)
    document.writeln("<OPTION value=\"licence\">Show Licence Information</OPTION>");
  document.writeln("</SELECT>");
  document.writeln("&nbsp;&nbsp;");
  document.writeln("<INPUT type=\"button\" value=\"  A propos  \" onclick=\"showInfo()\">");
  document.writeln("<BR>&nbsp;<BR>");
  document.writeln("<INPUT checked type=\"checkbox\" name=\"cbvalid\" onclick=\"doSwitchValidation(this.form)\">");
  document.writeln("<FONT size=\"-1\"> Seules les positions valides sont permises pour créer un tableau HTML</FONT>");

  document.writeln("<HR>");
  document.writeln("<B><FONT color=\"blue\">Tableau HTML</FONT></B><BR>");
  if (Browser == "N")
    document.writeln("<TEXTAREA readonly name=\"HTMLoutput\" rows=\"15\" cols=\"60\" wrap=\"physical\"></TEXTAREA>");
  else
    document.writeln("<TEXTAREA readonly name=\"HTMLoutput\" rows=\"15\" cols=\"70\" wrap=\"physical\"></TEXTAREA>");

  document.writeln("</CENTER></P>");
  document.writeln("</FORM>");

  ValidPosition = false;

  iold = -1;
  jold = -1;
  cold = -1;
  inew = -1;
  jnew = -1;
  cnew = -1;

  SelPiece = ' ';

  letters = null;
  document.frmBoard.epd.focus();
} // makeBoard()


function setPieceImage2(i, j, setto)
{
  switch (i)
   {
    case 0 :
             switch (j)
              {
               case 0 :
                        document.cbi_0_0.src = setto;
                        break;
               case 1 :
                        document.cbi_0_1.src = setto;
                        break;
               case 2 :
                        document.cbi_0_2.src = setto;
                        break;
               case 3 :
                        document.cbi_0_3.src = setto;
                        break;
               case 4 :
                        document.cbi_0_4.src = setto;
                        break;
               case 5 :
                        document.cbi_0_5.src = setto;
                        break;
               case 6 :
                        document.cbi_0_6.src = setto;
                        break;
               case 7 :
                        document.cbi_0_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 1 :
             switch (j)
              {
               case 0 :
                        document.cbi_1_0.src = setto;
                        break;
               case 1 :
                        document.cbi_1_1.src = setto;
                        break;
               case 2 :
                        document.cbi_1_2.src = setto;
                        break;
               case 3 :
                        document.cbi_1_3.src = setto;
                        break;
               case 4 :
                        document.cbi_1_4.src = setto;
                        break;
               case 5 :
                        document.cbi_1_5.src = setto;
                        break;
               case 6 :
                        document.cbi_1_6.src = setto;
                        break;
               case 7 :
                        document.cbi_1_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 2 :
             switch (j)
              {
               case 0 :
                        document.cbi_2_0.src = setto;
                        break;
               case 1 :
                        document.cbi_2_1.src = setto;
                        break;
               case 2 :
                        document.cbi_2_2.src = setto;
                        break;
               case 3 :
                        document.cbi_2_3.src = setto;
                        break;
               case 4 :
                        document.cbi_2_4.src = setto;
                        break;
               case 5 :
                        document.cbi_2_5.src = setto;
                        break;
               case 6 :
                        document.cbi_2_6.src = setto;
                        break;
               case 7 :
                        document.cbi_2_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 3 :
             switch (j)
              {
               case 0 :
                        document.cbi_3_0.src = setto;
                        break;
               case 1 :
                        document.cbi_3_1.src = setto;
                        break;
               case 2 :
                        document.cbi_3_2.src = setto;
                        break;
               case 3 :
                        document.cbi_3_3.src = setto;
                        break;
               case 4 :
                        document.cbi_3_4.src = setto;
                        break;
               case 5 :
                        document.cbi_3_5.src = setto;
                        break;
               case 6 :
                        document.cbi_3_6.src = setto;
                        break;
               case 7 :
                        document.cbi_3_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 4 :
             switch (j)
              {
               case 0 :
                        document.cbi_4_0.src = setto;
                        break;
               case 1 :
                        document.cbi_4_1.src = setto;
                        break;
               case 2 :
                        document.cbi_4_2.src = setto;
                        break;
               case 3 :
                        document.cbi_4_3.src = setto;
                        break;
               case 4 :
                        document.cbi_4_4.src = setto;
                        break;
               case 5 :
                        document.cbi_4_5.src = setto;
                        break;
               case 6 :
                        document.cbi_4_6.src = setto;
                        break;
               case 7 :
                        document.cbi_4_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 5 :
             switch (j)
              {
               case 0 :
                        document.cbi_5_0.src = setto;
                        break;
               case 1 :
                        document.cbi_5_1.src = setto;
                        break;
               case 2 :
                        document.cbi_5_2.src = setto;
                        break;
               case 3 :
                        document.cbi_5_3.src = setto;
                        break;
               case 4 :
                        document.cbi_5_4.src = setto;
                        break;
               case 5 :
                        document.cbi_5_5.src = setto;
                        break;
               case 6 :
                        document.cbi_5_6.src = setto;
                        break;
               case 7 :
                        document.cbi_5_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 6 :
             switch (j)
              {
               case 0 :
                        document.cbi_6_0.src = setto;
                        break;
               case 1 :
                        document.cbi_6_1.src = setto;
                        break;
               case 2 :
                        document.cbi_6_2.src = setto;
                        break;
               case 3 :
                        document.cbi_6_3.src = setto;
                        break;
               case 4 :
                        document.cbi_6_4.src = setto;
                        break;
               case 5 :
                        document.cbi_6_5.src = setto;
                        break;
               case 6 :
                        document.cbi_6_6.src = setto;
                        break;
               case 7 :
                        document.cbi_6_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    case 7 :
             switch (j)
              {
               case 0 :
                        document.cbi_7_0.src = setto;
                        break;
               case 1 :
                        document.cbi_7_1.src = setto;
                        break;
               case 2 :
                        document.cbi_7_2.src = setto;
                        break;
               case 3 :
                        document.cbi_7_3.src = setto;
                        break;
               case 4 :
                        document.cbi_7_4.src = setto;
                        break;
               case 5 :
                        document.cbi_7_5.src = setto;
                        break;
               case 6 :
                        document.cbi_7_6.src = setto;
                        break;
               case 7 :
                        document.cbi_7_7.src = setto;
                        break;
               default :
                        alert("setPieceImage2(): parameter j (" + j + ") invalid");
                        return;
              }
             break;
    default :
             alert("setPieceImage2(): parameter i (" + i + ") invalid");
             return;
   }
  return;
}


function setPieceImage(i, j)
{
  var color = ((i % 2) + j) % 2;

  switch (board[i][j])
   {
    case "K" :
         if (color != 0)
           setPieceImage2(i, j, wkw.src);
         else
           setPieceImage2(i, j, wkb.src);
         break;
    case "Q" :
         if (color != 0)
           setPieceImage2(i, j, wqw.src);
         else
           setPieceImage2(i, j, wqb.src);
         break;
    case "R" :
         if (color != 0)
           setPieceImage2(i, j, wrw.src);
         else
           setPieceImage2(i, j, wrb.src);
         break;
    case "B" :
         if (color != 0)
           setPieceImage2(i, j, wbw.src);
         else
           setPieceImage2(i, j, wbb.src);
         break;
    case "N" :
         if (color != 0)
           setPieceImage2(i, j, wnw.src);
         else
           setPieceImage2(i, j, wnb.src);
         break;
    case "P" :
         if (color != 0)
           setPieceImage2(i, j, wpw.src);
         else
           setPieceImage2(i, j, wpb.src);
         break;
    case "k" :
         if (color != 0)
           setPieceImage2(i, j, bkw.src);
         else
           setPieceImage2(i, j, bkb.src);
         break;
    case "q" :
         if (color != 0)
           setPieceImage2(i, j, bqw.src);
         else
           setPieceImage2(i, j, bqb.src);
         break;
    case "r" :
         if (color != 0)
           setPieceImage2(i, j, brw.src);
         else
           setPieceImage2(i, j, brb.src);
         break;
    case "b" :
         if (color != 0)
           setPieceImage2(i, j, bbw.src);
         else
           setPieceImage2(i, j, bbb.src);
         break;
    case "n" :
         if (color != 0)
           setPieceImage2(i, j, bnw.src);
         else
           setPieceImage2(i, j, bnb.src);
         break;
    case "p" :
         if (color != 0)
           setPieceImage2(i, j, bpw.src);
         else
           setPieceImage2(i, j, bpb.src);
         break;
    case " " :
         if (color != 0)
           setPieceImage2(i, j, efw.src);
         else
           setPieceImage2(i, j, efb.src);
         break;
    default :
         alert("Internal error in building board");
         alert("piece on square (" + i + "," + j + ") = " + board[i][j]);
         clearBoard(document.frmBoard);
         return;
   }
}


function clearBoard(form)
{
  var i = 0;
  var j = 0;

  for (i = 7; i >= 0; i--)
   {
    for (j = 0; j < 8; j++)
     {
      board[i][j] = " ";
      setPieceImage(i, j);
      }
   }

  iold = -1;
  jold = -1;
  cold = -1;
  inew = -1;
  jnew = -1;
  cnew = -1;

  ValidPosition = false;
  SelPiece      = ' ';
  form.epd.focus();
}


function isValidPosition()
{
  var valid = false;
  var wkings   = 0;
  var bkings   = 0;
  var wqueens  = 0;
  var bqueens  = 0;
  var wrooks   = 0;
  var brooks   = 0;
  var wbishops = 0;
  var bbishops = 0;
  var wknights = 0;
  var bknights = 0;
  var wpawns   = 0;
  var bpawns   = 0;
  var i = -1;
  var j = -1;

  for (i = 7; i >= 0; i--)
   {
    for (j = 0; j < 8; j++)
     {
      switch(board[i][j])
       {
        case 'K' :
                   wkings++;
                   break;
        case 'Q' :
                   wqueens++;
                   break;
        case 'R' :
                   wrooks++;
                   break;
        case 'B' :
                   wbishops++;
                   break;
        case 'N' :
                   wknights++;
                   break;
        case 'P' :
                   wpawns++;
                   break;
        case 'k' :
                   bkings++;
                   break;
        case 'q' :
                   bqueens++;
                   break;
        case 'r' :
                   brooks++;
                   break;
        case 'b' :
                   bbishops++;
                   break;
        case 'n' :
                   bknights++;
                   break;
        case 'p' :
                   bpawns++;
                   break;
       }
     }
   }

  valid = true;

  if (wkings == 0)
   {
    valid = false;
    alert("White king is missing");
   }
  else
  if (wkings > 1)
   {
    valid = false;
    alert("Too many white kings");
   }

  if (bkings == 0)
   {
    valid = false;
    alert("Black king is missing");
   }
  else
  if (bkings > 1)
   {
    valid = false;
    alert("Too many black kings");
   }

  if (wqueens > 9)
   {
    valid = false;
    alert("Too many white queens");
   }

  if (bqueens > 9)
   {
    valid = false;
    alert("Too many black queens");
   }

  if (wrooks > 10)
   {
    valid = false;
    alert("Too many white rooks");
   }

  if (brooks > 10)
   {
    valid = false;
    alert("Too many black rooks");
   }

  if (wbishops > 10)
   {
    valid = false;
    alert("Too many white bishops");
   }

  if (bbishops > 10)
   {
    valid = false;
    alert("Too many black bishops");
   }

  if (wknights > 10)
   {
    valid = false;
    alert("Too many white knights");
   }

  if (bknights > 10)
   {
    valid = false;
    alert("Too many black knights");
   }

  if (wpawns > 8)
   {
    valid = false;
    alert("Too many white pawns");
   }

  if (bpawns > 8)
   {
    valid = false;
    alert("Too many black pawns");
   }


  if ((wkings + wqueens + wrooks + wbishops + wknights + wpawns) > 16)
   {
    valid = false;
    alert("Too many white pieces on board");
   }

  if ((bkings + bqueens + brooks + bbishops + bknights + bpawns) > 16)
   {
    valid = false;
    alert("Too many black pieces on board");
   }


  for (j = 0; j < 8; j++)
   {
    if ((board[7][j] == 'P') || (board[7][j] == 'p'))
     {
      valid = false;
      alert("No pawns on 8th rank allowed");
      break;
     }
   }

  for (j = 0; j < 8; j++)
   {
    if ((board[0][j] == 'P') || (board[7][j] == 'p'))
     {
      valid = false;
      alert("No pawns on 1st rank allowed");
      break;
     }
   }


  return valid;
}


function setBoard(form)
{
  var i   = 0;
  var j   = 0;
  var k   = 0;
  var l   = 0;
  var lng = 0;
  var pos = 0;
  var c   = "";
  var empty = false;

  if (form.epd.value == "")
   {
    alert("EPD string is empty");
    form.epd.focus();
    return;
   }

  clearBoard(form);

  epd  = new String(form.epd.value);
  epd2 = new String(epd);
  lng = epd.length;
  if (lng < 15)
   {
    alert("Input is not an EPD string");
    epd = null;
    return;
   }
//alert("epd = >>" + epd + "<<");


  pos = 0;
  i   = 7;
  while (i >= 0)
   {
    j = 0;
    while (j <= 7)
     {
      pos++;
      if (pos > lng)
       {
        return;
       }

      c = epd2.substring(0,1);
      epd = epd.substring(1, lng);
      epd2 = new String(epd);

//alert("[" + i + "," + j + "]: working on " + c);

      if (c == "/")
       {
        if (j == 0)
          continue;
        break;
       }

      empty = false;
      switch (c)
       {
        case "K" :
             board[i][j] = c;
             break;
        case "Q" :
             board[i][j] = c;
             break;
        case "R" :
             board[i][j] = c;
             break;
        case "B" :
             board[i][j] = c;
             break;
        case "N" :
             board[i][j] = c;
             break;
        case "P" :
             board[i][j] = c;
             break;
        case "k" :
             board[i][j] = c;
             break;
        case "q" :
             board[i][j] = c;
             break;
        case "r" :
             board[i][j] = c;
             break;
        case "b" :
             board[i][j] = c;
             break;
        case "n" :
             board[i][j] = c;
             break;
        case "p" :
             board[i][j] = c;
             break;
        case "1" :
             k = 1;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "2" :
             k = 2;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "3" :
             k = 3;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "4" :
             k = 4;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "5" :
             k = 5;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "6" :
             k = 6;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "7" :
             k = 7;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        case "8" :
             k = 8;
             if ((j + k) > 8)
              {
               alert("invalid EPD string");
               epd  = null;
               epd2 = null;
               return;
              }
             for (l = 0; l < k; l++)
               board[i][j+l] = ' ';
             j += k;
             empty = true;
             break;
        default:
             alert(c + " - unexpected in EPD string");
             clearBoard(form);
             epd  = null;
             epd2 = null;
             return;
       }      

      if (empty)
        continue;
      j++;
     }

    i--;
   }

//alert("EPD string read");

  epd  = null;
  epd2 = null;


  for (i = 7; i >= 0; i--)
   {
    for (j = 0; j < 8; j++)
     {
      setPieceImage(i, j);
     }
   }

  if (validate)
    ValidPosition = isValidPosition();
  else
    ValidPosition = true;
  return;
}


function buildEPDstring()
{
  var i     = -1;
  var j     = -1;
  var epd   = "";
  var empty = -1;

//  alert("buildEPDstring() called");

  for (i = 7; i >= 0; i--)
   {
    empty = 0;
    for (j = 0; j < 8; j++)
     {
      if (board[i][j] != ' ')
       {
        if (empty > 0)
         {
          epd += empty;
          empty = 0;
         }
        epd += board[i][j];
       }
      else
        empty++;
     }

    if (empty > 0)
      epd += empty;
    if (i > 0)
      epd += "/";
   }

  document.frmBoard.epd.value = epd;
}


function doValidation()
{
  if (!validate)
   {
    ValidPosition = true;
    alert("Validation check switched off");
    return;
   }

  ValidPosition = isValidPosition();
  if (ValidPosition)
    alert("Position looks okay");
//  else
//    alert("Position is *not* valid !!");
}


function doInit(form)
{
  form.epd.value = "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR";
  form.epd.focus();
  setBoard(form);
}

function doReset(form)
{
  clearBoard(form);
  form.HTMLoutput.value = "";
  form.epd.value = "";
  form.epd.focus();
}


function doPick(i, j)
{
  var color;

  if (SelPiece != ' ')
   {
    board[i][j] = SelPiece;
    setPieceImage(i, j);
    SelPiece = ' ';
    ValidPosition = false;
    buildEPDstring();
    return;
   }

//  alert("i = " + i + "\n" + "j = " + j);


  color = ((i % 2) + j) % 2;

  if (iold < 0)
   {
//    alert("set old field: i = " + i + "\n" + "j = " + j);
    if (board[i][j] == ' ')
     {
      alert("No piece selected");
      return;
     }
    iold = i;
    jold = j;
    cold = color;
    pold = board[i][j];
    return;
   }

//  alert("set new field: i = " + i + "\n" + "j = " + j);
  inew = i;
  jnew = j;
  cnew = color;


  if ((inew == iold) && (jnew == jold))
   {
//    alert("delet piece on field:  i = " + i + "\n" + "j = " + j);
    board[iold][jold] = ' ';
    setPieceImage(iold, jold);
    ValidPosition = false;
    buildEPDstring();
    iold = -1;
    jold = -1;
    cold = -1;
    inew = -1;
    jnew = -1;
    cnew = -1;
    return;
   }

//  alert("iold = " + iold + " , jold = " + jold + "\n" +
//        "inew = " + inew + " , jnew = " + jnew);
  board[iold][jold] = ' ';
  board[inew][jnew] = pold;
  setPieceImage(iold, jold);
  setPieceImage(inew, jnew);
  ValidPosition = false;
  buildEPDstring();

  iold = -1;
  jold = -1;
  cold = -1;
  inew = -1;
  jnew = -1;
  cnew = -1;
}


function selPiece(c)
{
//  alert("Piece " + c + " selected");
  SelPiece = c;
}


function doCreateHTMLTable(form)
{
  var letters;

  if (validate)
   {
    if (!isValidPosition())
     {
      return;
     }
   }

  self.defaultStatus = "Create HTML output...";
  letters  = new makeLetters();

  form.HTMLoutput.value  = "<" + "!----- Begin of chess board HTML table -----" + "><BR>\n";
  form.HTMLoutput.value += "<TABLE border=\"5\" bgcolor=\"#C0C0C0\" bordercolor=\"#000000\"><BR>\n";
  form.HTMLoutput.value += "<TR>";
  form.HTMLoutput.value += "<TD>";
  form.HTMLoutput.value += "<TABLE border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><BR>\n";
  form.HTMLoutput.value += "<TR>";
  form.HTMLoutput.value += "<TD>&nbsp;</TD>";
  for (i = 0; i <= 7; i++)
      form.HTMLoutput.value += "<TD align=\"center\"><B>" + letters[i] + "</B></TD>";
  form.HTMLoutput.value += "</TR>";
  for (i = 7; i >= 0; i--)
   {
    form.HTMLoutput.value += "<TR>";
    form.HTMLoutput.value += "<TD>";
    form.HTMLoutput.value += "&nbsp;<B>" + (i + 1) + "</B>&nbsp;";
    form.HTMLoutput.value += "</TD>";
    for (j = 0; j < 8; j++)
     {
      color = ((i % 2) + j) % 2;
      form.HTMLoutput.value += "<TD height=\"38\" width=\"38\">";
      form.HTMLoutput.value += "<IMG src=\"";
      switch (board[i][j])
       {
        case "K" :
             if (color != 0)
               form.HTMLoutput.value += "wkw.gif";
             else
               form.HTMLoutput.value += "wkb.gif";
             break;
        case "Q" :
             if (color != 0)
               form.HTMLoutput.value += "wqw.gif";
             else
               form.HTMLoutput.value += "wqb.gif";
             break;
        case "R" :
             if (color != 0)
               form.HTMLoutput.value += "wrw.gif";
             else
               form.HTMLoutput.value += "wrb.gif";
             break;
        case "B" :
             if (color != 0)
               form.HTMLoutput.value += "wbw.gif";
             else
               form.HTMLoutput.value += "wbb.gif";
             break;
        case "N" :
             if (color != 0)
               form.HTMLoutput.value += "wnw.gif";
             else
               form.HTMLoutput.value += "wnb.gif";
             break;
        case "P" :
             if (color != 0)
               form.HTMLoutput.value += "wpw.gif";
             else
               form.HTMLoutput.value += "wpb.gif";
             break;
        case "k" :
             if (color != 0)
               form.HTMLoutput.value += "bkw.gif";
             else
               form.HTMLoutput.value += "bkb.gif";
             break;
        case "q" :
             if (color != 0)
               form.HTMLoutput.value += "bqw.gif";
             else
               form.HTMLoutput.value += "bqb.gif";
             break;
        case "r" :
             if (color != 0)
               form.HTMLoutput.value += "brw.gif";
             else
               form.HTMLoutput.value += "brb.gif";
             break;
        case "b" :
             if (color != 0)
               form.HTMLoutput.value += "bbw.gif";
             else
               form.HTMLoutput.value += "bbb.gif";
             break;
        case "n" :
             if (color != 0)
               form.HTMLoutput.value += "bnw.gif";
             else
               form.HTMLoutput.value += "bnb.gif";
             break;
        case "p" :
             if (color != 0)
               form.HTMLoutput.value += "bpw.gif";
             else
               form.HTMLoutput.value += "bpb.gif";
             break;
        case " " :
             if (color != 0)
               form.HTMLoutput.value += "efw.gif";
             else
               form.HTMLoutput.value += "efb.gif";
             break;
       }
      form.HTMLoutput.value += "\" width=\"38\" height=\"38\" border=\"0\">";
      form.HTMLoutput.value += "</TD>";
     }
    form.HTMLoutput.value += "<TD>";
    form.HTMLoutput.value += "&nbsp;<B>" + (i + 1) + "</B>&nbsp;";
    form.HTMLoutput.value += "</TD>";
    form.HTMLoutput.value += "</TR>";
   }
  form.HTMLoutput.value += "<TR>";
  form.HTMLoutput.value += "<TD>&nbsp;</TD>";
  for (i = 0; i <= 7; i++)
    form.HTMLoutput.value += "<TD align=\"center\"><B>" + letters[i] + "</B></TD>";
  form.HTMLoutput.value += "</TR>";
  form.HTMLoutput.value += "</TABLE>";
  form.HTMLoutput.value += "</TD>";
  form.HTMLoutput.value += "</TR>";
  form.HTMLoutput.value += "</TABLE><BR>\n";
  form.HTMLoutput.value += "<" + "!----- End of chess board HTML table -----" + "><BR>\n";

  letters = null;
  self.defaultStatus = "";
}


function doSwitchValidation(form)
{
  validate = form.cbvalid.checked;
}


function doLoadInit()
{
  document.frmBoard.selOptions.selectedIndex = 0;
  document.frmBoard.cbvalid.checked = true;
  document.frmBoard.epd.focus();
}


function doSelOption(form)
{
  var opt = form.selOptions.options[form.selOptions.selectedIndex].value;

  switch (opt)
   {
    case "clear" :
             doReset(form);
             break;
    case "html" :
             setTimeout("doCreateHTMLTable(document.frmBoard)", 100);
             break;
    case "init" :
             doInit(form);
             break;
    case "licence" :
             window.location = "jsEPD2diag.txt";
             break;
    case "none" :
             break;
    case "valid" :
             doValidation();
             break;
    default :
             alert("Internal error\nOption \"" + opt + "\" unexpected");
             break;
   }

  form.selOptions.selectedIndex = 0;
  form.epd.focus();
}
// End JavaScript -->
</SCRIPT>
<?
    $attribut_body = "onload='doLoadInit()'";
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "Outils > EPD/FEN en diagramme";
    require 'page_body.php';
?>
  <div id="contentlarge">
    <div class="blogbody">
        <SCRIPT language="JavaScript">
        <!-- Begin JavaScript
          makeBoard();
        // End JavaScript -->
        </SCRIPT>
        <HR>
        <ADDRESS>
        Copyright (c) 1998 Manfred Rosenboom (email: <A href="mailto:marochess@geocities.com">marochess@geocities.com</A>)<BR>
        WWW: <A href="http://www.geocities.com/CapeCanaveral/Launchpad/2640/chess.htm">http://www.geocities.com/CapeCanaveral/Launchpad/2640/chess.htm</A>
        </ADDRESS>

    </div>
</div>
<?
    require 'page_footer.php';
?>
