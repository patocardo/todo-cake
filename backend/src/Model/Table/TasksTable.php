<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TasksTable extends Table
{
    public function initialize(array $config): void
    {
        $this->setTable('tasks');
        $this->setPrimaryKey('id');
        
        $this->hasMany('Subtasks', [
            'className' => 'Tasks',
            'foreignKey' => 'parent_id'
        ]);
        
        $this->belongsTo('ParentTasks', [
            'className' => 'Tasks',
            'foreignKey' => 'parent_id'
        ]);
    }
    
    public function validationAdd(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name', 'Name is required')
            ->minLength('name', 3, 'Name is too short')
            ->maxLength('name', 255, 'Name is too long');

        $validator
            ->inList('completed', [0, 1], 'The provided value is invalid')
            ->allowEmptyString('completed');

        $validator
            ->numeric('parentId')
            ->allowEmptyString('parentId');

        $validator
            ->allowEmptyString('newImage');

        $validator
            ->allowEmptyString('video_link')
            ->url('video_link', 'Invalid URL format');

        return $validator;
    }

    public function validationEdit(Validator $validator)
    {
        $validator
            ->numeric('id')
            ->notEmptyString('id');

        $validator
            ->allowEmptyString('name')
            ->minLength('name', 3, 'Name is too short')
            ->maxLength('name', 255, 'Name is too long');

        $validator
            ->boolean('completed')
            ->allowEmptyString('completed');

        $validator
            ->numeric('parentId')
            ->allowEmptyString('parentId');

        $validator
            ->allowEmptyString('video_link')
            ->url('video_link', 'Invalid URL format');

        return $validator;
    }

    public function validationPatch(Validator $validator)
    {
        $validator
            ->requirePresence('tasksIds', 'create', 'tasksIds field is required')
            ->isArray('tasksIds', 'tasksIds must be an array')
            ->notEmpty('tasksIds', 'tasksIds cannot be empty');

        $validator
            ->requirePresence('action', 'create', 'action field is required')
            ->inList('action', ['delete', 'complete'], 'action must be one of "delete" or "complete"');

        return $validator;
    }
}
