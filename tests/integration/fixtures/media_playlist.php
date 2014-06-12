<?php
$data[] = array(
	"_id" => new MongoId("517c01dd8f604c9812000000"),
	"name" => "Friday's night party",
	"owner" => array(
		'$ref' => "user_user",
		'$id' => new MongoId("50116a08724f9a2a0a000000"),
		'$db'=> $databaseName
	),
	"songs" => array(
	     "0" => array(
			"artist" => array(
		         '$ref' => "music_artist",
		         '$id' => new MongoId("5159c7548ead0ea619000000"),
		         '$db' => $databaseName 
	      ),
	      "song_id" => new MongoId("5175e1108f604c0b09000001") 
	    ),
	     "1" => array(
	       "artist" => array(
		     '$ref' => "music_artist",
		     '$id' => new MongoId("5159c7548ead0ea619000001"),
		     '$db' => $databaseName 
	      ),
	       "song_id" => new MongoId("5175e1108f604c0c08000001") 
    	)
	) 	
);
