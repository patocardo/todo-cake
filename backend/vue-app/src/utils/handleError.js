import store from '@/store';

/**
 * Centralized error handling.
 * @param {Object} error - The error object.
 * @param {Object} error.response - The response object from axios (if it's a server error).
 * @param {string} error.response.data - The error message from the server.
 * @param {string} [comment] - An optional comment to add to the error message.
 */
export default function handleError(error, comment = '') {
  let displayMessage, logMessage;
    // Check if the error response from the server contains a message
    const serverMessage = error.response && error.response.data && error.response.data.message;
    if(serverMessage) {
      // Define user-friendly messages for specific error scenarios
      const userFriendlyMessages = {
        400: `Bad request. Please check your input. ${comment}`,
        401: `Unauthorized. Please log in again. ${comment}`,
        403: `Forbidden. You do not have the necessary permissions. ${comment}`,
        404: `Resource not found. ${comment}`,
        500: `Internal server error. Please try again later. ${comment}`,
      };
    
      // Determine the message to display to the user
      displayMessage = userFriendlyMessages[error.response?.status] || serverMessage || 'An unexpected error occurred.';
      logMessage = serverMessage || 'An unexpected error occurred. From server';
    } else {
      displayMessage = error.uimessage || error.message || 'An unexpected error occurred.';
      logMessage = error.logmessage || error.message || 'An unexpected error occurred.';
    }
  
    const errorData = {
      userMessage: displayMessage,
      logMessage: logMessage
    };
  
    store.dispatch('setError', errorData);
}
  