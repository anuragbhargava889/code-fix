<?php

use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    public function testCreationSuccessCase()
    {
        echo "\n <----- Intergation Test Cases ----> \n";
        echo "\n <----- Order create Success Case -------> \n";

        $validData = [
            'source' => ['28.704061', '77.102493'],
            'destination' => ['28.535517','77.391029']
        ];

        $response = $this->json('POST', '/api/orders', $validData);
        $data = (array) $response->getData();
        echo "\n\t <----- assert status 200 -----> \n";
        $response->assertStatus(200);
        echo "\n\t <----- Response should have key id, status and distance ------> \n";
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('distance', $data);
    }
    
    public function testCreateWithWrongParameters()
    {
        echo "\n <--- Creating order with Invalid Parameters case 1 ----> \n";

        $invalidData1 = [
            'source' => ['28.704060', '77.102493'],
            'destination1' => ['28.535517','77.391029']
        ];

        $response = $this->json('POST', '/api/orders', $invalidData1);
        $data = (array) $response->getData();
        $response->assertStatus(406);
        
        echo "\n <---- Creating order with Invalid Parameters case 2 -------> \n";
        $invalidData = [
            'source' => ['44.968046', 'xyz1', '44.345565'],
            'destination' => ['27.535517','78.455677']
        ];

        $response = $this->json('POST', '/api/orders', $invalidData);
        $response->assertStatus(406);
    }

    public function testCreateWithEmptyParameters()
    {
        echo "\n <----- Creating order with Empty Parameters -----> \n";

        $invalidData1 = [
            'source' => ['28.704060', ''],
            'destination' => ['','77.391029']
        ];

        $response = $this->json('POST', '/api/orders', $invalidData1);
        $response->assertStatus(406);
    }

    
    public function testOrderUpdateSuccessCase()
    {
        echo "\n <----- Executing Order Update Test Cases -----> \n";

        echo "\n <----- Order Update valid case -----> \n";

        echo "\n \t <----- Creating an order -----> \n";
        $validData = [
            'source' => ['28.712340', '77.687654'],
            'destination' => ['28.713402','77.687902']
        ];

        $updateData = ['status' => 'TAKEN'];
        $response = $this->json('POST', '/api/orders', $validData);
        $data = (array) $response->getData();
        $orderId = $data['id'];

        echo "\n \t <----- Updating Order -------> \n";
        $response = $this->json('PATCH', '/api/orders/'. $orderId, $updateData);
        $data = (array) $response->getData();

        echo "\n <----- Order taken success case ------> \n";
        $response->assertStatus(200);

        echo "\n <----- Check the order update response key  -------> \n";
        $this->assertArrayHasKey('status', $data);

        echo "\n <------ test case to check order already taken success case  ----> \n";
        echo "\n Raise condition case testing \n";

        $updateData = ['status' => 'TAKEN'];

        $response = $this->json('PATCH', '/api/orders/'. $orderId, $updateData);
        $data = (array) $response->getData();

        $response->assertStatus(409);

        echo "\n \t <--- response contain the error key ---> \n";
        $this->assertArrayHasKey('error', $data);

        echo "\n <--- Invalid Params case 1 ---> \n";
        $this->orderTakeFailureInvalidParams($orderId, ['stat' => 'TAKEN'], 406);

        echo "\n <--- Invalid Params case 2 worng status value ---> \n";
        $this->orderTakeFailureInvalidParams($orderId, ['status' => 'WRONG'], 406);

        echo "\n <--- Empty Param value ---> \n";
        $this->orderTakeFailureInvalidParams($orderId, ['status' => ''], 406);

        echo "\n <--- Non numeric order id ---> \n";
        $this->orderTakeFailureInvalidParams('23A', ['status' => 'TAKEN'], 406);

        echo "\n <--- Invalid Order id ---> \n";
        $this->orderTakeFailureInvalidParams(9999999, ['status' => 'TAKEN'], 417);
    }

    protected function orderTakeFailureInvalidParams($orderId, $params, $expectedCode)
    {
        $response = $this->json('PATCH', '/api/orders/'. $orderId, $params);
        $data = (array) $response->getData();

        echo "\n \t <--- update order with different invalid params $expectedCode ---> \n";
        $response->assertStatus($expectedCode);

        echo "\n \t <---- invalid order update response key check ----> \n";
        $this->assertArrayHasKey('error', $data);
    }

    public function testOrderListSuccessCases()
    {
        echo "\n \n <----- Executing Order fetch test cases ------> \n";

        echo "\n <----- Order List Success cases ------> \n";

        $query = 'page=1&limit=4';
        $response = $this->json('GET', "/api/orders?$query", []);
        $data = (array) $response->getData();

        echo "\n <----- assert success response for order list api ----> \n";
        $response->assertStatus(200);

        echo "\n <----- Order list api success case checking count  \n";
        $this->assertLessThan(5, count($data));
        
        echo "\n\<--- validating the keys in the response -- > \n";
        foreach ($data as $order) {
            $order = (array) $order;
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('distance', $order);
            $this->assertArrayHasKey('status', $order);
        }
    }


    public function testOrderFetchFailureCases()
    {
        echo "\n <---- Invalid list case 1 ---> \n";
        $query = 'page1=1&limit=4';
        $this->orderListFailure($query, 406);

        echo "\n <---- Invalid list case 2 ---> \n";
        $query = 'page=1&limit1=4';
        $this->orderListFailure($query, 406);

        echo "\n <---- Invalid list case 3  ---->\n";
        $query = 'page=0&limit=4';
        $this->orderListFailure($query, 406);

        echo "\n <---- Invalid list case 4  ----> \n";
        $query = 'page=1&limit=0';
        $this->orderListFailure($query, 406);

        echo "\n <----- Invalid list case 5 ---->  ";
        $query = 'page=1&limit=0';
        $this->orderListFailure($query, 406);
    }

    protected function orderListFailure($query, $expectedCode)
    {
        $response = $this->json('GET', "/api/orders?$query", []);
        $data = (array) $response->getData();

        $response->assertStatus($expectedCode);
    }
}
