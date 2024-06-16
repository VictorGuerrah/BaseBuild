<div class="login-container">
    <h2>Login</h2>
    <form id="loginForm">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" placeholder="teste@email.com" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="123456" required>
        </div>
        <button type="button" class="login-btn" onclick="Auth.validateCredentials()">Enter</button>
        <a href="#" class="forgot-password">Esqueceu a senha?</a>
    </form>
</div>