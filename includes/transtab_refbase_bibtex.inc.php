<?php
	// Project:    Web Reference Database (refbase) <http://www.refbase.net>
	// Copyright:  Matthias Steffens <mailto:refbase@extracts.de> and the file's
	//             original author(s).
	//
	//             This code is distributed in the hope that it will be useful,
	//             but WITHOUT ANY WARRANTY. Please see the GNU General Public
	//             License for more details.
	//
	// File:       ./includes/transtab_refbase_bibtex.inc.php
	// Repository: $HeadURL: https://refbase.svn.sourceforge.net/svnroot/refbase/trunk/includes/transtab_refbase_bibtex.inc.php $
	// Author(s):  Matthias Steffens <mailto:refbase@extracts.de>
	//
	// Created:    28-May-06, 17:01
	// Modified:   $Date: 2008-08-28 17:36:28 -0700 (Thu, 28 Aug 2008) $
	//             $Author: msteffens $
	//             $Revision: 1214 $

	// Search & replace patterns for conversion from refbase markup to LaTeX/BibTeX markup & entities. Converts refbase fontshape markup (italic, bold) into
	// LaTeX commands of the 'textcomp' package, super- and subscript as well as greek letters get converted into the respective commands in math mode.
	// You may need to adopt the LaTeX markup to suit your individual needs.
	// Notes: - when exporting MODS XML with '$convertExportDataToUTF8' set to "yes" in 'ini.inc.php', we convert refbase character markup (such as greek letters)
	//          to appropriate UTF-8 entities and let bibutils take care of the conversion to LaTeX markup; in this case, this conversion table only kicks in for
	//          refbase markup that was NOT converted to UTF-8, i.e. fontshape markup and super- and subscript text that has no matching Unicode entities
	//        - search & replace patterns must be specified as perl-style regular expression and search patterns must include the leading & trailing slashes

	$transtab_refbase_bibtex = array(

		"/\\\\_\\\\_(?!_)(.+?)\\\\_\\\\_/"  =>  '\\1', // underline is currently removed; instead, you could use '\\ul{\\1}' which requires '\usepackage{soul}'; the pattern for underline (__...__) must come before the one for italic (_..._); see note below w.r.t. backslashes before substrings
		"/\\\\_(.+?)\\\\_/"                 =>  '\\textit{\\1}', // or use '\\it{\\1}' (the backslashes before the substrings were inserted by bibutils: '_word_' gets '\_word\_')
		"/\\*\\*(.+?)\\*\\*/"               =>  '\\textbf{\\1}', // or use '\\bf{\\1}'
		"/\\[super:(.+?)\\]/i"              =>  '$^{\\1}$', // or use '\\textsuperscript{\\1}'
		"/\\[sub:(.+?)\\]/i"                =>  '$_{\\1}$', // or use '\\textsubscript{\\1}' if defined in your package
		"/\\[permil\\]/"                    =>  '{\\textperthousand}',
		"/\\[infinity\\]/"                  =>  '$\\infty$',
		"/\\[alpha\\]/"                     =>  '$\\alpha$',
		"/\\[beta\\]/"                      =>  '$\\beta$',
		"/\\[gamma\\]/"                     =>  '$\\gamma$',
		"/\\[delta\\]/"                     =>  '$\\delta$',
		"/\\[epsilon\\]/"                   =>  '$\\epsilon$',
		"/\\[zeta\\]/"                      =>  '$\\zeta$',
		"/\\[eta\\]/"                       =>  '$\\eta$',
		"/\\[theta\\]/"                     =>  '$\\theta$',
		"/\\[iota\\]/"                      =>  '$\\iota$',
		"/\\[kappa\\]/"                     =>  '$\\kappa$',
		"/\\[lambda\\]/"                    =>  '$\\lambda$',
		"/\\[mu\\]/"                        =>  '$\\mu$',
		"/\\[nu\\]/"                        =>  '$\\nu$',
		"/\\[xi\\]/"                        =>  '$\\xi$',
		"/\\[omicron\\]/"                   =>  '$o$',
		"/\\[pi\\]/"                        =>  '$\\pi$',
		"/\\[rho\\]/"                       =>  '$\\rho$',
		"/\\[sigmaf\\]/"                    =>  '$\\varsigma$',
		"/\\[sigma\\]/"                     =>  '$\\sigma$',
		"/\\[tau\\]/"                       =>  '$\\tau$',
		"/\\[upsilon\\]/"                   =>  '$\\upsilon$',
		"/\\[phi\\]/"                       =>  '$\\phi$',
		"/\\[chi\\]/"                       =>  '$\\chi$',
		"/\\[psi\\]/"                       =>  '$\\psi$',
		"/\\[omega\\]/"                     =>  '$\\omega$',
		"/\\[Alpha\\]/"                     =>  '$A$',
		"/\\[Beta\\]/"                      =>  '$B$',
		"/\\[Gamma\\]/"                     =>  '$\\Gamma$',
		"/\\[Delta\\]/"                     =>  '$\\Delta$',
		"/\\[Epsilon\\]/"                   =>  '$E$',
		"/\\[Zeta\\]/"                      =>  '$Z$',
		"/\\[Eta\\]/"                       =>  '$H$',
		"/\\[Theta\\]/"                     =>  '$\\Theta$',
		"/\\[Iota\\]/"                      =>  '$I$',
		"/\\[Kappa\\]/"                     =>  '$K$',
		"/\\[Lambda\\]/"                    =>  '$\\Lambda$',
		"/\\[Mu\\]/"                        =>  '$M$',
		"/\\[Nu\\]/"                        =>  '$N$',
		"/\\[Xi\\]/"                        =>  '$\\Xi$',
		"/\\[Omicron\\]/"                   =>  '$O$',
		"/\\[Pi\\]/"                        =>  '$\\Pi$',
		"/\\[Rho\\]/"                       =>  '$R$',
		"/\\[Sigma\\]/"                     =>  '$\\Sigma$',
		"/\\[Tau\\]/"                       =>  '$T$',
		"/\\[Upsilon\\]/"                   =>  '$\\Upsilon$',
		"/\\[Phi\\]/"                       =>  '$\\Phi$',
		"/\\[Chi\\]/"                       =>  '$X$',
		"/\\[Psi\\]/"                       =>  '$\\Psi$',
		"/\\[Omega\\]/"                     =>  '$\\Omega$',
		"/^(?=(URL|LOCATION|NOTE|KEYWORDS)=)/mi" =>  'opt'

	);

?>
