<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./includes/transtab_latin1_bibtex.inc.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/includes/transtab_latin1_bibtex.inc.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    13-Aug-06, 13:30
	// Modified:   $Date: 2007-05-23 12:36:15 -0700 (Wed, 23 May 2007) $
	//             $Author: msteffens $
	//             $Revision: 953 $

	// This is a translation table for conversion from ISO-8859-1 to LaTeX/BibTeX entities. It contains a list of substitution strings for 'ISO-8859-1 West European' characters,
	// which can be used with the 'T1' font encoding. Uses commands from the 'textcomp' package.
	// Adopted from 'transtab' by Markus Kuhn
	// (transtab.utf v1.8 2000-10-12 11:01:28+01 mgk25 Exp); see <http://www.cl.cam.ac.uk/~mgk25/unicode.html> for more info about Unicode and transtab.

	$transtab_latin1_bibtex = array(

		// NUMBER SIGN
		"(?<!\\\\)#" => '$\\#$',
		// <U0023> <U0023>

		// PERCENT SIGN
		"(?<!\\\\)%" => "\\%",
		// <U0025> <U0025>

		// AMPERSAND
//		"(?<!\\\\)&" => "\\&", // encoding of ampersands is already handled by bibutils (which handles it better since it excludes ampersands in URLs from encoding)
		// <U0026> <U0026>

		// APOSTROPHE
		"(?<!\\\\)'" => "{\\textquoteright}",
		// <U0027> <U2019>

		// GRAVE ACCENT
		"(?<!\\\\)`" => "{\\textquoteleft}",
		// <U0060> <U201B>;<U2018>

		// NO-BREAK SPACE
		"�" => "~",
		// <U00A0> <U0020>

		// INVERTED EXCLAMATION MARK
		"�" => "{\\textexclamdown}",
		// <U00A1> <U0021>

		// CENT SIGN
		"�" => "{\\textcent}",
		// <U00A2> <U0063>

		// POUND SIGN
		"�" => "{\\textsterling}",
		// <U00A3> "<U0047><U0042><U0050>"

		// YEN SIGN
		"�" => "{\\textyen}",
		// <U00A5> <U0059>

		// BROKEN BAR
		"�" => "{\\textbrokenbar}",
		// <U00A6> <U007C>

		// SECTION SIGN
		"�" => "{\\textsection}",
		// <U00A7> <U0053>

		// DIAERESIS
		"�" => "{\\textasciidieresis}",
		// <U00A8> <U0022>

		// COPYRIGHT SIGN
		"�" => "{\\textcopyright}",
		// <U00A9> "<U0028><U0063><U0029>";<U0063>

		// FEMININE ORDINAL INDICATOR
		"�" => "{\\textordfeminine}",
		// <U00AA> <U0061>

		// LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
		"�" => "{\\guillemotleft}",
		// <U00AB> "<U003C><U003C>"

		// NOT SIGN
		"�" => "{\\textlnot}",
		// <U00AC> <U002D>

		// SOFT HYPHEN
		"�" => "-", // correct?
		// <U00AD> <U002D>

		// REGISTERED SIGN
		"�" => "{\\textregistered}",
		// <U00AE> "<U0028><U0052><U0029>"

		// MACRON
		"�" => "{\\textasciimacron}",
		// <U00AF> <U002D>

		// DEGREE SIGN
		"�" => "{\\textdegree}",
		// <U00B0> <U0020>

		// PLUS-MINUS SIGN
		"�" => "{\\textpm}",
		// <U00B1> "<U002B><U002F><U002D>"

		// SUPERSCRIPT TWO
		"�" => "{\\texttwosuperior}",
		// <U00B2> "<U005E><U0032>";<U0032>

		// SUPERSCRIPT THREE
		"�" => "{\\textthreesuperior}",
		// <U00B3> "<U005E><U0033>";<U0033>

		// ACUTE ACCENT
		"�" => "{\\textasciiacute}",
		// <U00B4> <U0027>

		// MICRO SIGN
		"�" => "{\\textmu}",
		// <U00B5> <U03BC>;<U0075>

		// PILCROW SIGN
		"�" => "{\\textparagraph}",
		// <U00B6> <U0050>

		// MIDDLE DOT
		"�" => "{\\textperiodcentered}",
		// <U00B7> <U002E>

		// CEDILLA
		"�" => "{\\c\\ }",
		// <U00B8> <U002C>

		// SUPERSCRIPT ONE
		"�" => "{\\textonesuperior}",
		// <U00B9> "<U005E><U0031>";<U0031>

		// MASCULINE ORDINAL INDICATOR
		"�" => "{\\textordmasculine}",
		// <U00BA> <U006F>

		// RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
		"�" => "{\\guillemotright}",
		// <U00BB> "<U003E><U003E>"

		// VULGAR FRACTION ONE QUARTER
		"�" => "{\\textonequarter}",
		// <U00BC> "<U0020><U0031><U002F><U0034>"

		// VULGAR FRACTION ONE HALF
		"�" => "{\\textonehalf}",
		// <U00BD> "<U0020><U0031><U002F><U0032>"

		// VULGAR FRACTION THREE QUARTERS
		"�" => "{\\textthreequarters}",
		// <U00BE> "<U0020><U0033><U002F><U0034>"

		// INVERTED QUESTION MARK
		"�" => "{\\textquestiondown}",
		// <U00BF> <U003F>

		// LATIN CAPITAL LETTER A WITH GRAVE
		"�" => "{\\`A}", // \symbol{"C0}
		// <U00C0> <U0041>

		// LATIN CAPITAL LETTER A WITH ACUTE
		"�" => "{\\'A}", // \symbol{"C1}
		// <U00C1> <U0041>

		// LATIN CAPITAL LETTER A WITH CIRCUMFLEX
		"�" => "{\\^A}", // \symbol{"C2}
		// <U00C2> <U0041>

		// LATIN CAPITAL LETTER A WITH TILDE
		"�" => "{\\~A}", // \symbol{"C3}
		// <U00C3> <U0041>

		// LATIN CAPITAL LETTER A WITH DIAERESIS
		"�" => "{\\\"A}", // \symbol{"C4}
		// <U00C4> "<U0041><U0065>";<U0041>

		// LATIN CAPITAL LETTER A WITH RING ABOVE
		"�" => "{\\r A}", // "\\AA" // \symbol{"C5}
		// <U00C5> "<U0041><U0061>";<U0041>

		// LATIN CAPITAL LETTER AE
		"�" => "{\\AE}", // \symbol{"C6}
		// <U00C6> "<U0041><U0045>";<U0041>

		// LATIN CAPITAL LETTER C WITH CEDILLA
		"�" => "{\\c C}", // \symbol{"C7}
		// <U00C7> <U0043>

		// LATIN CAPITAL LETTER E WITH GRAVE
		"�" => "{\\`E}", // \symbol{"C8}
		// <U00C8> <U0045>

		// LATIN CAPITAL LETTER E WITH ACUTE
		"�" => "{\\'E}", // \symbol{"C9}
		// <U00C9> <U0045>

		// LATIN CAPITAL LETTER E WITH CIRCUMFLEX
		"�" => "{\\^E}", // \symbol{"CA}
		// <U00CA> <U0045>

		// LATIN CAPITAL LETTER E WITH DIAERESIS
		"�" => "{\\\"E}", // \symbol{"CB}
		// <U00CB> <U0045>

		// LATIN CAPITAL LETTER I WITH GRAVE
		"�" => "{\\`I}", // \symbol{"CC}
		// <U00CC> <U0049>

		// LATIN CAPITAL LETTER I WITH ACUTE
		"�" => "{\\'I}", // \symbol{"CD}
		// <U00CD> <U0049>

		// LATIN CAPITAL LETTER I WITH CIRCUMFLEX
		"�" => "{\\^I}", // \symbol{"CE}
		// <U00CE> <U0049>

		// LATIN CAPITAL LETTER I WITH DIAERESIS
		"�" => "{\\\"I}", // \symbol{"CF}
		// <U00CF> <U0049>

		// LATIN CAPITAL LETTER ETH
		"�" => "{\\DH}", // \symbol{"D0}
		// <U00D0> <U0044>

		// LATIN CAPITAL LETTER N WITH TILDE
		"�" => "{\\~N}", // \symbol{"D1}
		// <U00D1> <U004E>

		// LATIN CAPITAL LETTER O WITH GRAVE
		"�" => "{\\`O}", // \symbol{"D2}
		// <U00D2> <U004F>

		// LATIN CAPITAL LETTER O WITH ACUTE
		"�" => "{\\'O}", // \symbol{"D3}
		// <U00D3> <U004F>

		// LATIN CAPITAL LETTER O WITH CIRCUMFLEX
		"�" => "{\\^O}", // \symbol{"D4}
		// <U00D4> <U004F>

		// LATIN CAPITAL LETTER O WITH TILDE
		"�" => "{\\~O}", // \symbol{"D5}
		// <U00D5> <U004F>

		// LATIN CAPITAL LETTER O WITH DIAERESIS
		"�" => "{\\\"O}", // \symbol{"D6}
		// <U00D6> "<U004F><U0065>";<U004F>

		// MULTIPLICATION SIGN
		"�" => "{\\texttimes}", // \symbol{"D7}
		// <U00D7> <U0078>

		// LATIN CAPITAL LETTER O WITH STROKE
		"�" => "{\\O}", // \symbol{"D8}
		// <U00D8> <U004F>

		// LATIN CAPITAL LETTER U WITH GRAVE
		"�" => "{\\`U}", // \symbol{"D9}
		// <U00D9> <U0055>

		// LATIN CAPITAL LETTER U WITH ACUTE
		"�" => "{\\'U}", // \symbol{"DA}
		// <U00DA> <U0055>

		// LATIN CAPITAL LETTER U WITH CIRCUMFLEX
		"�" => "{\\^U}", // \symbol{"DB}
		// <U00DB> <U0055>

		// LATIN CAPITAL LETTER U WITH DIAERESIS
		"�" => "{\\\"U}", // \symbol{"DC}
		// <U00DC> "<U0055><U0065>";<U0055>

		// LATIN CAPITAL LETTER Y WITH ACUTE
		"�" => "{\\'Y}", // \symbol{"DD}
		// <U00DD> <U0059>

		// LATIN CAPITAL LETTER THORN
		"�" => "{\\TH}", // \symbol{"DE}
		// <U00DE> "<U0054><U0068>"

		// LATIN SMALL LETTER SHARP S
		"�" => "{\\ss}", // \symbol{"DF}
		// <U00DF> "<U0073><U0073>";<U03B2>

		// LATIN SMALL LETTER A WITH GRAVE
		"�" => "{\\`a}", // \symbol{"E0}
		// <U00E0> <U0061>

		// LATIN SMALL LETTER A WITH ACUTE
		"�" => "{\\'a}", // \symbol{"E1}
		// <U00E1> <U0061>

		// LATIN SMALL LETTER A WITH CIRCUMFLEX
		"�" => "{\\^a}", // \symbol{"E2}
		// <U00E2> <U0061>

		// LATIN SMALL LETTER A WITH TILDE
		"�" => "{\\~a}", // \symbol{"E3}
		// <U00E3> <U0061>

		// LATIN SMALL LETTER A WITH DIAERESIS
		"�" => "{\\\"a}", // \symbol{"E4}
		// <U00E4> "<U0061><U0065>";<U0061>

		// LATIN SMALL LETTER A WITH RING ABOVE
		"�" => "{\\r a}", // "\\aa" // \symbol{"E5}
		// <U00E5> "<U0061><U0061>";<U0061>

		// LATIN SMALL LETTER AE
		"�" => "{\\ae}", // \symbol{"E6}
		// <U00E6> "<U0061><U0065>";<U0061>

		// LATIN SMALL LETTER C WITH CEDILLA
		"�" => "{\\c c}", // \symbol{"E7}
		// <U00E7> <U0063>

		// LATIN SMALL LETTER E WITH GRAVE
		"�" => "{\\`e}", // \symbol{"E8}
		// <U00E8> <U0065>

		// LATIN SMALL LETTER E WITH ACUTE
		"�" => "{\\'e}", // \symbol{"E9}
		// <U00E9> <U0065>

		// LATIN SMALL LETTER E WITH CIRCUMFLEX
		"�" => "{\\^e}", // \symbol{"EA}
		// <U00EA> <U0065>

		// LATIN SMALL LETTER E WITH DIAERESIS
		"�" => "{\\\"e}", // \symbol{"EB}
		// <U00EB> <U0065>

		// LATIN SMALL LETTER I WITH GRAVE
		"�" => "{\\`\\i}", // \symbol{"EC}
		// <U00EC> <U0069>

		// LATIN SMALL LETTER I WITH ACUTE
		"�" => "{\\'\\i}", // \symbol{"ED}
		// <U00ED> <U0069>

		// LATIN SMALL LETTER I WITH CIRCUMFLEX
		"�" => "{\\^\\i}", // \symbol{"EE}
		// <U00EE> <U0069>

		// LATIN SMALL LETTER I WITH DIAERESIS
		"�" => "{\\\"\\i}", // \symbol{"EF}
		// <U00EF> <U0069>

		// LATIN SMALL LETTER ETH
		"�" => "{\\dh}", // \symbol{"F0}
		// <U00F0> <U0064>

		// LATIN SMALL LETTER N WITH TILDE
		"�" => "{\\~n}", // \symbol{"F1}
		// <U00F1> <U006E>

		// LATIN SMALL LETTER O WITH GRAVE
		"�" => "{\\`o}", // \symbol{"F2}
		// <U00F2> <U006F>

		// LATIN SMALL LETTER O WITH ACUTE
		"�" => "{\\'o}", // \symbol{"F3}
		// <U00F3> <U006F>

		// LATIN SMALL LETTER O WITH CIRCUMFLEX
		"�" => "{\\^o}", // \symbol{"F4}
		// <U00F4> <U006F>

		// LATIN SMALL LETTER O WITH TILDE
		"�" => "{\\~o}", // \symbol{"F5}
		// <U00F5> <U006F>

		// LATIN SMALL LETTER O WITH DIAERESIS
		"�" => "{\\\"o}", // \symbol{"F6}
		// <U00F6> "<U006F><U0065>";<U006F>

		// DIVISION SIGN
		"�" => "{\\textdiv}", // \symbol{"F7}
		// <U00F7> <U003A>

		// LATIN SMALL LETTER O WITH STROKE
		"�" => "{\\o}", // \symbol{"F8}
		// <U00F8> <U006F>

		// LATIN SMALL LETTER U WITH GRAVE
		"�" => "{\\`u}", // \symbol{"F9}
		// <U00F9> <U0075>

		// LATIN SMALL LETTER U WITH ACUTE
		"�" => "{\\'u}", // \symbol{"FA}
		// <U00FA> <U0075>

		// LATIN SMALL LETTER U WITH CIRCUMFLEX
		"�" => "{\\^u}", // \symbol{"FB}
		// <U00FB> <U0075>

		// LATIN SMALL LETTER U WITH DIAERESIS
		"�" => "{\\\"u}", // \symbol{"FC}
		// <U00FC> "<U0075><U0065>";<U0075>

		// LATIN SMALL LETTER Y WITH ACUTE
		"�" => "{\\'y}", // \symbol{"FD}
		// <U00FD> <U0079>

		// LATIN SMALL LETTER THORN
		"�" => "{\\th}", // \symbol{"FE}
		// <U00FE> "<U0074><U0068>"

		// LATIN SMALL LETTER Y WITH DIAERESIS
		"�" => "{\\\"y}" // \symbol{"FF}
		// <U00FF> <U0079>

	);

?>
