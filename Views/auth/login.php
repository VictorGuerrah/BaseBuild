<div class="login-container">
    <h2>Login</h2>
    <form action="process_login.php" method="post">
        <input type="text" name="username" placeholder="Usuário" required>
        <input type="password" name="password" placeholder="Senha" required>
        <input type="submit" value="Entrar">
    </form>
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">Usuário ou senha inválidos.</div>
    <?php endif; ?>
</div>