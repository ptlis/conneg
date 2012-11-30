<?
include 'content_negotiation.inc.php';

class xhtml_html extends content_negotiation {
	static public function xhtml_or_html() {
		$content_match	= array('type'			=> array('text/html',
									'application/xhtml+xml'),
					'app_preference'	=> array(1,
									0.9));


		$content_match = content_negotiation::mime_all_negotiation($content_match);
		if(($content_match['q_value'][0] == 0) && ($content_match['q_value'][1] == 0)) {
			return 'text/html';
		}
		else {
			return $content_match['type'][0];
		}
	}
}
?>