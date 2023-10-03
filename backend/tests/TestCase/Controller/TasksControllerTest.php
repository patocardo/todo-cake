<?php
namespace App\Test\TestCase\Controller;

use App\Controller\TasksController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;


class TasksControllerTest extends TestCase
{
    use IntegrationTestTrait;
    private $apipath = '/api/tasks';
    public $fixtures = ['app.Task'];

    public function setUp(): void
    {
        parent::setUp();
        $this->Tasks = TableRegistry::getTableLocator()->get('Tasks');
    }

    public function tearDown(): void
    {
        unset($this->Tasks);
        parent::tearDown();
    }

    public function testIndex()
    {
        $this->get($this->apipath . '.json');
        $this->assertResponseOk();
        $responseBody = (string)$this->_response->getBody();
        $response = json_decode($responseBody, true);        
        foreach ($response['tasks'] as $task) {
            $this->assertContains('Task', $task['name']);
        }
    }
    public function testIndexWithSearch()
    {
        $this->get($this->apipath . '.json?search=' . urlencode('Task 4'));
        $this->assertResponseOk();
        $responseBody = (string)$this->_response->getBody();
        $response = json_decode($responseBody, true);        
        foreach ($response['tasks'] as $task) {
            $this->assertContains('Task 4', $task['name']);
        }
    }
    
    public function testIndexWithParentId()
    {
        $this->get($this->apipath . '.json?parentId=1');
        $this->assertResponseOk();
        $responseBody = (string)$this->_response->getBody();
        $response = json_decode($responseBody, true);        
        foreach ($response['tasks'] as $task) {
            $this->assertEquals(1, $task['parent_id']);
        }
    }
    

    public function testIndexWithCompleted()
    {
        $this->get($this->apipath . '.json?completed=true');
        $this->assertResponseOk();
        $responseBody = (string)$this->_response->getBody();
        $response = json_decode($responseBody, true);       
        foreach ($response['tasks'] as $task) {
            $this->assertTrue($task['completed']);
        }
    }

    public function testView()
    {
        $this->get($this->apipath . '/1.json');
        $this->assertResponseOk();
    }

    public function testAdd()
    {
        $data = [
            'name' => 'Test Task',
            'completed' => false,
            'video_link' => 'https://www.youtube.com/watch?v=YPzl_DZqzFA',
            // Simulate file upload for image
            'newImage' => [
                'tmp_name' => TESTS . 'Fixture/mara.png',
                'type' => 'image/png',
                'size' => 1024,
                'error' => 0,
                'name' => 'test_image.png'
            ]
        ];
    
        $this->post($this->apipath . '.json', $data);
        $this->assertResponseSuccess();
    
        // Decode the JSON response
        $responseBody = (string)$this->_response->getBody();
        $responseData = json_decode($responseBody, true);
    
        // Assert that the data from the created task is present in the response
        $this->assertEquals('Test Task', $responseData['task']['name']);
        $this->assertFalse($responseData['task']['completed']);
        $this->assertEquals('https://www.youtube.com/watch?v=YPzl_DZqzFA', $responseData['task']['video_link']);
        // You can also assert the image path if needed, but it might be dynamic based on how it's saved
    
        // Check that the task was saved in the database
        $query = $this->Tasks->find()->where(['name' => 'Test Task']);
        $this->assertEquals(1, $query->count());
    }    

    public function testEdit()
    {

        $this->enableCsrfToken();
    
        $data = [
            'name' => 'Updated Task',
        ];
        $this->put($this->apipath . '/1.json', $data);
        $this->assertResponseSuccess();
    
        $query = $this->Tasks->find()->where(['name' => 'Updated Task']);
        $this->assertEquals(1, $query->count());
    }

    public function testComplete()
    {
        $this->patch($this->apipath. '/1.json', ['tasksIds' => [1], 'action' => 'complete']);
        $this->assertResponseSuccess();
        
        $tasksTable = TableRegistry::getTableLocator()->get('Tasks');
        $task = $tasksTable->get(1);

        $this->assertTrue($task->completed, 'Task with id=1 is not marked as completed');
    }

    public function testDelete()
    {
        $this->patch($this->apipath. '/1.json', ['tasksIds' => [1], 'action' => 'delete']);
        $this->assertResponseSuccess();
        
        $query = $this->Tasks->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }
    
    public function testAddWithInvalidUrl()
    {
        $data = [
            'name' => 'Test Task with Invalid URL',
            'completed' => false,
            'video_link' => 'not_a_valid_url', // Invalid URL
        ];

        $this->post($this->apipath . '.json', $data);
        
        // Expecting a bad request response due to validation failure
        $this->assertResponseCode(400);
    }

}
