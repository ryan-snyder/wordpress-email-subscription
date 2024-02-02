/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/* eslint-disable no-console */
console.log(
	'Hello World! (from create-block-email-subscription-block block)'
);
/* eslint-enable no-console */

const { apiFetch } = wp;
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('email-subscription-form');
	console.log('Got form...')
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = form.querySelector('input[name="email"]').value;
			const mailchimpUrl = form.getAttribute('data-mailchimp-url'); // Retrieve the Mailchimp URL
			console.log('Submitting form')
			console.log(JSON.stringify({ email: email}))
			console.log(mailchimpUrl)
            apiFetch({
                path: '/email-subscription-block/email-subscribe',
                method: 'POST',
                headers: {
					'Content-Type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
				body: JSON.stringify({ email: email})
            }).then((response) => {
                console.log('Subscription successful:', response);
                // Handle success response
				showSnackbar(response.message, 'success');
				form.reset(); // Reset form fields
            }).catch((error) => {
                console.error('Subscription failed:', error);
				const { message } = error.data.details.email;
                // Handle error response
				showSnackbar(message || 'An error occurred, please try again.', 'error');
            });
        });
    }
});

function showSnackbar(message, messageType) {
    const snackbar = document.getElementById('snackbar');
    snackbar.textContent = message;
    snackbar.className = 'show ' + messageType; // Add "show" class along with "success" or "error"
    setTimeout(function() { snackbar.className = snackbar.className.replace('show ' + messageType, ''); }, 3000);
}

