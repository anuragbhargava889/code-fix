<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class OrderServiceTest extends Tests\TestCase {

    use WithoutMiddleware;

    protected $distanceHelper;
    protected $routeCoordinatesValidator;

    protected function setUp() {
        $this->distanceHelper = $this->createMock(\App\Helpers\DistanceHelper::class);
        $this->routeCoordinatesValidator = $this->createMock(\App\Validators\RouteCoordinatesValidator::class);
        parent::setUp();
    }

    public function testCreateOrderInvalidCoordinates() {
        echo "\n <---- Unit Test - order service create - considering invalid coordinates ----> \n";

        $distanceCoordinates = $this->generateGeoCordinates();

        $this->routeCoordinatesValidator->method('validate')->with(
                $distanceCoordinates['source'][0],
                $distanceCoordinates['source'][1],
                $distanceCoordinates['destination'][0],
                $distanceCoordinates['destination'][1]
        )->willReturn(false);

        $orderService = new \App\Http\Services\OrderService($this->routeCoordinatesValidator, $this->distanceHelper);

        $this->assertEquals(false, $orderService->create((object) $distanceCoordinates));
    }

    public function testCreateOrderValidCoordinates() {
        echo "\n <--- Unit Test - Service::order - Method:create - considering valid GEO data ---> \n";

        $distanceCoordinates = $this->generateGeoCordinates();

        $source = implode(',', $distanceCoordinates['source']);
        $destination = implode(',', $distanceCoordinates['destination']);

        $this->routeCoordinatesValidator->method('validate')->with(
                $distanceCoordinates['source'][0],
                $distanceCoordinates['source'][1],
                $distanceCoordinates['destination'][0],
                $distanceCoordinates['destination'][1]
        )->willReturn(true);

        $this->distanceHelper->method('getDistance')->with(
                $source,
                $destination
        )->willReturn($distanceCoordinates['distance']);

        $orderService = new \App\Http\Services\OrderService($this->routeCoordinatesValidator, $this->distanceHelper);

        $this->assertInstanceOf('\App\Http\Models\Order', $orderService->create((object) $distanceCoordinates));
    }

    public function testCreateOrderInValidDistanceCalculation() {
        echo "\n <--- Unit Test - Service::order - Method:create - considering invalid response from Google map API ---> \n";

        $distanceCoordinates = $this->generateGeoCordinates();

        $source = implode(',', $distanceCoordinates['source']);
        $destination = implode(',', $distanceCoordinates['destination']);

        $this->routeCoordinatesValidator->method('validate')->with(
                $distanceCoordinates['source'][0],
                $distanceCoordinates['source'][1],
                $distanceCoordinates['destination'][0],
                $distanceCoordinates['destination'][1]
        )->willReturn(true);

        $this->distanceHelper->method('getDistance')->with(
                $source,
                $destination
        )->willReturn('GOOGLE_API_NULL_RESPONSE');

        $orderService = new \App\Http\Services\OrderService($this->routeCoordinatesValidator, $this->distanceHelper);

        $this->assertEquals(false, $orderService->create((object) $distanceCoordinates));
    }

    public function testGetList() {

        $orderService = new \App\Http\Services\OrderService($this->routeCoordinatesValidator, $this->distanceHelper);

        echo "\n <--- Unit Test - Service::order - Method:getList - With Invalid page variables ---> \n";
        $this->assertEquals([], $orderService->getList('A12', 2));

        echo "\n <--- Unit Test - Service::order - Method:getList - With Invalid limit variables ---> \n";
        $this->assertEquals([], $orderService->getList(2, 'XZY'));

        echo "\n <--- Unit Test - Service::order - Method:getList - With Valid page=1 and limit=5 variables ---> \n";
        $response = $orderService->getList(1, 5);

        echo "\n <--- Response Type should be an array ---> \n";
        $this->assertInternalType('object', $response);

        echo "\n <--- Response should count less than or equal to 5 ---> \n";
        $this->assertLessThanOrEqual(5, count($response));
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
            'source' => [$initialLatitude, $initialLongitude],
            'destination' => [$finalLatitude, $finalLongitude],
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
