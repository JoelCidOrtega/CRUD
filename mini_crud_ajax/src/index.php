<?php
declare(strict_types=1);?>

<body>
    <main class="card" style="max-width: 400px; margin-top: 10vh;">
        <h1 style="text-align: center;">Iniciar Sesión</h1>
        <form id="formLogin" action="index.php" method="post">
            <div>
                <label for="usuario">Email</label>
                <input type="email" id="usuario" name="email" required placeholder="tu@email.com">
            </div>
            
            <div>
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="••••••">
            </div>
            
            <button type="submit">Ingresar</button>
            <p id="errorMsg" style="color: var(--error-color); text-align: center; margin: 0;"></p>
        </form>
    </main>
    <script src="/assets/js/login.js"></script>
</body>
</html>