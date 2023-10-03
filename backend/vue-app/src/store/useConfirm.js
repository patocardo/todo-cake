import { ref, watch, provide } from "vue";

const confirmStatuses = {
    none: 'none',
    asking: 'asking',
    confirmed: 'confirmed',
    canceled: 'canceled',
}
/**
 * State hook for confirm dialog operations
 */
function useConfirm() {
    const confirmStatus = ref(confirmStatuses.none);
    const question = ref([]); // paragraphs to include
    const success = ref(false);

    /**
     * Creates a promise for confirmation dialog
     * @returns {Promise<boolean>}
     */
    const confirmPromise = () => {
        return new Promise((resolve) => {
            const unwatch = watch(confirmStatus, (newStatus, oldStatus) => {
                if (oldStatus !== confirmStatuses.asking) {
                    unwatch(); // promise should never been created
                    resolve(false);
                    return false;
                }
                if (newStatus === confirmStatuses.confirmed || newStatus === confirmStatuses.cancel) {
                    unwatch(); // Stop watching once the condition is met
                    resolve(newStatus === confirmStatuses.confirmed);
                }
            });
        });
    };

    /**
     * Display the confirm dialog
     * @param {Array<string>} messages
     * @param {function(): Promise.<boolean>} action.
     */
    const displayConfirmation = async(messages, action) => {
        confirmStatus.value = confirmStatuses.asking;
        question.value = messages;
        await confirmPromise();
        if(confirmStatus.value === confirmStatuses.confirmed) {
            success.value = await action(); 
        }
        confirmStatus.value = confirmStatuses.none; // closes the dialog
    }


    provide('confirm', {
        confirmStatus,
        question,
        success,
        displayConfirmation,
    });

    return {displayConfirmation};
}

export {confirmStatuses, useConfirm};