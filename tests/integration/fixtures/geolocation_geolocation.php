<?php
$data[] = array(
  "_id" => new MongoId("51a0ec0f8f604c9e22000003"),
  "address" => "173 Leander Road, London Borough of Lambeth, SW2, UK",
  "post_code" => "SW2",
  "city" => array(
    '$ref' => "geolocation_city",
    '$id' => new MongoId("51a0ec0f8f604c9e22000002"),
    '$db'=> $databaseName
  ),
  "latitude" => 51.4501411,
  "longitude" => -0.1172651
);
$data[] = array(
  "_id" => new MongoId("51a0ec158f604c9e22000006"),
  "address" => "Avenida de Andalucía, 14550 Montilla, Córdoba, Spain",
  "post_code" => "14550",
  "city" => array(
    '$ref' => "geolocation_city",
    '$id' => new MongoId("51a0ec158f604c9e22000005"),
    '$db'=> $databaseName
  ),
  "latitude" => 37.5809925,
  "longitude" => -4.646874
);
$data[] = array(
  "_id" => new MongoId("51a0ec158f604c9e22000009"),
  "address" => "42, Calle 7 # 34-70, Medellín, Antioquia, Colombia",
  "post_code" => "",
  "city" => array(
    '$ref' => "geolocation_city",
    '$id' => new MongoId("51a0ec158f604c9e22000008"),
    '$db'=> $databaseName
  ),
  "latitude" => 6.2061188,
  "longitude" => -75.5651981
);
$data[] = array(
  "_id" => new MongoId("51a0ec168f604c9e2200000c"),
  "address" => "944 5th Avenue, New York, NY 10021, USA",
  "post_code" => "10021",
  "city" => array(
    '$ref' => "geolocation_city",
    '$id' => new MongoId("51a0ec168f604c9e2200000b"),
    '$db'=> $databaseName
  ),
  "latitude" => 40.774506,
  "longitude" => -73.965109
);
$data[] = array(
  "_id" => new MongoId("51a0ec178f604c9e2200000e"),
  "address" => "Carrer de los Castillejos, 373, 08025 Barcelona, Spain",
  "post_code" => "08025",
  "city" => array(
    '$ref' => "geolocation_city",
    '$id' => new MongoId("51a0ec178f604c9e2200000d"),
    '$db'=> $databaseName
  ),
  "latitude" => 41.4114199,
  "longitude" => 2.1716747
);
