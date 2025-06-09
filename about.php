<?php
session_start();
require_once 'config/database.php';

// Fetch project count
$projects_count = 0;
$projects_query = "SELECT COUNT(*) as count FROM projects WHERE status = 'active'";
try {
    $stmt = $pdo->query($projects_query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $projects_count = $result ? $result['count'] : 0;
} catch (PDOException $e) {
    $projects_count = 0;
}

// Fetch activities count
$activities_count = ['total' => 0, 'this_month' => 0];
$activities_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE()) THEN 1 ELSE 0 END) as this_month
    FROM activities";
try {
    $stmt = $pdo->query($activities_query);
    $activities_count = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Default values already set
}

// Calculate percentage changes (this would normally come from comparing with previous period)
$projects_change = 12.5; // This would be calculated based on previous period
$activities_change = 8.3; // This would be calculated based on previous period

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get counts for sidebar
$dashboard_count = 0; // Dashboard doesn't need a count

// Get total projects count
$projects_count = $pdo->query("SELECT COUNT(*) as count FROM projects")->fetch(PDO::FETCH_ASSOC)['count'];

// Get activities count by status
$activities_count = $pdo->query("SELECT 
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'in progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    COUNT(*) as total
    FROM activities")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal DICT Project Monitoring System - About</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary: #0f172a;
            --secondary: #1e293b;
            --accent: #00f7ff;
            --accent-hover: #00e1e8;
            --text: #e2e8f0;
            --text-secondary: #94a3b8;
            --glass: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(0, 247, 255, 0.1);
            --card-bg: rgba(30, 41, 59, 0.5);
        }
        
        body {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--text);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.7;
            overflow-x: hidden;
        }
        
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px -15px rgba(0, 247, 255, 0.3);
            border-color: var(--accent);
        }
        
        .gradient-text {
            background: linear-gradient(90deg, #00f7ff, #00b4d8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: gradient 8s ease infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 247, 255, 0.1);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: var(--accent);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            background: var(--accent);
            color: var(--primary);
            transform: rotateY(180deg);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(90deg, var(--accent), #00b4d8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        
        .tech-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .tech-badge {
            background: rgba(0, 247, 255, 0.1);
            color: var(--accent);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            border: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }
        
        .tech-badge:hover {
            background: var(--accent);
            color: var(--primary);
            transform: translateY(-2px);
        }
        
        .btn-neon {
            background: transparent;
            color: var(--accent);
            border: 2px solid var(--accent);
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            transition: all 0.4s ease;
        }
        
        .btn-neon:hover {
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 247, 255, 0.4);
        }
        
        .btn-neon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--accent);
            z-index: -1;
            transition: all 0.4s ease;
        }
        
        .btn-neon:hover::before {
            width: 100%;
        }
        
        .pulse {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: var(--accent);
            border-radius: 50%;
            margin-left: 10px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 247, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 247, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 247, 255, 0); }
        }
        
        .glow {
            text-shadow: 0 0 10px rgba(0, 247, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row h-100">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 main-content" style="margin-left: 350px; padding: 2rem;">
                <!-- Hero Section -->
                <div class="container-fluid p-0 mb-5">
                    <div class="row align-items-center">
                        <div class="col-lg-6" data-aos="fade-right">
                            <span class="text-accent text-uppercase fw-bold">Welcome to</span>
                            <h1 class="display-4 fw-bold mb-4">Personal DICT Project Monitoring <span class="gradient-text">System</span> <span class="pulse"></span></h1>
                            <p class="lead mb-4">Empowering the future of digital governance through innovative project management solutions.</p>
                            <div class="d-flex gap-3">
                                <a href="#features" class="btn btn-neon">Explore Features</a>
                                <a href="#contact" class="btn btn-outline-light">Contact Us</a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Features Section -->
                <section id="features" class="mb-5">
                    <div class="text-center mb-5" data-aos="fade-up">
                        <span class="text-accent text-uppercase fw-bold">Features</span>
                        <h2 class="fw-bold mb-4">Powerful <span class="gradient-text">Capabilities</span></h2>
                        <p class="lead mx-auto" style="max-width: 700px;">Designed to streamline your project management workflow with cutting-edge technology</p>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="glass-card feature-card h-100 p-4">
                                <div class="feature-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <h4 class="mb-3">Real-time Analytics</h4>
                                <p class="text-white" style="opacity: 0.9;">Get instant insights with our powerful analytics dashboard that updates in real-time.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="glass-card feature-card h-100 p-4">
                                <div class="feature-icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <h4 class="mb-3">Project Tracking</h4>
                                <p class="text-white" style="opacity: 0.9;">Monitor all your projects in one place with our intuitive tracking system.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="glass-card feature-card h-100 p-4">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h4 class="mb-3">Advanced Reporting</h4>
                                p class="text-white" style="opacity: 0.9;">Generate comprehensive reports with just a few clicks.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tech Stack -->
                <div class="glass-card p-4 mb-5" data-aos="fade-up">
                    <h4 class="mb-4">Built With Modern Technology</h4>
                    <div class="tech-stack">
                        <span class="tech-badge"><i class="fab fa-php me-2"></i>PHP 8.1+</span>
                        <span class="tech-badge"><i class="fab fa-js me-2"></i>JavaScript</span>
                        <span class="tech-badge"><i class="fab fa-bootstrap me-2"></i>Bootstrap 5</span>
                        <span class="tech-badge"><i class="fas fa-database me-2"></i>MySQL</span>
                        <span class="tech-badge"><i class="fab fa-html5 me-2"></i>HTML5</span>
                        <span class="tech-badge"><i class="fab fa-css3-alt me-2"></i>CSS3</span>
                    </div>
                </div>

                <!-- Contact Section -->
                <section id="contact" class="mb-5">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                            <h2 class="fw-bold mb-4">Get In <span class="gradient-text">Touch</span></h2>
                            <p class="mb-4">Have questions or need assistance? I'm here to help you with any inquiries.</p>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3 text-accent">
                                    <i class="fas fa-envelope fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Email Us</h5>
                                    <a href="mailto:salamander00000@gmail.com" class="text-decoration-none text-white">salamander00000@gmail.com</a>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3 text-accent">
                                    <i class="fas fa-phone-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Call Us</h5>
                                    <a href="tel:+639121619044" class="text-decoration-none text-white">+63 912 161 9044</a>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start">
                                <div class="me-3 text-accent">
                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Visit Us</h5>
                                    <p class="text-white mb-0">Mabini, Tubajon, Province of Dinagat Islands, Philippines</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6" data-aos="fade-left">
                            <div class="glass-card p-4">
                                <h4 class="mb-4">Send us a Message</h4>
                                <form>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control bg-dark text-white border-secondary" id="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control bg-dark text-white border-secondary" id="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control bg-dark text-white border-secondary" id="message" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-neon px-4">Send Message</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="text-center text-muted py-4">
                    <p class="mb-0">© <?php echo date('Y'); ?> DICT Project Monitoring System. All rights reserved.</p>
                </footer>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize AOS (Animate On Scroll)
        AOS.init({
            duration: 800,
            once: true
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add active class to current section in navigation
        const sections = document.querySelectorAll('section');
        const navItems = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - sectionHeight / 3)) {
                    current = section.getAttribute('id');
                }
            });

            navItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === `#${current}`) {
                    item.classList.add('active');
                }
            });
        });

        // Update current year in footer
        document.getElementById('currentYear').textContent = ` ${new Date().getFullYear()} ` + document.getElementById('currentYear').textContent;

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Activity Trend Chart
            const trendCtx = document.getElementById('activityTrendChart').getContext('2d');
            const activityTrendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Activities',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: 'rgba(0, 247, 255, 1)',
                        backgroundColor: 'rgba(0, 247, 255, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(0, 247, 255, 0.5)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#94a3b8',
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

            // Project Status Chart
            const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
            const projectStatusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'On Hold', 'Not Started'],
                    datasets: [{
                        data: [35, 25, 15, 25],
                        backgroundColor: [
                            'rgba(46, 213, 115, 0.8)',
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(241, 196, 15, 0.8)',
                            'rgba(149, 163, 184, 0.8)'
                        ],
                        borderColor: [
                            'rgba(46, 213, 115, 1)',
                            'rgba(52, 152, 219, 1)',
                            'rgba(241, 196, 15, 1)',
                            'rgba(149, 163, 184, 1)'
                        ],
                        borderWidth: 1,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#e2e8f0',
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(0, 247, 255, 0.5)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${percentage}% (${value})`;
                                }
                            }
                        }
                    }
                }
            });

            // Team Performance Chart
            const teamCtx = document.getElementById('teamPerformanceChart').getContext('2d');
            const teamPerformanceChart = new Chart(teamCtx, {
                type: 'bar',
                data: {
                    labels: ['John D.', 'Jane S.', 'Mike J.', 'Sarah W.', 'David B.'],
                    datasets: [{
                        label: 'Tasks Completed',
                        data: [12, 19, 8, 15, 10],
                        backgroundColor: 'rgba(0, 247, 255, 0.8)',
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            borderColor: 'rgba(0, 247, 255, 0.5)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#e2e8f0'
                            }
                        }
                    }
                }
            });

            // Chart metric toggle
            const metricButtons = document.querySelectorAll('[data-metric]');
            metricButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    metricButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Update chart data based on selected metric
                    const metric = this.getAttribute('data-metric');
                    let newData, newLabel;
                    
                    switch(metric) {
                        case 'activities':
                            newData = [65, 59, 80, 81, 56, 55, 40];
                            newLabel = 'Activities';
                            break;
                        case 'projects':
                            newData = [28, 35, 42, 38, 45, 50, 48];
                            newLabel = 'Projects';
                            break;
                        case 'users':
                            newData = [10, 15, 18, 20, 22, 24, 26];
                            newLabel = 'Users';
                            break;
                    }
                    
                    activityTrendChart.data.datasets[0].data = newData;
                    activityTrendChart.data.datasets[0].label = newLabel;
                    activityTrendChart.update();
                });
            });

            // Time range filter
            const timeRangeButtons = document.querySelectorAll('.time-range');
            timeRangeButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all buttons
                    timeRangeButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Update dropdown text
                    const dropdownBtn = document.getElementById('timeRangeDropdown');
                    dropdownBtn.innerHTML = `<i class="fas fa-calendar-alt me-2"></i>${this.textContent}`;
                    
                    // Here you would typically make an AJAX call to update the data
                    // For now, we'll just log the selected range
                    const range = this.getAttribute('data-range');
                    console.log('Selected range:', range);
                    
                    // Simulate loading
                    // You would replace this with actual data loading
                    setTimeout(() => {
                        // Update charts with new data based on range
                        // This is just a simulation
                        console.log('Data loaded for range:', range);
                    }, 500);
                });
            });
        });
    </script>
</body>
</html>