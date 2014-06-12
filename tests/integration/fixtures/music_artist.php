<?php
$data[] = array(
	'_id' => new MongoId("5159c7548ead0ea619000000"),
	"name" => "Nirvana",
	"about_me" => "The best band of the 90'",
	"addresses" => array(
		array(
			"street" =>  "944 5th Avenue",
			"geolocation" => array(
				'$ref' => "geolocation_geolocation",
				'$id' => new MongoId("51a0ec168f604c9e2200000c"),
				'$db' => $databaseName
			)
		)
    ),
	"genres" => array(
		0 => array(
			'$ref'=> "music_genre",
			'$id'=> new MongoId("5159c7548ead0ea619000333"),
			'$db'=> $databaseName
		)
	),
	"usm" => array(
		"date_sigup" => Date('2013-02-02'),
		"active" => "yes"	
	),
	"songs" =>array(
		   0 => array(
		       '_id' => new MongoId("5175e1108f604c0b09000001"),
		       "artist" => "Soziedad Alkoholika",
		       "title" => "Pauso Bat",
		       "album" => "Rarezas",
		       "file" => "pausobat.mp3",
		       "url" => "\/url\/url\/",
		       "plays" => 3
	    ),
		   1 => array(
		       '_id' => new MongoId("5175e1108f604c0b09000002"),
		       "artist" => "Boikot",
		       "title" => "Revolucion",
		       "album" => "10 metros bajo el suelo",
		       "file" => "10metros.mp3",
		       "url" => "\/url\/url\/",
		       "plays" => 7 
	    )
	)
);

$data[] = array(
	'_id' => new MongoId("5159c7548ead0ea619000001"),
	"name" => "Pearl Jam",
	"about_me" => "The second band of the 90'",
	"addresses" => array(
		array(
			"street" =>  "955 East",
			"geolocation" => array(
				'$ref' => "geolocation_geolocation",
				'$id' => new MongoId("51a0ec168f604c9e2200000c"),
				'$db' => $databaseName
			)
		)
    ),
	"genres" => array(
		0 =>array(
			'$ref'=> "music_genre",
			'$id'=> new MongoId("5159c7548ead0ea619000333"),
			'$db'=> $databaseName
		),
		1 => array(
			'$ref'=> "music_genre",
			'$id'=> new MongoId("5159c7548ead0ea619000444"),
			'$db'=> $databaseName
		),
	),
	"songs" =>array(
		   0 => array(
		       '_id' => new MongoId("5175e1108f604c0c08000001"),
		       "artist" => "Segismundo Toxicomano",
		       "title" => "Realidad",
		       "album" => "1,2,3 fuego",
		       "file" => "Realidad.mp3",
		       "url" => "\/url\/url\/",
		       "plays" => 9
	    ),
	)
);



$data[] = array(
	"addresses" => array(
		array(
			"street" =>  "",
			"geolocation" => array(
				'$ref' => "geolocation_geolocation",
				'$id' => new MongoId("51a0ec158f604c9e22000009"),
				'$db' => $databaseName
			)
		)
    ),
  "genres" =>array ( 
		array(
			'$ref'=> "music_genre",
      		'$id'=> new MongoId("5159c7548ead0ea619000444"),
 			'$db' => $databaseName	
    ),
    0=>array
    (
      '$ref'=> "music_genre",
      '$id'=> new MongoId("5159c7548ead0ea619000555"),
	  '$db' => $databaseName    ),
    ),
  
  "name"=> "Kiriath",
  "owner"=> array(
		'$ref'=> "user_user",
		'$id'=> new MongoId("51829a338ead0e0006000000"),
		'$db' => $databaseName
  		),
  "phonenumbers"=> array
    (
		array(
		      "phonenumber"=> "2345"
		)
    )
  ,
  "songs"=> array
    (
      '_id'=> new MongoId("5182b4598ead0e0706000000"),
      "album"=> "demo inicial",
      "artist"=> "Kiriath",
      "original_filename"=> "kiriath-odiarte ( Demo version ).mp3",
      "plays"=> 0,
      "title"=> "Odiarte",
      "url"=> "\/media\/artist\/51829aa58ead0e2d0c000000\/audio\/5182b4598ead0e0706000000.mp3"
    )
);
