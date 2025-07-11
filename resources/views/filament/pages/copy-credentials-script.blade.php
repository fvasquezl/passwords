<script>
    function copyCredentials(event) {
        event.preventDefault();

        const element = event.target.closest('[data-username]');
        const username = element.getAttribute('data-username');
        const password = element.getAttribute('data-password');

        const credentials = `User: ${username}\nPassword: ${password}`;

        navigator.clipboard.writeText(credentials).then(() => {
            // Crear notificación personalizada
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = 'Credentials copied to clipboard';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    }

    function copyCredentialsFromGrid(event) {
        event.preventDefault();

        const element = event.target.closest('[data-username]');
        const username = element.getAttribute('data-username');
        const password = element.getAttribute('data-password');

        const credentials = `User: ${username}\nPassword: ${password}`;

        navigator.clipboard.writeText(credentials).then(() => {
            // Crear notificación personalizada
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = 'Credentials copied to clipboard';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    }

    function togglePasswordVisibility() {
        const passwordText = document.getElementById('password-text');
        const container = passwordText.closest('[data-password]');
        const actualPassword = container.getAttribute('data-password');
        
        if (passwordText.textContent.includes('•')) {
            passwordText.textContent = actualPassword;
        } else {
            passwordText.textContent = '•'.repeat(actualPassword.length);
        }
    }
</script>