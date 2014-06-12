<?php
$data[] = array(
  "_id" => new MongoId("51a0ec0f8f604c9e22000002"),
  "name" => "London",
  "state" => "",
  "country" => array(
    '$ref' => "geolocation_country",
    '$id' => new MongoId("51a0ec0f8f604c9e22000001"),
    '$db' => $databaseName
  ),
  "latitude" => 51.5112139,
  "longitude" => -0.1198244
);
$data[] = array(
  "_id" => new MongoId("51a0ec158f604c9e22000005"),
  "name" => "Montilla",
  "state" => "Andalusia",
  "country" => array(
    '$ref' => "geolocation_country",
    '$id' => new MongoId("51a0ec0f8f604c9e22000004"),
    '$db' => $databaseName
  ),
  "latitude" => 37.5868449,
  "longitude" => -4.638967
);
$data[] = array(
  "_id" => new MongoId("51a0ec158f604c9e22000008"),
  "name" => "Medellin",
  "state" => "Antioquia",
  "country" => array(
    '$ref' => "geolocation_country",
    '$id' => new MongoId("51a0ec158f604c9e22000007"),
    '$db' => $databaseName
  ),
  "latitude" => 6.235925,
  "longitude" => -75.575137
);
$data[] = array(
  "_id" => new MongoId("51a0ec168f604c9e2200000b"),
  "name" => "New York",
  "state" => "New York",
  "country" => array(
    '$ref' => "geolocation_country",
    '$id' => new MongoId("51a0ec168f604c9e2200000a"),
    '$db' => $databaseName
  ),
  "latitude" => 40.7143528,
  "longitude" => -74.0059731
);
$data[] = array(
  "_id" => new MongoId("51a0ec178f604c9e2200000d"),
  "name" => "Barcelona",
  "state" => "Catalonia",
  "country" => array(
    '$ref' => "geolocation_country",
    '$id' => new MongoId("51a0ec0f8f604c9e22000004"),
    '$db' => $databaseName
  ),
  "latitude" => 41.3850639,
  "longitude" => 2.1734035
);
