<?php require_once 'api-helper.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Btab PHP Playground</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { 
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .status-error { 
            background: #fee;
            border-color: #c33;
            color: #800;
        }
        .status-success { 
            background: #efe;
            border-color: #3c3;
            color: #060;
        }
        .status-warning { 
            background: #ffc;
            border-color: #cc3;
            color: #660;
        }
        .setup-steps {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .setup-steps h2 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .setup-steps ol {
            margin-left: 20px;
        }
        .setup-steps li {
            margin: 10px 0;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
        .nav {
            margin: 30px 0;
            padding: 20px 0;
            border-top: 2px solid #eee;
            border-bottom: 2px solid #eee;
        }
        .nav a {
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Btab PHP Playground</h1>
        <p class="subtitle">Build your custom storefront with the Btab API</p>

        <?php if (!isApiKeyConfigured()): ?>
            <div class="status status-warning">
                <strong>⚠️ API Key Not Configured</strong><br>
                You need to set up your API key to start using the Btab API.
            </div>

            <div class="setup-steps">
                <h2>Quick Setup Guide</h2>
                <ol>
                    <li><strong>Register for a Btab vendor account:</strong><br>
                        Go to <a href="https://dashboard.btab.app/register" target="_blank">dashboard.btab.app/register</a> and create your account
                    </li>
                    <li><strong>Get your API key:</strong><br>
                        After registration, login to the dashboard and copy your API key
                    </li>
                    <li><strong>Configure this playground:</strong><br>
                        Edit the config file and add your API key:
                        <div class="code"># On the VPS, edit:
/home/adminuser/php-playground/config/config.php

# Change this line:
define('BTAB_API_KEY', 'your_api_key_here');</div>
                    </li>
                    <li><strong>Restart the container:</strong><br>
                        <div class="code">cd /home/adminuser/php-playground
docker-compose restart</div>
                    </li>
                </ol>
            </div>

            <div class="nav">
                <a href="https://dashboard.btab.app/register" class="btn" target="_blank">Register for Account</a>
                <a href="https://dashboard.btab.app" class="btn" target="_blank">Login to Dashboard</a>
            </div>

        <?php else: ?>
            <div class="status status-success">
                <strong>✅ API Key Configured!</strong><br>
                You're ready to start building with the Btab API.
            </div>

            <div class="nav">
                <h2 style="margin-bottom: 15px;">Example Pages:</h2>
                <a href="products.php" class="btn">View Products</a>
                <a href="cart.php" class="btn">Shopping Cart Demo</a>
                <a href="test-api.php" class="btn">Test API Connection</a>
            </div>

            <div class="setup-steps">
                <h2>What's Next?</h2>
                <ul>
                    <li>Check out the example pages above to see the API in action</li>
                    <li>All your PHP files go in <code>/home/adminuser/php-playground/public/</code></li>
                    <li>API helper functions are in <code>api-helper.php</code></li>
                    <li>View the PHP Developer Guide for complete examples</li>
                    <li>Upload files via SFTP to start building your custom storefront</li>
                </ul>
            </div>
        <?php endif; ?>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 0.9em;">
            <p><strong>API Endpoint:</strong> https://api.btab.app/api/v1</p>
            <p><strong>Documentation:</strong> See PHP_DEVELOPER_GUIDE.md</p>
            <p><strong>Dashboard:</strong> <a href="https://dashboard.btab.app" target="_blank">dashboard.btab.app</a></p>
        </div>
    </div>
</body>
</html>
<!-- Auto-deploy test Mon Oct 20 06:03:03 PM AEDT 2025 -->
