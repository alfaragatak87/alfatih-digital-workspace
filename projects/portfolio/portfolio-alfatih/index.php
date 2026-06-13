<?php
require_once 'config/constants.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to My Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #00e5ff;
            --primary-dark: #00b8d4;
            --dark-bg: #0a0a1a;
            --dark-surface: #121225;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: #fff;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }
        
        /* Space background with stars */
        .space-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
            overflow: hidden;
        }
        
        .stars {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .star {
            position: absolute;
            background-color: #fff;
            border-radius: 50%;
            animation: twinkle 5s infinite;
        }
        
        @keyframes twinkle {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 0.3; }
        }
        
        .welcome-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            z-index: 10;
            padding: 0 20px;
        }
        
        .welcome-title {
            font-size: 3.5rem;
            margin-bottom: 2rem;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(0, 229, 255, 0.5);
        }
        
        .welcome-subtitle {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            color: #ccc;
            max-width: 600px;
        }
        
        .buttons-container {
            display: flex;
            gap: 2rem;
        }
        
        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            outline: none;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--dark-bg);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
        }
        
        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-outline:hover {
            background-color: rgba(0, 229, 255, 0.1);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
        }
        
        /* Logo */
        .logo {
            margin-bottom: 2rem;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px rgba(0, 229, 255, 0.3);
        }
        
        .logo i {
            font-size: 4rem;
            color: var(--primary-color);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2.5rem;
            }
            
            .buttons-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="space-bg">
        <div class="stars" id="stars"></div>
    </div>
    
    <div class="welcome-container">
        <div class="logo">
            <i class="fas fa-code"></i>
        </div>
        <h1 class="welcome-title">Welcome to My Portfolio</h1>
        <p class="welcome-subtitle">Explore my projects, read my blog, and learn more about my journey as a web developer and UI/UX designer.</p>
        <div class="buttons-container">
            <a href="<?= BASE_URL ?>/pages/index.php" class="btn btn-primary">PENGUNJUNG</a>
            <a href="<?= BASE_URL ?>/admin/login.php" class="btn btn-outline">ADMIN</a>
        </div>
    </div>
    
    <script>
        // Create stars for the background
        document.addEventListener('DOMContentLoaded', function() {
            const starsContainer = document.getElementById('stars');
            const numberOfStars = 200;
            
            for (let i = 0; i < numberOfStars; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                
                // Random position
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                
                // Random size
                const size = Math.random() * 3;
                
                // Random opacity
                const opacity = Math.random() * 0.7 + 0.3;
                
                // Random animation delay
                const delay = Math.random() * 5;
                
                star.style.left = `${posX}%`;
                star.style.top = `${posY}%`;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.opacity = opacity;
                star.style.animationDelay = `${delay}s`;
                
                starsContainer.appendChild(star);
            }
        });
    </script>
</body>
</html>