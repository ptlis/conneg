<?
include 'conNeg.inc.php';

class xhtmlNeg extends conNeg {
	static public function xhtmlOrHtml() {
		$match	= array('type'			=> array(	'text/html',
													'application/xhtml+xml'),
						'qFactorApp'	=> array(	1,
													0.9));


		$match = conNeg::mimeAll($match);

		// Default to text/html if neither field is explicitely matched
		if(($match['q_value'][0] == 0) && ($match['q_value'][1] == 0)) {
			return 'text/html';
		}
		else {
			return $match['type'][0];
		}
	}
}
?>