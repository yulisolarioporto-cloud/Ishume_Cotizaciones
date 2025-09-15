<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISHUME - Sistema de Cotización</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
        }

        body {
            background: linear-gradient(135deg, #1b09013c 0%, #ff5e00ff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .brand-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 0.5rem; /* menos separación general */
        }   

        .brand-header .logo {
            height: 120px;
            width: auto;
            margin-bottom: 4px; /* logo más cerca del texto */
            object-fit: contain;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
        }


        .brand-title {
            font-size: 4rem;
            font-weight: 800;
            color: var(--primary-color);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 3px;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 3rem;
            font-weight: 300;
        }

        .menu-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .menu-card {
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            border-radius: 15px;
            padding: 2rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .menu-card:hover::before {
            left: 100%;
        }

        .menu-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: inherit;
        }

        .cotizacion-card {
            border-color: var(--secondary-color);
        }

        .cotizacion-card:hover {
            background: linear-gradient(145deg, #3498db, #2980b9);
            color: white;
        }

        .pedidos-card {
            border-color: var(--success-color);
        }

        .pedidos-card:hover {
            background: linear-gradient(145deg, #27ae60, #229954);
            color: white;
        }

        .menu-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .cotizacion-card .menu-icon {
            color: var(--secondary-color);
        }

        .pedidos-card .menu-icon {
            color: var(--success-color);
        }

        .menu-card:hover .menu-icon {
            color: white;
            transform: scale(1.2);
            transition: all 0.3s ease;
        }

        .menu-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .menu-description {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @media (max-width: 768px) {
            .brand-title {
                font-size: 2.5rem;
            }
            
            .menu-options {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .brand-card {
                padding: 2rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="main-container">
        <div class="brand-card">
            <div class="brand-header">
            <img src="ishume logo.png" alt="Logo ISHUME" class="logo">
            <h1 class="brand-title">ISHUME</h1>
            </div>

            <p class="brand-subtitle">Sistema de Cotización y Gestión de Pedidos</p>
            
            <div class="menu-options">
                <a href="uploads/cotizaciones.php" class="menu-card cotizacion-card">
                    <i class="fas fa-calculator menu-icon"></i>
                    <h3 class="menu-title">Cotización</h3>
                    <p class="menu-description">Crear y gestionar cotizaciones de productos escolares</p>
                </a>
                
                <a href="pedidos.php" class="menu-card pedidos-card">
                    <i class="fas fa-shopping-cart menu-icon"></i>
                    <h3 class="menu-title">Pedidos</h3>
                    <p class="menu-description">Ver y administrar pedidos realizados</p>
                </a>
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-graduation-cap me-1"></i>
                    Especializado en productos para promociones escolares
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efecto de partículas en movimiento
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.menu-card');
            
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.05)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>