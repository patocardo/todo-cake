<template>
    <div v-if="confirmStatus == confirmStatuses.asking" class="dialog">
      <div class="card">
        <h2 class="card-title">{{ question[0] }}</h2>
        <ul>
            <li class="card-li" v-for="(line, idx) in question.slice(1)" :key="idx">
                {{ line }}
            </li>
        </ul>
        <div class="card-actions">
          <button class="btn btn-green" @click="accept()">Yes</button>
          <button class="btn btn-red" @click="cancel()">Cancel</button>
        </div>
      </div>
    </div>
</template>

<script>
import { inject } from 'vue';
import { confirmStatuses } from '@/store/useConfirm';

export default {
    name: 'ConfirmDialog',
    setup() {
        const {
            confirmStatus,
            question,
        } = inject('confirm');

        function accept() {
            confirmStatus.value = confirmStatuses.confirmed;
        }

        function cancel() {
            confirmStatus.value = confirmStatuses.canceled;
            setTimeout(() => {
                confirmStatus.value = confirmStatuses.none;
            }, 1000);
        }

        return {
            question,
            confirmStatus,
            confirmStatuses,
            accept,
            cancel,
        }
    }
}
</script>

<style>
.dialog {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.card {
    max-width: 290px;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.5rem;
    margin-bottom: 20px;
}
.card-li {
    font-size: 1.2rem;
    margin-bottom: 12px;
}

.card-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s;
}

.btn-green {
    background-color: #4CAF50;
    color: #fff;
}

.btn-green:hover {
    background-color: #388E3C;
}

.btn-red {
    background-color: #F44336;
    color: #fff;
}

.btn-red:hover {
    background-color: #D32F2F;
}
</style>
