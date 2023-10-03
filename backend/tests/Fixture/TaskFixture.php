<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TaskFixture extends TestFixture
{
    public $import = ['table' => 'tasks'];

    private $uploads = ['cascabel.jpeg', 'chinchilla.jpg', 'chuna.jpg', 'guanaco.jpeg', 'nandu.jpg'];

    public $records = [];

    public function init()
    {
        for ($i = 1; $i <= 50; $i++) {
            $this->records[] = [
                'id' => $i,
                'name' => 'Task ' . $i,
                'completed' => ($i % 2) == 0, // Alternate between completed and not completed
                'parent_id' => ($i % 5) == 0 ? null : $i % 5, // Some tasks will have null parentId
                'image' => 'img/uploads/' . array_rand($this->uploads),
            ];
        }
        parent::init();
    }
}

