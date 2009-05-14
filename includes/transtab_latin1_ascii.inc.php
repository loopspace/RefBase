<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./includes/transtab_latin1_ascii.inc.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/includes/transtab_latin1_ascii.inc.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    24-Aug-05, 20:11
	// Modified:   $Date: 2007-05-23 12:36:15 -0700 (Wed, 23 May 2007) $
	//             $Author: msteffens $
	//             $Revision: 953 $

	// This is a transliteration table for a best-effort conversion from ISO-8859-1 to ASCII. It contains a list of substitution strings for 'ISO-8859-1 West European' characters,
	// comparable to the fallback notations that people use commonly in email and on typewriters to represent unavailable characters. Adopted from 'transtab' by Markus Kuhn
	// (transtab.utf v1.8 2000-10-12 11:01:28+01 mgk25 Exp); see <http://www.cl.cam.ac.uk/~mgk25/unicode.html> for more info about Unicode and transtab.

	$transtab_latin1_ascii = array(

		// APOSTROPHE
		"'" => "'",
		// <U0027> <U2019>

		// GRAVE ACCENT
		"`" => "'",
		// <U0060> <U201B>;<U2018>

		// NO-BREAK SPACE
		"�" => " ",
		// <U00A0> <U0020>

		// INVERTED EXCLAMATION MARK
		"�" => "!",
		// <U00A1> <U0021>

		// CENT SIGN
		"�" => "c",
		// <U00A2> <U0063>

		// POUND SIGN
		"�" => "GBP",
		// <U00A3> "<U0047><U0042><U0050>"

		// YEN SIGN
		"�" => "Y",
		// <U00A5> <U0059>

		// BROKEN BAR
		"�" => "|",
		// <U00A6> <U007C>

		// SECTION SIGN
		"�" => "S",
		// <U00A7> <U0053>

		// DIAERESIS
		"�" => "\"",
		// <U00A8> <U0022>

		// COPYRIGHT SIGN
		"�" => "(c)", // "c"
		// <U00A9> "<U0028><U0063><U0029>";<U0063>

		// FEMININE ORDINAL INDICATOR
		"�" => "a",
		// <U00AA> <U0061>

		// LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
		"�" => "<<",
		// <U00AB> "<U003C><U003C>"

		// NOT SIGN
		"�" => "-",
		// <U00AC> <U002D>

		// SOFT HYPHEN
		"�" => "-",
		// <U00AD> <U002D>

		// REGISTERED SIGN
		"�" => "(R)",
		// <U00AE> "<U0028><U0052><U0029>"

		// MACRON
		"�" => "-",
		// <U00AF> <U002D>

		// DEGREE SIGN
		"�" => " ",
		// <U00B0> <U0020>

		// PLUS-MINUS SIGN
		"�" => "+/-",
		// <U00B1> "<U002B><U002F><U002D>"

		// SUPERSCRIPT TWO
		"�" => "^2", // "2"
		// <U00B2> "<U005E><U0032>";<U0032>

		// SUPERSCRIPT THREE
		"�" => "^3", // "3"
		// <U00B3> "<U005E><U0033>";<U0033>

		// ACUTE ACCENT
		"�" => "'",
		// <U00B4> <U0027>

		// MICRO SIGN
		"�" => "u",
		// <U00B5> <U03BC>;<U0075>

		// PILCROW SIGN
		"�" => "P",
		// <U00B6> <U0050>

		// MIDDLE DOT
		"�" => ".",
		// <U00B7> <U002E>

		// CEDILLA
		"�" => ",",
		// <U00B8> <U002C>

		// SUPERSCRIPT ONE
		"�" => "^1", // "1"
		// <U00B9> "<U005E><U0031>";<U0031>

		// MASCULINE ORDINAL INDICATOR
		"�" => "o",
		// <U00BA> <U006F>

		// RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
		"�" => ">>",
		// <U00BB> "<U003E><U003E>"

		// VULGAR FRACTION ONE QUARTER
		"�" => " 1/4",
		// <U00BC> "<U0020><U0031><U002F><U0034>"

		// VULGAR FRACTION ONE HALF
		"�" => " 1/2",
		// <U00BD> "<U0020><U0031><U002F><U0032>"

		// VULGAR FRACTION THREE QUARTERS
		"�" => " 3/4",
		// <U00BE> "<U0020><U0033><U002F><U0034>"

		// INVERTED QUESTION MARK
		"�" => "?",
		// <U00BF> <U003F>

		// LATIN CAPITAL LETTER A WITH GRAVE
		"�" => "A",
		// <U00C0> <U0041>

		// LATIN CAPITAL LETTER A WITH ACUTE
		"�" => "A",
		// <U00C1> <U0041>

		// LATIN CAPITAL LETTER A WITH CIRCUMFLEX
		"�" => "A",
		// <U00C2> <U0041>

		// LATIN CAPITAL LETTER A WITH TILDE
		"�" => "A",
		// <U00C3> <U0041>

		// LATIN CAPITAL LETTER A WITH DIAERESIS
		"�" => "Ae", // "A"
		// <U00C4> "<U0041><U0065>";<U0041>

		// LATIN CAPITAL LETTER A WITH RING ABOVE
		"�" => "Aa", // "A"
		// <U00C5> "<U0041><U0061>";<U0041>

		// LATIN CAPITAL LETTER AE
		"�" => "AE", // "A"
		// <U00C6> "<U0041><U0045>";<U0041>

		// LATIN CAPITAL LETTER C WITH CEDILLA
		"�" => "C",
		// <U00C7> <U0043>

		// LATIN CAPITAL LETTER E WITH GRAVE
		"�" => "E",
		// <U00C8> <U0045>

		// LATIN CAPITAL LETTER E WITH ACUTE
		"�" => "E",
		// <U00C9> <U0045>

		// LATIN CAPITAL LETTER E WITH CIRCUMFLEX
		"�" => "E",
		// <U00CA> <U0045>

		// LATIN CAPITAL LETTER E WITH DIAERESIS
		"�" => "E",
		// <U00CB> <U0045>

		// LATIN CAPITAL LETTER I WITH GRAVE
		"�" => "I",
		// <U00CC> <U0049>

		// LATIN CAPITAL LETTER I WITH ACUTE
		"�" => "I",
		// <U00CD> <U0049>

		// LATIN CAPITAL LETTER I WITH CIRCUMFLEX
		"�" => "I",
		// <U00CE> <U0049>

		// LATIN CAPITAL LETTER I WITH DIAERESIS
		"�" => "I",
		// <U00CF> <U0049>

		// LATIN CAPITAL LETTER ETH
		"�" => "D",
		// <U00D0> <U0044>

		// LATIN CAPITAL LETTER N WITH TILDE
		"�" => "N",
		// <U00D1> <U004E>

		// LATIN CAPITAL LETTER O WITH GRAVE
		"�" => "O",
		// <U00D2> <U004F>

		// LATIN CAPITAL LETTER O WITH ACUTE
		"�" => "O",
		// <U00D3> <U004F>

		// LATIN CAPITAL LETTER O WITH CIRCUMFLEX
		"�" => "O",
		// <U00D4> <U004F>

		// LATIN CAPITAL LETTER O WITH TILDE
		"�" => "O",
		// <U00D5> <U004F>

		// LATIN CAPITAL LETTER O WITH DIAERESIS
		"�" => "Oe", // "O"
		// <U00D6> "<U004F><U0065>";<U004F>

		// MULTIPLICATION SIGN
		"�" => "x",
		// <U00D7> <U0078>

		// LATIN CAPITAL LETTER O WITH STROKE
		"�" => "O",
		// <U00D8> <U004F>

		// LATIN CAPITAL LETTER U WITH GRAVE
		"�" => "U",
		// <U00D9> <U0055>

		// LATIN CAPITAL LETTER U WITH ACUTE
		"�" => "U",
		// <U00DA> <U0055>

		// LATIN CAPITAL LETTER U WITH CIRCUMFLEX
		"�" => "U",
		// <U00DB> <U0055>

		// LATIN CAPITAL LETTER U WITH DIAERESIS
		"�" => "Ue", // "U"
		// <U00DC> "<U0055><U0065>";<U0055>

		// LATIN CAPITAL LETTER Y WITH ACUTE
		"�" => "Y",
		// <U00DD> <U0059>

		// LATIN CAPITAL LETTER THORN
		"�" => "Th",
		// <U00DE> "<U0054><U0068>"

		// LATIN SMALL LETTER SHARP S
		"�" => "ss",
		// <U00DF> "<U0073><U0073>";<U03B2>

		// LATIN SMALL LETTER A WITH GRAVE
		"�" => "a",
		// <U00E0> <U0061>

		// LATIN SMALL LETTER A WITH ACUTE
		"�" => "a",
		// <U00E1> <U0061>

		// LATIN SMALL LETTER A WITH CIRCUMFLEX
		"�" => "a",
		// <U00E2> <U0061>

		// LATIN SMALL LETTER A WITH TILDE
		"�" => "a",
		// <U00E3> <U0061>

		// LATIN SMALL LETTER A WITH DIAERESIS
		"�" => "ae", // "a"
		// <U00E4> "<U0061><U0065>";<U0061>

		// LATIN SMALL LETTER A WITH RING ABOVE
		"�" => "aa", // "a"
		// <U00E5> "<U0061><U0061>";<U0061>

		// LATIN SMALL LETTER AE
		"�" => "ae", // "a"
		// <U00E6> "<U0061><U0065>";<U0061>

		// LATIN SMALL LETTER C WITH CEDILLA
		"�" => "c",
		// <U00E7> <U0063>

		// LATIN SMALL LETTER E WITH GRAVE
		"�" => "e",
		// <U00E8> <U0065>

		// LATIN SMALL LETTER E WITH ACUTE
		"�" => "e",
		// <U00E9> <U0065>

		// LATIN SMALL LETTER E WITH CIRCUMFLEX
		"�" => "e",
		// <U00EA> <U0065>

		// LATIN SMALL LETTER E WITH DIAERESIS
		"�" => "e",
		// <U00EB> <U0065>

		// LATIN SMALL LETTER I WITH GRAVE
		"�" => "i",
		// <U00EC> <U0069>

		// LATIN SMALL LETTER I WITH ACUTE
		"�" => "i",
		// <U00ED> <U0069>

		// LATIN SMALL LETTER I WITH CIRCUMFLEX
		"�" => "i",
		// <U00EE> <U0069>

		// LATIN SMALL LETTER I WITH DIAERESIS
		"�" => "i",
		// <U00EF> <U0069>

		// LATIN SMALL LETTER ETH
		"�" => "d",
		// <U00F0> <U0064>

		// LATIN SMALL LETTER N WITH TILDE
		"�" => "n",
		// <U00F1> <U006E>

		// LATIN SMALL LETTER O WITH GRAVE
		"�" => "o",
		// <U00F2> <U006F>

		// LATIN SMALL LETTER O WITH ACUTE
		"�" => "o",
		// <U00F3> <U006F>

		// LATIN SMALL LETTER O WITH CIRCUMFLEX
		"�" => "o",
		// <U00F4> <U006F>

		// LATIN SMALL LETTER O WITH TILDE
		"�" => "o",
		// <U00F5> <U006F>

		// LATIN SMALL LETTER O WITH DIAERESIS
		"�" => "oe", // "o"
		// <U00F6> "<U006F><U0065>";<U006F>

		// DIVISION SIGN
		"�" => ":",
		// <U00F7> <U003A>

		// LATIN SMALL LETTER O WITH STROKE
		"�" => "o",
		// <U00F8> <U006F>

		// LATIN SMALL LETTER U WITH GRAVE
		"�" => "u",
		// <U00F9> <U0075>

		// LATIN SMALL LETTER U WITH ACUTE
		"�" => "u",
		// <U00FA> <U0075>

		// LATIN SMALL LETTER U WITH CIRCUMFLEX
		"�" => "u",
		// <U00FB> <U0075>

		// LATIN SMALL LETTER U WITH DIAERESIS
		"�" => "ue", // "u"
		// <U00FC> "<U0075><U0065>";<U0075>

		// LATIN SMALL LETTER Y WITH ACUTE
		"�" => "y",
		// <U00FD> <U0079>

		// LATIN SMALL LETTER THORN
		"�" => "th",
		// <U00FE> "<U0074><U0068>"

		// LATIN SMALL LETTER Y WITH DIAERESIS
		"�" => "y"
		// <U00FF> <U0079>

	);

?>
