<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Filesystem\File;
use Cake\Log\Log;
use Cake\Validation\Validator;

function getConstructor($object)
{
    if (!is_object($object)) {
        throw new Exception('Parameter must be an object');
    }

    $reflectionClass = new ReflectionClass($object);
    $constructor = $reflectionClass->getConstructor();

    return $constructor ? $constructor->getName() : null;
}

class TasksController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    protected function validateQueryParams(array $params): array
    {
        $validator = new \Cake\Validation\Validator();
    
        $validator
            ->numeric('parentId')
            ->allowEmptyString('parentId');
    
        $validator
            ->allowEmptyString('search')
            ->maxLength('search', 255, 'Name is too long');
    
        $validator
            ->inList('completed', ['true', 'false', '1', '0'])
            ->allowEmptyString('completed');
    
        $errors = $validator->validate($params);
    
        if ($errors) {
            throw new BadRequestException(json_encode($errors));
        }
    
        return $params;
    }
    
    public function index()
    {
        $queryParams = $this->validateQueryParams($this->request->getQuery());

        $query = $this->Tasks->find();
    
        // Filter by text
        $searchTerm = $queryParams['search'] ?? null;
        if ($searchTerm) {
            $query->where(['name LIKE' => '%' . $searchTerm . '%']);
        }
    
        // Filter by completed status
        $completedStatus = $this->request->getQuery('completed');
        if ($completedStatus === 'true' || $completedStatus === '1') {
            $query->where(['completed' => true]);
        } elseif ($completedStatus === 'false' || $completedStatus === '0') {
            $query->where(['completed' => false]);
        }
    
        // Filter by parentId
        $parentId = $queryParams['parentId'] ?? null;
        if ($parentId !== null) {
            if ($parentId == '0') {
                $query->where(['parent_id IS' => null]);
            } else {
                $query->where(['parent_id' => $parentId]);
            }
        }
    
        // Pagination
        $page = (int)$this->request->getQuery('page', 1); // Default to 1 if not provided
        if($page < 1) { $page = 1; }
        $pageLength = (int)$this->request->getQuery('pageLength', 20); // Default to 20 if not provided
    
        $query->limit($pageLength)->offset(($page - 1) * $pageLength);
    
        $tasks = $query->all();
    
        $this->set([
            'tasks' => $tasks,
            '_serialize' => ['tasks']
        ]);
    }

    public function view($id)
    {
        $task = $this->Tasks->get($id);
        $this->set([
            'task' => $task,
            '_serialize' => ['task']
        ]);
    }



    /**
     * Add a new task.
     *
     * @return void
     * @throws BadRequestException When validation fails.
     */
    public function add()
    {
        $task = $this->Tasks->newEntity();
        $message = 'Not added';
    
        if ($this->request->is('post')) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData(), [
                'validate' => 'add'
            ]);
    
            if ($task->hasErrors()) {
                // Handle validation errors
                throw new BadRequestException(json_encode($task->getErrors()));
            }            
    
            // Set default value for completed
            if ($task->completed === null) {
                $task->completed = false;
            }
    
            // Handle image upload
            $newImage = $this->request->getData('newImage');
            if ($newImage && $newImage['error'] == UPLOAD_ERR_OK) {
                try {
                    $filename = time() . $newImage['name'];
                    move_uploaded_file($newImage['tmp_name'], WWW_ROOT . 'img/uploads/' . $filename);
                    $task->image_path = 'img/uploads/' . $filename;
                } catch (Exception $e) {
                    Log::write('debug', 'Error moving uploaded file: ' . $e->getMessage());
                }
            } else {
                Log::write('debug', /*getConstructor($newImage),*/ $newImage);
            }
    
            if ($this->Tasks->save($task)) {
                $message = 'Added';
            } else {
                $message = 'Error';
                Log::write('debug', json_encode($task->getErrors()));
                throw new BadRequestException(json_encode($task->getErrors()));
            }
        }
    
        $this->set([
            'message' => $message,
            'task' => $task,  // Return the created task in the response
            '_serialize' => ['message', 'task']  // Include the task in the serialized response
        ]);
    }

    /**
     * Edit a task.
     *
     * @param int $id The ID of the task.
     * @return void
     * @throws BadRequestException When validation fails.
     */
    public function edit($id)
    {
        $method = $this->request->getMethod();
        $message = 'nothing done';
        if ($method === "PUT") {
            $task = $this->Tasks->get($id);
            $task = $this->Tasks->patchEntity($task, $this->request->getData(), [
                'validate' => 'edit'
            ]);
            if ($task->hasErrors()) {
                // Handle validation errors
                throw new BadRequestException(json_encode($task->getErrors()));
            }  

            // Handle image removal
            if ($this->request->getData('removeImage') === true) {
                if ($task->image_path) {
                    $file = new File(WWW_ROOT . $task->image_path);
                    if ($file->exists()) {
                        $file->delete();
                    }
                    $task->image_path = null;
                }
            } else {
                // Handle image upload
                $newImage = $this->request->getData('newImage');
                if ($newImage instanceof \Cake\Http\UploadedFile) {
                    // Delete old image
                    if ($task->image_path) {
                        $file = new File(WWW_ROOT . $task->image_path);
                        if ($file->exists()) {
                            $file->delete();
                        }
                    }
                    $filename = time() . $newImage->getClientFilename();
                    $newImage->moveTo(WWW_ROOT . 'img/uploads/' . $filename);
                    $task->image_path = 'img/uploads/' . $filename;
                }
            }

            if ($this->Tasks->save($task)) {
                $message = 'Updated';
            } else {
                $message = 'Error';
                throw new BadRequestException(json_encode($task->getErrors()));
            }
        } elseif ($method === "PATCH") {

            $data = $this->request->getData();
            $tasksValidator = $this->Tasks->validationPatch(new Validator());

            $errors = $tasksValidator->validate($data);
            if ($errors) {
                throw new BadRequestException(json_encode($errors));
            }
            Log::write('debug', '225');

            $tasksIds = $this->request->getData('tasksIds');
            $action = $this->request->getData('action');
            Log::write('debug', $tasksIds);
            $done = [];
            switch($action) {
                case 'delete':
                    if ($this->Tasks->deleteAll(['Tasks.id IN' => $tasksIds])) {
                        $done = $tasksIds;
                    } else {
                        $error = "records couldn't be removed";
                    }
                    break;
                case 'complete':
                    if(    $this->Tasks->updateAll(
                        ['completed' => $newStatus], // Fields to update
                        ['Tasks.id IN' => $tasksIds]  // Condition to match
                    )) {
                        $done = $tasksIds;
                    } else {
                        $error = "records couldn't be updated";
                    }
                    break;
                default:
                    $error = "unknown action";
            }
            if(count($done)) {
                $message = "Tasks with id " . implode($done) . " were " . $action . "d";
            }
        }

        $this->set([
            'message' => $message,
            '_serialize' => ['message']
        ]);
    }

}
