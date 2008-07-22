<?php
	function WYSIWYG_Links($text) {
		$text = preg_replace(
			'#\b(https?\://[a-z0-9.-]+(/[a-zA-Z0-9./+?;&=%-]*)?)#',
			'<a href="\1">\1</a>',
			$text
		);
		return $text;
	}

	function WYSIWYG_TextProcess($text) {
		$text = htmlspecialchars($text);
		$text = WYSIWYG_Links($text);
		$text = WYSIWYG_Smileys($text);
		return $text;
	}

	function WYSIWYG_Smileys($text) {
		static $smileys = array(
			":D" => "teeth",
			":-)" => "smile",
			":)" => "smile",
			":P" => "tongue",
			":p" => "tongue",
			":-P" => "tongue",
			":-p" => "tongue",
			":-D" => "teeth",
			":-S" => "confused",
			":S" => "confused",
			":'(" => "cry", 
			":angel:" => "innocent",
			":angry:" => "angry", 
			":bat:" => "bat",
			":beer:" => "beer",
			":cake:" => "cake",
			":photo:" => "camera",
			":cat:" => "cat",
			":clock:" => "clock",
			":drink:" => "cocktail",
			":cafe:" => "cup",
			":666:" => "devil",
			":evil:" => "devil",
			":dog:" => "dog",
			":mail:" => "email",
			":email:" => "email",
			":e-mail:" => "email",
			"^^Uu" => "embarassed",
			":film:" => "film",
			":smooch:" => "kiss",
			":idea:" => "lightbulb",
			"LOL" => "lol",
			":phone:" => "phone",
			":cool:" => "shade",
			":no:" => "thumbs_down",
			":yes:" => "thumbs_up",
			":yuck:" => "tongue",
			":heartbroken:" => "unlove",
			":unlove:" => "unlove",
			":hate:" => "unlove",
			":rose:" => "wilted_rose",
			":star:" => "star",
			":X" => "uptight",
			":gift:" => "present",
			":present:" => "present",
			":love:" => "love",
			":heart:" => "love",
			":music:" => "note",
			":note:" => "note",
			":airplane:" => "airplane", 
			":boy:" => "boy",
			":car:" => "car",
			":smoke:" => "cigarette",
			":computer:" => "computer", 
			":girl:" => "girl",
			":-I" => "indifferent",
			":-|" => "indifferent",
			":island:" => "ip",
			":!!:" => "lightning",
			":sms:" => "mobile_phone",
			":wow:" => "omg",
			":-(" => "sad",
			":sheep:" => "sheep",
			":@:" => "snail",
			":ball:" => "soccer", 
			":kaboom:" => "storm",
			":sun:" => "sun",
			":turtle:" => "turtle",
			":?:" => "thinking",
			":umbrella:" => "umbrella",
			":~:" => "ugly",
			":::" => "empty"
		);
		static $smileysprocessed = false;
		static $smileysprocessedkeys = false;
		global $xc_settings;

		if ($smileysprocessed === false) {
			foreach ($smileys as $i => $smiley) {
				$smileysprocessed[$i] = '<img src=\'' 
							. $xc_settings['staticimagesurl'] 
							. 'emoticons/' 
							. $smiley 
							. '.png\' alt=\'' 
							. htmlspecialchars($i) 
							. '\' title=\'' 
							. htmlspecialchars($i) 
							. '\' class=\'emoticon\' width=\'22\' height=\'22\' />';
			}
			$smileysprocessedkeys = array_keys($smileysprocessed);
		}

		$text = str_replace($smileysprocessedkeys, $smileysprocessed, $text);
		$text = preg_replace(
			'#(^|\s);-?\)(\s|$)#',
			'<img src=\''
			. $xc_settings['staticimagesurl'] 
			. 'emoticons/wink.png\' alt=\';-)\' title=\';-)\' class=\'emoticon\' width=\'22\' height=\'22\' />',
			$text
		);
		return $text;
	}

	$tests = array(
		'http://www.google.com/',
		'Hello https://python.org/ !',
		'http://localhost/index.php?p=comments&a=show <-- look here',
		'OK https://foo.bar.gr/wiki.php?a=true&s=false ... htts://mistake.org/ http:/another.net/index.php ...'
	);

	foreach ( $tests as $t ) {
		$result = WYSIWYG_TextProcess($t);
		echo "$result <br />\n";
	}
?>

