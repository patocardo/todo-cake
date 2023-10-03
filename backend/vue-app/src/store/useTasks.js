import { provide, ref } from 'vue'
import axios from 'axios';
import { TASKS_ENDPOINT } from '@/constants/api';

/**
 * @typedef {Object} Task
 * 
 * @property {(number|string)=} id - The ID of the task, which can be a number or a UUID. Optional.
 * @property {string} name - The name of the task.
 * @property {boolean} completed - Indicates whether the task is completed.
 * @property {(number|string|null)} parentId - The ID of the parent task, which can be a number, a UUID, or null.
 * @property {File=} newImage - The image to upload to be associated with the task. Optional.
 * @property {boolean=} removeImage - The image associated with the task. Optional.
 * @property {string=} image_path - The image associated with the task. Optional.
 * @property {string=} video_link - The URL of the video associated with the task. Optional.
 * @property {boolean} selected - UI variable.
 * @property {boolean} folded - UI variable.
 * @property {array} children - UI variable.
 */

function findDescendants(taskId, tree) {
    let descendants = [];
    
    for (const task of tree) {
        if (task.parentId === taskId) {
            descendants.push(task.id);
            descendants = descendants.concat(findDescendants(task.id, tree));
        }
    }
    
    return descendants;
}

function getBaseTask(parentId) {
    return {...{
        parent_id: parentId,
        name: '',
        image_path: '',
        video_link: '',
        completed: false,
        newImage: null,
        removeImage: false,
        selected: false,
        folded: true,
        children: [],
    }}
}

/**
 * State hook for tasks operations
 */
export default function useTasks(handleError, displayConfirmation) {
    const tasks = ref([]);
    const currentTask = ref(getBaseTask(null));
    const loading = ref(false);
    const displayPanel = ref(''); // 'view'|'edit'|'add'

    // internal functions
    
    /**
     * Add loaded elements into the tasks list
     * @param {Array<Task>} newList 
     */
    const mergeTasks = (newList) => {
        let result = [...tasks.value];
    
        newList.forEach(newItem => {
            const found = result.find(({id}) => id === newItem.id);
            if (found) {
                Object.assign(found, newItem);
            } else {
                result.push(newItem);
            }
        });
    
        tasks.value = result;
    }

    // Local state
    const setSubtasksSelected = (taskId, selected) => {
        const descendants = findDescendants(taskId, tasks.value);
        if(!descendants.length) return;
        tasks.value = tasks.value.map((task) => ({
            ...task,
            selected: descendants.includes(task.id) ? selected : task.selected,
        }));   
    }

    const toggleTaskSelected = (taskId, selected) => {
        tasks.value = tasks.value.map((task) =>({
            ...task,
            selected: task.id === taskId ? selected : task.selected,
        }));
        if(selected) {
            setSubtasksSelected(taskId, selected);
        }
    }

    const getSelectedIds = () => {
        return tasks.value.filter(({ selected }) => selected).map(({id}) => id);
    }

    /**
     * set the values to make visible the panel for adding a task
     * @param {object} task with image and video values
     */
    const setViewTaskOn = (task) => {
        currentTask.value = task;
        displayPanel.value = 'view';
    }

    /**
     * set the values to make visible the panel for adding a task
     * @param {ID} parentId 
     */
    const setAddTaskOn = (parentId) => {
        currentTask.value = getBaseTask(parentId);
        displayPanel.value = 'add';
    }

    /**
     * set the values to make visible the panel for adding a task
     * @param {object} task with image and video values
     */
    const setEditTaskOn = (task) => {
        currentTask.value = task;
        displayPanel.value = 'edit';
    }   

    /**
     * set value for hide side panel
     */
    const setPanelOff = () => {
        displayPanel.value = '';
    }

    // Server data

    /**
     * load more data from the task
     * @param {number} taskId 
     * @returns {object | null}
     */
    const fetchTaskData = async(taskId) => {
        loading.value = true;
        let data = null;
        try {
            displayPanel.value = 'view';
            const response = await axios.get(`${TASKS_ENDPOINT}/${taskId}.json`);
            if(response.error) throw new Error(response.error);
            if(!response.data?.task) throw new Error('Bad response form');
            mergeTasks([response.data.task]);
            data = response.data.task;
        } catch(error) {
            handleError(error, 'Failed to fetch more tasks.');
        } finally {
            loading.value = false;
        }
        return data;
    }

    /**
     * load list of tasks into the task list
     * @param {number} taskId 
     * @returns {object | null}
     */
    const fetchTasks = async(params) => {
        loading.value = true;
        let success = false;
        const { parentId = 0, page = 1, search = ''} = params;
        try {
            const response = await axios.get(`${TASKS_ENDPOINT}.json`, {
                params: {
                    parentId,
                    page,
                    search,
                    ...(params.completed !== undefined ? { completed: params.completed} : {}),
                }
            });
            if(response.error) throw new Error(response.error);
            if(!Array.isArray(response.data?.tasks)) throw new Error('Bad data format');
            // if it is a new search, refresh the list
            if(!parentId && page === 1) {
                tasks.value = response.data.tasks;
            } else {
                mergeTasks(response.data.tasks);
            }
            success = true;
        } catch (error) {
            handleError(error, 'Failed to fetch more tasks.');
        } finally {
            loading.value = false;
        }
        return success;
    }

    /**
     * toggle the view of subtasks. If it will unfold, it loads the content
     * @param {number|string} taskId
     * @returns {Promise<boolean>} 
     */
    const toggleTaskFold = async (taskId) => {
        const task = tasks.value.find((task) => (task.id !== taskId));
        if(task.folded) {
            mergeTasks([{...task, folded: !task.folded}]);
            return true;
        }
        const fetched = await fetchTasks({ parentId: taskId });
        if(!fetched) return false;
        mergeTasks([{...task, folded: !task.folded}]);
        return true;
    };

    /**
     * Creates a task in database
     * @param {Task} taskData 
     * @returns {Promise<number | null>}
     */
    const addTask = async(taskData) => {
        loading.value = false;
        let newId = null;
        try {
            const formData = new FormData();
            for (const key in taskData) {
                const value = (() => {
                    if(typeof taskData[key] === 'boolean') {
                        return taskData[key] ? 1 : 0;
                    }
                    return taskData[key]
                })()
                formData.append(key, value);
            }
            const response = await axios.post(`${TASKS_ENDPOINT}.json`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            if(response.error) throw new Error(response.error);
            if(!response.data?.task) throw new Error('Bad response form');
            tasks.value.push(response.data.task);
            newId = response.data.task.id;
        } catch (error) {
            handleError(error, 'Error adding task.');
        } finally {
            loading.value = false;
        }
        return newId;
    }

    /**
     * Updates a task in database
     * @param {Task} taskData 
     * @returns {Promise<boolean>}
     */
    const updateTask = async(taskData) => {
        loading.value = false;
        let success = false;
        try {
            const formData = new FormData();
            for (const key in taskData) {
                formData.append(key, taskData[key]);
            }
            const response = await axios.put(`${TASKS_ENDPOINT}/${taskData.id}`, formData);
            if(response.error) throw new Error(response.error);
            mergeTasks([taskData]);
        } catch (error) {
            handleError(error, 'Error adding task.');
        } finally {
            loading.value = false;
        }
        return success;
    }

    /**
     * Deletes a set of tasks in database
     * @param {Array<number | string>} tasksIds 
     * @returns {Promise<boolean>}
     */
    const deleteTasks = async(tasksIds) => {
        loading.value = false;
        let success = false;
        try {
            const response = await axios.patch(`${TASKS_ENDPOINT}/1.json`, {
                action: 'delete',
                tasksIds,
            });
            if(response.error) throw new Error(response.error);
            tasks.value = tasks.value.filter(({id}) => !tasksIds.includes(id));
        } catch (error) {
            handleError(error, 'Error adding task.');
        } finally {
            loading.value = false;
        }
        return success;
    }

    /**
     * Mark as complete a set of tasks in database
     * @param {Array<number | string>} tasksIds 
     * @returns {Promise<boolean>}
     */
    const completeTasks = async(tasksIds) => {
        loading.value = false;
        let success = false;
        try {
            const response = await axios.patch(`${TASKS_ENDPOINT}`, {
                action: 'complete',
                tasksIds,
            });
            if(response.error) throw new Error(response.error);
            tasks.value = tasks.value.map((task) => {
                if(!tasksIds.includes(task.id)) return task;
                return {...task, completed: true};
            });
        } catch (error) {
            handleError(error, 'Error completing tasks.');
        } finally {
            loading.value = false;
        }
        return success;
    }

    /**
     * fetch data and expose to panel
     * @param {number} taskId
     */
    const displayViewTask = async(taskId) => {
        const fetched = await fetchTaskData(taskId);
        if(!fetched) { return }
        setViewTaskOn(fetched);
    }

    /**
     * fetch data and expose to form to edit
     * @param {number} taskId
     */
    const displayEditTask = async(taskId) => {
        const fetched = await fetchTaskData(taskId);
        if(!fetched) { return }
        setEditTaskOn(fetched);
    }

    /**
     * display panel for adding
     * async for compatibility
     * 
     * @param {number | null} parentId
     */
    const displayAddTask = async(parentId) => {
        setAddTaskOn(parentId);
    }


    /**
     * display confirmation modal for deleting
     * 
     * @param {number | null} taskId. Null for selected
     * @returns {Promise<null>}
     */
    const displayDeleteConfirm = async (taskId) => {
        const toDelete = taskId ? [...findDescendants(taskId, tasks.value), taskId] : getSelectedIds();
        const messages = [
            'Are you sure you want to delete the following tasks?',
            ...tasks.value.filter(({ id }) => toDelete.includes(id)).map(({ name  }) => name),
        ]
        const done = await displayConfirmation(messages, () => deleteTasks(toDelete));
        return done;
    }

    /**
     * display panel for adding
     * 
     * @param {number | null} taskId. Null for selected
     * @returns {Promise<null>}
     */
    const displayCompletedConfirm = async (taskId) => {
        const toComplete = taskId ? [...findDescendants(taskId, tasks.value), taskId] : getSelectedIds();
        const messages = [
            'Are you sure to mark the following tasks as completed?',
            ...tasks.value.filter(({ id }) => toComplete.includes(id)).map(({ name  }) => name),
        ]
        const done = await displayConfirmation(messages, () => completeTasks(toComplete));
        return done;
    }

    provide('tasks', {
        tasks,
        currentTask,
        displayPanel,
        loading,
        setSubtasksSelected,
        toggleTaskSelected,
        setPanelOff,
        displayAddTask,
        displayEditTask,
        displayViewTask,
        fetchTasks,
        addTask,
        updateTask,
        displayDeleteConfirm,
        displayCompletedConfirm,
        toggleTaskFold,
    });
}