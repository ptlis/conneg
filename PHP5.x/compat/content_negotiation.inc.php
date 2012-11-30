<?php

	require_once '../conNeg.inc.php';

/**	@brief	Wrapper for backwards compatibility with v1.x api.
 *
 */
	class content_negotiation {

		static private function app_array_old_to_new(array $app_types) {
			for($i = 0; $i < count($app_types['app_preference']); $i++) {
				$app_types['qFactorApp'][$i]	= $app_types['app_preference'][$i];
			}
			unset($app_types['app_preference']);
			return $app_types;
		}


		static private function app_array_new_to_old(array $app_types) {
			for($i = 0; $i < count($app_types['qFactorApp']); $i++) {

				$app_types['app_preference'][$i]	= $app_types['qFactorApp'][$i];
				$app_types['q_value'][$i]			= $app_types['qFactorUser'][$i];
				if(array_key_exists('qFactorProduct', $app_types)) {
					$app_types['aggregate'][$i]		= $app_types['qFactorProduct'][$i];
				}
			}
			unset($app_types['qFactorApp']);
			unset($app_types['qFactorUser']);
			unset($app_types['qFactorProduct']);
			return $app_types;
		}


		static public function mime_best_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return conNeg::mimeBest($app_types, $aggregate_vals);
		}


		// return the whole array of mime-types
		static public function mime_all_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return self::app_array_new_to_old(conNeg::mimeAll($app_types, $aggregate_vals));
		}


		// return only the preferred charset
		static public function charset_best_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return conNeg::charBest($app_types, $aggregate_vals);
		}


		// return the whole array of charsets
		static public function charset_all_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return self::app_array_new_to_old(conNeg::charAll($app_types, $aggregate_vals));
		}


		// return only the preferred encoding-type
		static public function encoding_best_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return conNeg::encBest($app_types, $aggregate_vals);
		}


		// return the whole array of encoding-types
		static public function encoding_all_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return self::app_array_new_to_old(conNeg::encAll($app_types, $aggregate_vals));
		}


		// return only the preferred language
		static public function language_best_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return conNeg::langBest($app_types, $aggregate_vals);
		}


		// return the whole array of language
		static public function language_all_negotiation(array $app_types=array(), $aggregate_vals=false) {
			$app_types	= self::app_array_old_to_new($app_types);
			return self::app_array_new_to_old(conNeg::langAll($app_types, $aggregate_vals));
		}

	}
?>