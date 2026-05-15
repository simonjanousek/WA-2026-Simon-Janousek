 <!-- <?php require_once '../app/views/layout/header.php'; ?>

<main class="container mx-auto px-6 py-10 flex-grow flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-light tracking-widest text-slate-300 uppercase">Přihlášení</h2>
            <p class="text-slate-500 italic mt-2 text-sm">Vítejte zpět v naší Knihovně.</p>
        </div>
        
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl shadow-2xl backdrop-blur-sm p-6 md:p-8">
            <form action="index.php?url=auth/authenticate" method="post">
                
                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">E-mail</label>
                        <input type="email" id="email" name="email" required autofocus
                               class="w-full bg-slate-900/50 border border-slate-600 rounded-md px-4 py-2 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Heslo</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full bg-slate-900/50 border border-slate-600 rounded-md px-4 py-2 text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 text-white font-bold py-3 px-4 rounded-md shadow-lg border border-emerald-500 transition-all uppercase tracking-widest text-sm">
                            Přihlásit se
                        </button>
                    </div>
                    
                    <p class="text-center text-slate-500 text-sm border-t border-slate-700 pt-4">
                        Nemáte ještě účet? <a href="index.php?url=auth/register" class="text-emerald-400 hover:text-white transition-colors">Zaregistrujte se</a>.
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>

-->
<?php require_once '../app/views/layout/header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --secondary: #8b5cf6;
        --bg-gradient: radial-gradient(circle at top right, #f8fafc, #e2e8f0);
        --text-main: #1e293b;
        --text-muted: #64748b;
        --card-bg: #ffffff;
    }

    .login-main {
        font-family: 'Inter', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 160px);
        background: var(--bg-gradient);
        padding: 2rem 1.5rem;
    }

    .login-box {
        width: 100%;
        max-width: 420px;
        animation: fadeIn 0.8s ease-out;
    }

    /* Animace */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .login-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .login-header h2 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.04em;
    }

    .login-header p {
        color: var(--text-muted);
        font-size: 1.05rem;
        font-weight: 400;
    }

    /* Karta formuláře */
    .login-card {
        background: var(--card-bg);
        padding: 3rem 2.5rem;
        border-radius: 2rem;
        /* Moderní "soft" stín */
        box-shadow: 
            0 10px 15px -3px rgba(0, 0, 0, 0.05),
            0 25px 50px -12px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
    }

    .form-group {
        margin-bottom: 1.8rem;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.6rem;
        margin-left: 0.2rem;
    }

    .form-group input {
        width: 100%;
        box-sizing: border-box;
        padding: 1rem 1.25rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 1rem;
        background: #fcfcfd;
        color: var(--text-main);
        font-size: 1rem;
        font-family: inherit;
        transition: all 0.25s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        transform: scale(1.01);
    }

    /* Tlačítko s efektem */
    .submit-btn {
        width: 100%;
        padding: 1.1rem;
        margin-top: 0.5rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 1rem;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.5);
        filter: brightness(1.1);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    /* Patička karty */
    .register-link {
        text-align: center;
        margin-top: 2rem;
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .register-link a {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
        position: relative;
    }

    .register-link a::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 2px;
        bottom: -2px;
        left: 0;
        background: var(--secondary);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .register-link a:hover::after {
        transform: scaleX(1);
    }
</style>

<main class="login-main">
    <div class="login-box">
        
        <div class="login-header">
            <h2>Knihovna</h2>
            <p>Vítejte zpět! Přihlaste se ke svému účtu.</p>
        </div>
        
        <div class="login-card">
            <form action="index.php?url=auth/authenticate" method="post">
                
                <div class="form-group">
                    <label for="email">E-mailová adresa</label>
                    <input type="email" id="email" name="email" placeholder="např. karel@ctenář.cz" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="submit-btn">
                    Vstoupit do knihovny
                </button>
                
                <p class="register-link">
                    Nový čtenář? <a href="index.php?url=auth/register">Vytvořit účet</a>
                </p>

            </form>
        </div>
        
    </div>
</main>

<?php require_once '../app/views/layout/footer.php'; ?>