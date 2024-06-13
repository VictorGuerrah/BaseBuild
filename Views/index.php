<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basebuild</title>
    <?php require_once('imports.php')?>
</head>
<body>
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
</body>
</html>