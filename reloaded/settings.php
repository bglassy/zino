<?php
    return array(
        'applicationname' => 'Zino',
        'rootdir'         => '/var/www/zino.gr/beta/reloaded',
        'resourcesdir'    => '/var/www/zino.gr/beta/resources',
        'imagesurl'       => 'http://static.zino.gr/images/',
        'production'      => false,
        'hostname'        => 'beta.zino.gr',
        'url'             => '/reloaded',
        'port'            => 80,
        'webaddress'      => 'https://beta.zino.gr/reloaded',
        'timezone'        => 'UTC',
        'language'        => 'el',
        'databases'       => array( // prefix all keys with "db"
            'db' => array(
                'name'     => 'ccbetareloaded', // reloaded sandbox
                'driver'   => 'mysql',
                'hostname' => 'localhost',
                'username' => 'ccbeta',
                'password' => 'IkJ84nZT',
                'charset'  => 'DEFAULT',
                'prefix'   => 'merlin_',
                'tables'   => array(
                	'articles'      	=> 'articles',
                	'bans'          	=> 'ipban',
                	'bulk'          	=> 'bulk',
                	'categories'    	=> 'categories',
                	'chats'         	=> 'chat',
                	'comments'      	=> 'comments',
                    'callisto_subscriptions' => 'callisto_subscriptions',
                    'dictionaries'      => 'dictionaries',
                    'dictionarywords'   => 'dictionarywords',
                	'faqquestions'  	=> 'faqquestions',
                	'faqcategories'		=> 'faqcategories',
                    'friendrel'		    => 'friendrel',
                	'images'        	=> 'images',
                    'interesttags'      => 'interesttags',
					'latestimages'		=> 'latestimages',
                	'logs'          	=> 'logs',
                	'memcachesql'   	=> 'memcache',
					'pmfolders'			=> 'pmfolders',
					'pmmessageinfolder' => 'pmmessageinfolder',
					'pmmessages'		=> 'pmmessages',
                	'pageviews'     	=> 'pageviews',
                	'places'        	=> 'places',
					'profileanswers' 	=> 'profilea',
                	'polls'         	=> 'polls',
                	'polloptions'   	=> 'polloptions',
                	'questions'     	=> 'profileq',
                	'relations'     	=> 'relations',
                	'revisions'     	=> 'revisions',
                	'ricons'        	=> 'ricons',
                	'searches'      	=> 'searches',
                	'shoutbox'      	=> 'shoutbox',
                	'starring'     		=> 'starring',
                	'templates'     	=> 'templates',
                	'userbans'      	=> 'userban',
                	'users'         	=> 'users',
                	'usershout'     	=> 'usershout',
                	'userspaces'    	=> 'articles',
                	'usrevisions'   	=> 'revisions',
                	'exvars'        	=> 'vars',
                	'votes'         	=> 'votes',
                	'albums'        	=> 'albums',
                	'notify'        	=> 'notify',
					'universities'		=> 'universities',
					'loginattempts'		=> 'loginattempts'
                )
            )
        )
    );
?>
