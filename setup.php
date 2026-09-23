<?php
// setup.php - Database Auto-Installer & Seeder

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');

$status_message = "";
$status_type = "";

if (isset($_POST['run_setup']) || isset($_GET['auto'])) {
    try {
        // Connect to MySQL server
        $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Read schema file
        $sql_file = __DIR__ . '/schema.sql';
        if (!file_exists($sql_file)) {
            throw new Exception("schema.sql file not found!");
        }

        $sql = file_get_contents($sql_file);

        // Execute SQL statements
        $pdo->exec($sql);

        // Connect specifically to perfume_db to add default admin if not existing
        $pdo->exec("USE `perfume_db`");
        $admin_email = 'admin@perfumeluxe.ng';
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$admin_email]);
        if (!$stmt->fetch()) {
            $hashed_pass = password_hash('password123', PASSWORD_BCRYPT);
            $stmt_insert = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt_insert->execute(['Admin Manager', $admin_email, $hashed_pass]);
        }

        $status_message = "Database 'perfume_db' and seed tables successfully initialized!";
        $status_type = "success";
    } catch (Exception $e) {
        $status_message = "Error setup database: " . $e->getMessage();
        $status_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Élixir De Luxe Perfumes</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.65);
            --glass-border: rgba(255, 255, 255, 0.12);
            --accent-red: #ef4444;
            --accent-blue: #3b82f6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(circle at 20% 20%, #1e1b4b 0%, #0f172a 60%, #090d16 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
        }
        .setup-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            width: 90%;
            max-width: 520px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
            text-align: center;
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, var(--accent-blue) 50%, var(--accent-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .subtitle {
            color: var(--text-muted);
            margin-bottom: 24px;
            font-size: 0.95rem;
        }
        .btn-setup {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-red));
            color: #ffffff;
            border: none;
            padding: 14px 28px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            width: 100%;
            margin-top: 15px;
        }
        .btn-setup:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.4);
        }
        .btn-store {
            display: inline-block;
            background: rgba(255,255,255,0.08);
            border: 1px solid var(--glass-border);
            color: #fff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            margin-top: 15px;
            width: calc(100% - 48px);
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-store:hover {
            background: rgba(255,255,255,0.15);
        }
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: left;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        .info-box {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 14px;
            padding: 16px;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 20px 0;
            text-align: left;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .info-box code {
            color: var(--accent-blue);
        }
    </style>
</head>
<body>
    <div class="setup-card">
        <h1 class="brand-title">Élixir De Luxe</h1>
        <p class="subtitle">Database Setup & Installer Wizard</p>

        <?php if (!empty($status_message)): ?>
            <div class="alert alert-<?php echo $status_type; ?>">
                <?php echo htmlspecialchars($status_message); ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <p><strong>Database Target:</strong> <code>perfume_db</code> on <code>127.0.0.1:3306</code></p>
            <p>Click below to automatically create database tables and seed 8 luxury perfumes with prices in Naira (₦).</p>
            <p style="margin-bottom:0;"><strong>Admin Credentials:</strong><br>Email: <code>admin@perfumeluxe.ng</code> | Pass: <code>password123</code></p>
        </div>

        <form method="POST">
            <button type="submit" name="run_setup" class="btn-setup">⚡ Initialize / Reset Database</button>
        </form>

        <a href="index.php" class="btn-store">🛍️ Launch Perfume Store</a>
    </div>
</body>
</html>
