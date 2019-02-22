<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class DistanceModelTest extends Tests\TestCase {

    use WithoutMiddleware;

    protected function setUp() {
        $this->response = $this->createMock(\App\Http\Response\Response::class);
        parent::setUp();
    }

    function testsaveDistanceWithCorrectData() {
        echo "\n <---- Unit Test saveDistance test ---> \n";
        $model = new \App\Http\Models\Distance();
        $distanceCoordinates = $this->generateGeoCordinates();
        $this->assertInstanceOf('\App\Http\Models\Distance', $model->saveDistance(
                        $distanceCoordinates['sourceLatitude'],
                        $distanceCoordinates['sourceLongitude'],
                        $distanceCoordinates['destinationLatitude'],
                        $distanceCoordinates['destinationLongitude'], $distanceCoordinates['distance']
        ));
    }

    function testcheckIfDistanceExistWithValidLatLong() {
        echo "\n <--- Unit Test checkIfDistanceExist ---> \n";
        $model = new \App\Http\Models\Distance();
        $distanceCoordinates = [
            'sourceLatitude' => '26.968046',
            'sourceLongitude' => '-94.420307',
            'destinationLatitude' => '27.33328',
            'destinationLongitude' => '-94.132006',
        ];

        $model->saveDistance($distanceCoordinates['sourceLatitude'],
                $distanceCoordinates['sourceLongitude'],
                $distanceCoordinates['destinationLatitude'],
                $distanceCoordinates['destinationLongitude'], 100);

        $this->assertInstanceOf('\App\Http\Models\Distance', $model->checkIfDistanceExist($distanceCoordinates['sourceLatitude'], $distanceCoordinates['sourceLongitude'],
                        $distanceCoordinates['destinationLatitude'], $distanceCoordinates['destinationLongitude']));
    }

    function testcheckIfDistanceExistWithInvalidLatLong() {
        echo "\n <--- Unit test  checkIfDistanceExist with no existing data NULL check---> \n";
        $model = new \App\Http\Models\Distance();
        $distanceCoordinates = [
            'sourceLatitude' => '0',
            'sourceLongitude' => '0',
            'destinationLatitude' => '0',
            'destinationLongitude' => '0',
        ];

        $this->assertNull($model->checkIfDistanceExist($distanceCoordinates['sourceLatitude'], $distanceCoordinates['sourceLongitude'],
                        $distanceCoordinates['destinationLatitude'], $distanceCoordinates['destinationLongitude']));
    }

    /**
     * @return array
     */
    protected function generateGeoCordinates() {
        $faker = Faker\Factory::create();

        $initialLatitude = $faker->latitude();
        $initialLongitude = $faker->latitude();
        $finalLatitude = $faker->longitude();
        $finalLongitude = $faker->longitude();

        $distance = $this->distance($initialLatitude, $initialLongitude, $finalLatitude, $finalLongitude);

        return [
            'sourceLatitude' => $initialLatitude,
            'sourceLongitude' => $initialLongitude,
            'destinationLatitude' => $finalLatitude,
            'destinationLongitude' => $finalLongitude,
            'distance' => $distance
        ];
    }

    /**
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     *
     * @return int
     */
    public function distance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distanceInMetre = $dist * 60 * 1.1515 * 1.609344 * 1000;

        return (int) $distanceInMetre;
    }

}
