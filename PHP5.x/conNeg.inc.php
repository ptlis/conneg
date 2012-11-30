<?php

/****************************************************************************
	*																		*
	*	Version: content_negotiation.inc.php v2.0.2 2012-01-01				*
	*	Copyright: (c) 2006-2012 ptlis										*
	*	Licence: GNU Lesser General Public License v2.1						*
	*	The current version of this library can be sourced from:			*
	*		http://ptlis.net/source/php/content-negotiation/#downloads		*
	*	Contact the author of this library at:								*
	*		ptlis@ptlis.net													*
	*																		*
	*	Provides a simple API through which content negotiation is			*
	*	performed on HTTP fields.											*
	*																		*
	*	This class requires PHP 5.x (it has been tested on 5.0.x - 5.3		*
	*	with error reporting set to E_ALL | E_STRICT without problems).		*
	*																		*
	***************************************************************************/


	class conNeg {

/**	@name	Specificness of quality factor match.
 *
 *	Used for handling the precidence of matches, exact matches override subtype
 *	wildcard matches (mime negotiation only) which override type wildcard
 *	matches.
 *
 *	@{
 */
		const	WILDCARD_DEFAULT	= -1;
		const	WILDCARD_TYPE		= 0;
		const	WILDCARD_SUBTYPE	= 1;
		const	WILDCARD_NONE		= 2;

/**	@}
 *
 *	@name	 Correctness of accept-extension match.
 *
 *	Used in the handling of ua-defined types without an accept-extension
 *	specified still matching app-defined types with an accept-extension. Quality
 *	factors of ua-defined and app-defined types with matching accept-extensions
 *	override type matches where the ua-defined type has no accept-extension.
 *
 *	@{
 */

		const	EXTENS_DEFAULT		= -1;
		const	EXTENS_NONE			= 0;
		const	EXTENS_MATCH		= 1;

/**	@} */

/**	@brief	Generic parser function called by all other field-specific
 *			functions.
 *
 *	@param	$field			The contents of the field being parsed.
 *
 *	@param	$returnType		Whether the function should return only the best
 *							type or the generated array of types and preferences.
 *
 *	@param	$rawAppTypes	(Optional, defaults to null) This data is used to
 *							augment the user agent quality factors in making
 *							decisions as to which type to serve.
 *
 *	When provided with a string, it is parsed as a field and a mutli-dimensional
 *	array in the below format is generated.
 *
 *	@note	although it is parsed as a field obviously wildcards cannot
 *			be used.
 *
 *  When provided with a specially constructed multi-dimensional array (in the
 *	format below) the data structure is used directly.
 *
 *	The datastructure is in the form:
 *
 *	$appTypes	= array('type'			=> array('', '', ...),
 *						'qFactorApp'	=> array('', '', ...);
 *
 *	The array indices are a contiguous sequence beginning at 0 and increment by
 *	1 for each additional type. The array indices for a given type / quality
 *	factor pair match.
 *
 *	@param	$productVals	If true the function calculates the product of the
 *							application and user agent quality factors and
 *							orders the types by this value. If false the array
 *							is sorted by user agent quality factors and the
 *							application quality factors are used only when the
 *							user agent assigns the same quality factor to two
 *							types. Defaults to true.
 *
 *	@param	$mimeNeg		Indicates that the negotiation being performed is on
 *							the Accept field, and as such different rules are
 *							used than for the other types in order to handle
 *							media ranges being in the form type/subtype where
 *							either subtype or both of them can be a wildcard.
 *							Defaults to false.
 *
 *	@retval	false on error (eg unable to parse the field)
 *	@retval	string containing the preferred type if $returnType is 'best'.
 *	@retval	array containing true and a second array, itself containing the all
 *			generated data if $returnType is 'all'.
 */
		static private function genericNeg($field, $returnType, $rawAppTypes=null, $productVals=true, $mimeNeg=false) {
			// List of accept-extension tokens, required for mime negotiation
			// sorting
			$userAcceptExtens		= array();
			$appAcceptExtens		= array();

			$userNonWildcardProvided	= false;
			$appNonWildcardProvided		= false;

			$field		= strtolower($field);

			// Attempt to parse field into multi-dimensional array.
			list($userTypes, $userAcceptExtens, $userNonWildcardProvided) = self::parseField($field, $mimeNeg);
			if(!$userTypes) {
				return false;
			}

			// Normalise application provided data structure.
			if(is_array($rawAppTypes) && count($rawAppTypes)) {
				$appVals			= true;
				$appTypes			= self::normaliseAppData($rawAppTypes, $mimeNeg);
			}

			// Parse application provided string as field.
			else if($rawAppTypes) {

				list($appTypes, $appAcceptExtens, $appNonWildcardProvided) = self::parseField($rawAppTypes, $mimeNeg, false);

				// Unable to parse string as field
				if(!$appTypes) {
					$appVals		= false;
					$appTypes		= array();
				}

				// Normalise parsed datastructure
				else {
					$appVals		= true;
					$appTypes		= self::normaliseAppData($appTypes, $mimeNeg);
				}
			}

			else {
				$appVals			= false;
				$appTypes			= array();
			}

			// Only continue processing field if there are non-wildcard types provided either by the User-Agent or Application.
			if($appNonWildcardProvided || $userNonWildcardProvided) {

				// Iterate through user agent types.
				for($i = 0; $i < count($userTypes['type']); $i++) {

					// No application type data was provided, store all non-wildcard
					// types.
					if(!$appVals) {
						if($mimeNeg && ($userTypes['mimeType'][$i] == '*' || ($userTypes['mimeSubtype'][$i] == '*'))
								|| !$mimeNeg && $userTypes['type'][$i] == '*') {
							continue;
						}

						$appTypes['type'][$i]			= $userTypes['type'][$i];
						$appTypes['qFactorUser'][$i]	= $userTypes['qFactorUser'][$i];

						// Handle the accept-extension tokens for mime neg
						if($mimeNeg) {
							$appTypes['acceptExtens'][$i]	= $userTypes['acceptExtens'][$i];
							// Store any accept-extension tokens
							for($j = 0; $j < count($userAcceptExtens); $j++) {
								$appTypes[$userAcceptExtens[$j]][$i]	= $userTypes[$userAcceptExtens[$j]][$i];
							}
						}
					}

					// Set UA q factor as apprpriate following rules of specificness
					// (match > mime subtype wildcard match > wildcard match)
					else {

						// Exact match for the current type
						if(($key = array_search($userTypes['type'][$i], $appTypes['type'])) !== false) {
							// Mime negotation requires accept-extension handling
							if($mimeNeg) {
								// Handle ua-provided type with no accept-extens
								if($appTypes['extensMatch'][$key] == self::EXTENS_DEFAULT && !$appTypes['acceptExtens'][$key]) {
									$appTypes['specificness'][$key]	= self::WILDCARD_NONE;
									$appTypes['qFactorUser'][$key]	= $userTypes['qFactorUser'][$i];
									$appTypes['extensMatch'][$key]	= self::EXTENS_NONE;
								}

								// Handle accept-extension match
								else if($appTypes['acceptExtens'][$key] && $appTypes[$appTypes['acceptExtens'][$key]][$key] == $appTypes[$appTypes['acceptExtens'][$key]][$key]) {
									$appTypes['specificness'][$key]	= self::WILDCARD_NONE;
									$appTypes['qFactorUser'][$key]	= $userTypes['qFactorUser'][$i];
									$appTypes['extensMatch'][$key]	= self::EXTENS_MATCH;
								}
							}
							else {
								$appTypes['specificness'][$key]	= self::WILDCARD_NONE;
								$appTypes['qFactorUser'][$key]	= $userTypes['qFactorUser'][$i];
							}
						}

						// Mime neg is being performed with a type and subtype
						// wildcard or any other negotiation is being performed with
						// a type wildcard.
						else if($mimeNeg && $userTypes['mimeType'][$i] == '*' && $userTypes['mimeSubtype'][$i] == '*'
								|| (!$mimeNeg && $userTypes['type'][$i] == '*')) {

							// Iterate through all app types updating the q factors
							// if no user type has yet matched them.
							for($j = 0; $j < count($appTypes['type']); $j++) {
								if($appTypes['specificness'][$j] == self::WILDCARD_DEFAULT) {
									$appTypes['specificness'][$j]	= self::WILDCARD_TYPE;
									$appTypes['qFactorUser'][$j]	= $userTypes['qFactorUser'][$i];
								}
							}
						}

						// Mime negotiation is being performed and the subtype is
						// a wildcard, iterate through $appTypes updating the q
						// factors of all app types that haven't yet been exactly
						// matched.
						else if($mimeNeg && $userTypes['mimeSubtype'] == '*') {
							for($j = 0; $j < count($appTypes['type']); $j++) {
								if($appTypes['specificness'][$j] <= self::WILDCARD_TYPE) {
									$appTypes['specificness'][$j]	= self::WILDCARD_SUBTYPE;
									$appTypes['qFactorUser'][$j]	= $userTypes['qFactorUser'][$i];
								}
							}
						}
					}
				}

				// Calculate product of q factors
				if($productVals && $appVals) {
					for($i = 0; $i < count($appTypes['type']); $i++) {
						$appTypes['qFactorProduct'][$i]	= $appTypes['qFactorUser'][$i] * $appTypes['qFactorApp'][$i];
					}
				}

				// Sort the datastructure
				self::sortTypes($appTypes, $productVals, $appVals, $appAcceptExtens);

				// Cleanup working data from the datastructure
				unset($appTypes['mimeType']);
				unset($appTypes['mimeSubtype']);
				unset($appTypes['specificness']);
				unset($appTypes['acceptExtens']);
				unset($appTypes['extensMatch']);

				if(array_key_exists('accepExtens', $appTypes)) {
					for($i = 0; $i < count($appTypes['acceptExtens']); $i++) {
						for($j = 0; $j < count($appAcceptExtens); $j++) {
							if($appTypes[$appAcceptExtens[$j]][$i] === null) {
								unset($appTypes[$appAcceptExtens[$j]][$i]);
							}
						}
					}
				}

				// Return appropriate data
				switch($returnType) {
					case 'all':
						return $appTypes;
						break;
					case 'best':
						return $appTypes['type'][0];
						break;
					default:
						return false;
						break;
				}
			}

			else {
				return false;
			}
		}


/**	@brief	Parses the HTTP field into a multi-dimensional array.
 *
 *	When the Accept field is being parsed the first returned array is in the
 *	form:
 *
 *						$types	= array('type' => array('', ...),
 *										'qFactorUser' => array('', ...),
 *										'mimeType' => array('', ...),
 *										'mimeSubtype' => array('', ...);
 *
 *	In addition, accept-extension data is added to this array.
 *
 *	When all other fields are parsed the first returned array is in the form:
 *
 *						$types	= array('type' => array('', ...),
 *										'qFactorUser' => array('', ...));
 *
 *	The array indices are a contiguous sequence beginning at 0 and increment by
 *	1 for each additional type. The array indices for a given type and quality
 *	factor (for the Accept field mimeType and mimeSubtype too) must match.
 *
 *	@param	$field		The contents of the field being parsed.
 *
 *	@param	$mimeNeg	Indicates that the negotiation being performed is on the
 *						Accept field.
 *
 *	@param	$ua$uaField	If true the field being parsed is from the user agent
 *						otherwise it is from the application (the quality
 *						factors are stored under a different index).
 *
 *	@retval	An array with the first element containing false if unable to parse
 *			the field.
 *	@retval	An array containing a multi-dimensional array of type data and an
 *			array of accept-extension parameters if parsing an Accept field, use
 *			list($types, $extens) = conNeg::parseField(); to push the result of
 *			the function into two arrays.
 */
		static protected function parseField($field, $mimeNeg, $uaField=true) {
			$matches			= array();	// Raw result of regular expression.
			$types				= array();	// Generated array of type data.
			$acceptExtens		= array();	// Generated array of accept-extension tokens
			$fullMimeProvided	= false;	// Whether a full (non-wildcard) mime is provided in $field

			if($uaField) {
				$qFactor	= 'qFactorUser';
			}
			else {
				$qFactor	= 'qFactorApp';
			}

			// Mime types are a special case due to the possibility of subtypes & accept-extension fragmets
			if($mimeNeg) {
				if(!preg_match_all('/([a-z\-\+\*]+)\/([a-z\-\+\*]+)\s*;?\s*(:?(?:q=(0\.\d{1,5}|1\.0|[01])\s*?;?\s*?)|(?:([a-z]+)=(")?([0-9a-z\-\+\.]+)\s*?;?\s*?)\\6?){0,},*/i', $field, $matches)) {
					return array(false, array());
				}

				// Generate a list of accept-extensions found in the Accept
				// field
				$extensArr	= array();	// If the extension
				for($i = 0; $i < count($matches[0]); $i++) {
					if(strlen($matches[5][$i]) && !array_key_exists($matches[5][$i], $extensArr)) {
						$extensArr[$matches[5][$i]]	= 1;
						$acceptExtens[]	= $matches[5][$i];
					}
				}

				// Normalise generated data structure
				for($i = 0; $i < count($matches[0]); $i++) {

					// Check to see if this mime has no wildcard component
					if($matches[1][$i] != '*' && $matches[2][$i] != '*') {
						$fullMimeProvided	= true;
					}

					$types['type'][$i]	= $matches[1][$i] . '/' . $matches[2][$i];

					// Empty quality factors default to 1
					if($matches[4][$i] != null) {
						$types[$qFactor][$i]	= $matches[4][$i];
					}
					else {
						$types[$qFactor][$i]	= 1;
					}

					// These values are only required when handling wildcards
					if($uaField) {
						$types['mimeType'][$i]		= $matches[1][$i];
						$types['mimeSubtype'][$i]	= $matches[2][$i];
					}

					// By default indicate that no accept-extension tokens were found
					$types['acceptExtens'][$i]	= false;

					// Iterate through found accept-extensions and either store
					// the provided one, or set that index to store null (required
					// for sorting function).
					for($j = 0; $j < count($acceptExtens); $j++) {
						// The current token was found
						if($matches[5][$i] == $acceptExtens[$j]) {
							$types['acceptExtens'][$i]		= $acceptExtens[$j];
							$types[$acceptExtens[$j]][$i]	= $matches[7][$i];
						}

						// Otherwise default the key to null
						else {
							$types[$acceptExtens[$j]][$i]	= null;
						}
					}
				}
			}

			// Charset, Language and Encoding can be handled together
			else {
				if(!preg_match_all('/([a-z\-0-9\*]+)\s*;?\s*q?=?(0\.\d{1,5}|1\.0|[01])?,*/i', $field, $matches)) {
					return array(false, array());
				}

				// Normalise generated data structure
				for($i = 0; $i < count($matches[0]); $i++) {

					// Check to see if this mime has no wildcard component
					if($matches[1][$i] != '*') {
						$fullMimeProvided	= true;
					}

					$types['type'][$i]	= $matches[1][$i];
					if($matches[2][$i] != null) {
						$types[$qFactor][$i]	= $matches[2][$i];
					}
					else {
						$types[$qFactor][$i]	= 1;
					}
				}
			}

			return array($types, $acceptExtens, $fullMimeProvided);
		}


/**	@brief	Normalises the type data provided by the application.
 *
 *	Ensures that all required elements of the multi-dimensional array of app
 *	data have been initialised to default values and the types are all converted
 *	to lower case. If negotiation is being performed on the Accept field then
 *	the mimeType and mimeSubtype elements are set.
 *
 *	@param	$appTypes	Application provided data-structure.
 *
 *	@param 	$mimeNeg	Negotiation is being performed on the Accept field.
 *
 *	@retval	normalised application datastructure.
 */
		static private function normaliseAppData(array $appTypes, $mimeNeg) {
			for($i = 0; $i < count($appTypes['type']); $i++) {			// Set default values (and make all lower case)
				$appTypes['type'][$i]			= strtolower($appTypes['type'][$i]);
				$appTypes['qFactorUser'][$i]	= 0;
				$appTypes['specificness'][$i]	= self::WILDCARD_DEFAULT;
				$appTypes['extensMatch'][$i]	= self::EXTENS_DEFAULT;
				$appTypes['acceptExtens'][$i]	= null;
				if($mimeNeg) {
					$type_parts	= explode('/', $appTypes['type'][$i]);
					$appTypes['mimeType'][$i]		= $type_parts[0];
					$appTypes['mimeSubtype'][$i]	= $type_parts[1];
				}
			}
			return $appTypes;
		}


/**	@brief	Sorts the type data.
 *
 *	The datastructure is sorted in decreasing order of preference; the data at
 *	key 0 is the preferred type.
 *
 *	The sort call is generated procedurally as generated datastructure varies
 *	dependant upon the field the negotiation is being performed on, if the
 *	application provides quality factors etc.
 *
 *	@param	$types				The datastructure generated by the application
 *								as it parses the fields.
 *	@param	$appVals			True if the application provided quality
 *								factors.
 *	@param	$productVals		If the application provides quality factors
 *								determines if the array is sorted by the product
 *								of them and the user agent's.
 *	@param	$appAcceptExtens	An array of accept-extension paramaters provided
 *								by the application (empty if none are provided).
 */
		static private function sortTypes(&$types, $productVals, $appVals, $appAcceptExtens) {

			$sortCall	= 'array_multisort(';
			if($appVals) {
				if($productVals) {
					$sortCall	.= '$types["qFactorProduct"], SORT_DESC, SORT_NUMERIC';
				}
				else {
					$sortCall	.= '$types["qFactorUser"], SORT_DESC, SORT_NUMERIC';
					$sortCall	.= ', $types["qFactorApp"], SORT_DESC, SORT_NUMERIC';
				}
			}
			else {
				$sortCall	.= '$types["qFactorUser"], SORT_DESC, SORT_NUMERIC';
			}

			if(count($appAcceptExtens)) {
				$sortCall	.= ', $types["extensMatch"], SORT_DESC, SORT_NUMERIC';
				foreach($appAcceptExtens as $extens) {
					$sortCall	.= ', $types["' . $extens . '"]';
				}
			}

			if($productVals) {
				$sortCall	.= ', $types["qFactorUser"]';
				if($appVals) {
					$sortCall	.= ', $types["qFactorApp"]';
				}
			}

			$sortCall	.= ', $types["type"]);';
			eval($sortCall);
		}


/**	@brief	Parses the Accept field and returns the best type.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval the best type to serve.
 */
		static public function mimeBest($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT'], 'best', $appTypes, $productVals, true);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept field and returns a sorted array of types and
 *			quality factors.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval sorted array of types and quality factors.
 */
		static public function mimeAll($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT'], 'all', $appTypes, $productVals, true);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept-Charset field and returns the best type.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval the best type to serve.
 */
		static public function charBest($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT_CHARSET'], 'best', $appTypes, $productVals);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept-Charset field and returns a sorted array of types and
 *			quality factors.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval sorted array of types and quality factors.
 */
		static public function charAll($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT_CHARSET'], 'all', $appTypes, $productVals);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept-Encoding field and returns the best type.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval the best type to serve.
 */
		static public function encBest($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT_ENCODING'], 'best', $appTypes, $productVals);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept-Encoding field and returns a sorted array of types and
 *			quality factors.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval sorted array of types and quality factors.
 */
		static public function encAll($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT_ENCODING'], 'all', $appTypes, $productVals);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept-Language field and returns the best type.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval the best type to serve.
 */
		static public function langBest($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'best', $appTypes, $productVals);
			}
			else {
				return false;
			}
		}


/**	@brief	Parses the Accept-Language field and returns a sorted array of types and
 *			quality factors.
 *
 *	@param	$appTypes		An array or string of application-provided quality
 *							factors.
 *	@param	$productVals	Mutiply the application and user-agent quality
 *							factors and sort the types by this.
 *
 *	@retval	false if unable to parse the field.
 *	@retval sorted array of types and quality factors.
 */
		static public function langAll($appTypes=null, $productVals=true) {
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				return self::genericNeg($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'all', $appTypes, $productVals);
			}
			else {
				return false;
			}
		}
}
