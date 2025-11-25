const formLogin = document.getElementById('formLogin');
const errorMsg = document.getElementById('errorMsg');

formLogin.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorMsg.textContent = '';

    const formData = new FormData(formLogin);
    const data = {
        email: formData.get('email'),
        contrasena: formData.get('contrasena')
    };

    try {
        const response = await fetch('/api.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.ok) {
            if (result.data.rol === 'admin') {
                window.location.href = '/index_ajax.html';
            } else {
                window.location.href = '/sociograma/index.php';
            }
        } else {
            errorMsg.textContent = result.error || 'Error al iniciar sesión';
        }
    } catch (error) {
        console.error('Error:', error);
        errorMsg.textContent = 'Error de conexión';
    }
});
