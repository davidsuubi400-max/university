<?php
// Define the upload directory
$uploadDir = 'uploads/';

// Create the directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file upload
$uploadMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $fileName = basename($file['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    
    // Allowed file types
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($fileType, $allowedTypes)) {
        if ($file['size'] <= 10 * 1024 * 1024) {
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $uploadMessage = '<div class="success">✅ Image uploaded successfully! Page will refresh to show new image.</div>';
                echo '<meta http-equiv="refresh" content="2">';
            } else {
                $uploadMessage = '<div class="error">❌ Error uploading file.</div>';
            }
        } else {
            $uploadMessage = '<div class="error">❌ File too large. Maximum size is 10MB.</div>';
        }
    } else {
        $uploadMessage = '<div class="error">❌ Only JPG, PNG, GIF, and WEBP files are allowed.</div>';
    }
}

// Get all images from the uploads directory
$images = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        $filePath = $uploadDir . $file;
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (is_file($filePath) && in_array($fileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $images[] = $filePath;
        }
    }
    // Sort images by creation time
    usort($images, function($a, $b) {
        return filectime($a) - filectime($b);
    });
}

// Convert images array to JSON for JavaScript
$imagesJson = json_encode($images);
$imageCount = count($images);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Continuous 3D Circular Slideshow | Never Stops Rotating</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            user-select: none;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at center, #1a1a2e 0%, #16213e 50%, #0f0f1f 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Animated stars effect */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, #fff, rgba(0,0,0,0)),
                radial-gradient(1px 1px at 40px 70px, #fff, rgba(0,0,0,0)),
                radial-gradient(3px 3px at 150px 200px, #fff, rgba(0,0,0,0));
            background-repeat: no-repeat;
            background-size: 200px 200px;
            opacity: 0.3;
            animation: stars 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes stars {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-200px);
            }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 10px;
            font-size: 2.5em;
            text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
            letter-spacing: 2px;
            animation: glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes glow {
            from {
                text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
            }
            to {
                text-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
            }
        }
        
        .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.8);
            margin-bottom: 30px;
            font-size: 1.1em;
        }

        /* Upload Form */
        .upload-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .upload-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }

        .file-input {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            background: rgba(255,255,255,0.9);
        }

        .upload-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .success, .error {
            text-align: center;
            padding: 10px;
            margin-top: 10px;
            border-radius: 8px;
            font-weight: bold;
            animation: slideDown 0.3s ease;
        }

        .success {
            background: rgba(212, 237, 218, 0.9);
            color: #155724;
        }

        .error {
            background: rgba(248, 215, 218, 0.9);
            color: #721c24;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 3D Circular Carousel - Continuous Rotation */
        .carousel-container {
            position: relative;
            width: 100%;
            height: 600px;
            margin: 50px 0;
            perspective: 1500px;
            perspective-origin: 50% 50%;
            cursor: grab;
        }
        
        .carousel-container:active {
            cursor: grabbing;
        }

        .carousel {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            will-change: transform;
        }

        .carousel-item {
            position: absolute;
            width: 280px;
            height: 350px;
            left: 50%;
            top: 50%;
            margin-left: -140px;
            margin-top: -175px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            transform-style: preserve-3d;
            backface-visibility: hidden;
        }

        .carousel-item:hover {
            transform: scale(1.08) translateZ(30px) !important;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6);
            transition: transform 0.3s ease;
            z-index: 100;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            pointer-events: none;
        }

        .carousel-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 10px;
            font-size: 12px;
            text-align: center;
            font-weight: bold;
            pointer-events: none;
        }

        /* Controls - Minimal to encourage continuous rotation */
        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .control-btn {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .control-btn:hover {
            background: rgba(102, 126, 234, 0.8);
            transform: translateY(-2px);
            border-color: rgba(255,255,255,0.6);
        }

        .control-btn:active {
            transform: translateY(0);
        }

        /* Speed and info panel */
        .info-panel {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
            padding: 15px 25px;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            flex-wrap: wrap;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .speed-control {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 5px 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 50px;
        }

        .speed-control label {
            font-weight: bold;
            color: white;
        }

        .speed-select {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #667eea;
            cursor: pointer;
            font-size: 14px;
            background: white;
        }

        .info-text {
            font-weight: bold;
            color: #667eea;
            font-size: 14px;
        }

        .rotation-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 15px;
            background: rgba(102, 126, 234, 0.3);
            border-radius: 50px;
            color: white;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        /* No images message */
        .no-images {
            text-align: center;
            padding: 100px 20px;
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            margin: 50px 0;
        }

        .no-images p {
            margin: 10px 0;
            color: #666;
            font-size: 18px;
        }

        .upload-icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .carousel-container {
                height: 400px;
            }
            
            .carousel-item {
                width: 200px;
                height: 250px;
                margin-left: -100px;
                margin-top: -125px;
            }
            
            h1 {
                font-size: 1.8em;
            }
            
            .control-btn {
                padding: 8px 16px;
                font-size: 14px;
            }
            
            .info-panel {
                border-radius: 20px;
                padding: 15px;
            }
        }
        
        /* Drag to rotate instruction */
        .drag-instruction {
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        
        /* Loading spinner */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌀 Continuous 3D Circular Slideshow</h1>
        <div class="subtitle">Images rotate endlessly • Smooth & steady • Never stops</div>
        
        <!-- Upload Section -->
        <div class="upload-section">
            <form method="POST" enctype="multipart/form-data" class="upload-form" id="uploadForm">
                <input type="file" name="image" accept="image/*" required class="file-input" id="fileInput">
                <button type="submit" class="upload-btn">➕ Add Image to Circle</button>
            </form>
            <?php echo $uploadMessage; ?>
        </div>
        
        <!-- 3D Circular Carousel -->
        <?php if ($imageCount > 0): ?>
        <div class="carousel-container" id="carouselContainer">
            <div class="carousel" id="carousel">
                <!-- Carousel items will be dynamically generated -->
            </div>
        </div>
        
        <div class="controls">
            <button class="control-btn" id="speedDownBtn" onclick="decreaseSpeed()">
                🐢 Slower
            </button>
            <button class="control-btn" id="speedUpBtn" onclick="increaseSpeed()">
                Faster 🐇
            </button>
            <button class="control-btn" onclick="resetRotation()">
                🔄 Reset View
            </button>
        </div>
        
        <div class="info-panel">
            <div class="speed-control">
                <label>⚡ Rotation Speed:</label>
                <select id="speedSelect" class="speed-select" onchange="changeSpeed()">
                    <option value="0.5">Very Slow (0.5x)</option>
                    <option value="1" selected>Normal (1x)</option>
                    <option value="1.5">Fast (1.5x)</option>
                    <option value="2">Very Fast (2x)</option>
                    <option value="3">Extreme (3x)</option>
                </select>
            </div>
            <div class="rotation-status">
                <span>🔄</span>
                <span>Rotating Continuously</span>
                <span>⟳</span>
            </div>
            <div class="info-text">
                📸 <span id="imageCount"><?php echo $imageCount; ?></span> images | 360° endless rotation
            </div>
        </div>
        <div class="drag-instruction">
            <span>🖱️ Click & drag to rotate manually</span>
            <span>|</span>
            <span>✨ Images rotate automatically forever</span>
        </div>
        <?php else: ?>
        <div class="no-images">
            <div class="upload-icon">🎡</div>
            <p>No images in the continuous rotation circle yet!</p>
            <p style="margin-top: 10px; font-size: 14px;">Upload images above to see them rotate endlessly in a beautiful 3D circle.</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // 3D Circular Carousel - Continuous Smooth Rotation
        const images = <?php echo $imagesJson; ?>;
        const imageCount = images.length;
        
        let carousel;
        let currentAngle = 0;
        let animationFrameId = null;
        let lastTimestamp = 0;
        let rotationSpeed = 0.005; // Radians per frame (smooth continuous)
        let speedMultiplier = 1;
        let isDragging = false;
        let dragStartX = 0;
        let dragStartAngle = 0;
        
        // Calculate angle between images
        const angleStep = 360 / imageCount;
        
        // Initialize the 3D carousel
        function initCarousel() {
            carousel = document.getElementById('carousel');
            if (!carousel || imageCount === 0) return;
            
            carousel.innerHTML = '';
            
            // Create carousel items
            images.forEach((image, index) => {
                const angle = index * angleStep;
                const item = document.createElement('div');
                item.className = 'carousel-item';
                item.setAttribute('data-index', index);
                
                // Create image element
                const img = document.createElement('img');
                img.src = image;
                img.alt = `Image ${index + 1}`;
                img.loading = 'eager';
                
                // Add caption
                const caption = document.createElement('div');
                caption.className = 'caption';
                caption.textContent = `Image ${index + 1}`;
                
                item.appendChild(img);
                item.appendChild(caption);
                
                // Add click event to focus on this image
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    focusOnImage(index);
                });
                
                carousel.appendChild(item);
                
                // Set initial position
                updateItemPosition(item, angle);
            });
        }
        
        // Update 3D position of a carousel item
        function updateItemPosition(item, angle) {
            const radius = 420; // Radius of the circle
            const radian = (angle * Math.PI) / 180;
            const x = Math.sin(radian) * radius;
            const z = Math.cos(radian) * radius;
            
            item.style.transform = `translateX(${x}px) translateZ(${z}px) rotateY(${angle}deg)`;
            
            // Adjust opacity and scale based on z-index (depth)
            const depthFactor = (z + radius) / (radius * 2);
            const opacity = 0.5 + depthFactor * 0.5;
            const scale = 0.9 + depthFactor * 0.1;
            item.style.opacity = opacity;
            item.style.filter = `brightness(${0.7 + depthFactor * 0.3})`;
        }
        
        // Update all item positions based on current angle
        function updateAllPositions() {
            if (!carousel) return;
            const items = carousel.children;
            for (let i = 0; i < items.length; i++) {
                const angle = (i * angleStep + currentAngle) % 360;
                updateItemPosition(items[i], angle);
            }
        }
        
        // Continuous rotation animation using requestAnimationFrame
        function rotateContinuously(timestamp) {
            if (!carousel) return;
            
            if (!isDragging) {
                // Smooth continuous rotation
                const rotationDelta = rotationSpeed * speedMultiplier * 16.67; // Normalize to ~60fps
                currentAngle = (currentAngle + rotationDelta) % 360;
                updateAllPositions();
            }
            
            // Continue animation loop
            animationFrameId = requestAnimationFrame(rotateContinuously);
        }
        
        // Start continuous rotation
        function startContinuousRotation() {
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            animationFrameId = requestAnimationFrame(rotateContinuously);
        }
        
        // Focus on a specific image (smooth transition)
        function focusOnImage(index) {
            if (!carousel || isDragging) return;
            
            // Calculate target angle to bring this image to front
            const targetImageAngle = (index * angleStep) % 360;
            let targetAngle = -targetImageAngle;
            
            // Smooth transition
            const startAngle = currentAngle;
            const angleDiff = targetAngle - startAngle;
            const startTime = performance.now();
            const duration = 500;
            
            function animateFocus(now) {
                const elapsed = now - startTime;
                const progress = Math.min(1, elapsed / duration);
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                
                currentAngle = startAngle + angleDiff * easeProgress;
                updateAllPositions();
                
                if (progress < 1) {
                    requestAnimationFrame(animateFocus);
                }
            }
            
            requestAnimationFrame(animateFocus);
        }
        
        // Reset rotation to initial view
        function resetRotation() {
            if (!carousel) return;
            
            const startAngle = currentAngle;
            const targetAngle = 0;
            const angleDiff = targetAngle - startAngle;
            const startTime = performance.now();
            const duration = 800;
            
            function animateReset(now) {
                const elapsed = now - startTime;
                const progress = Math.min(1, elapsed / duration);
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                
                currentAngle = startAngle + angleDiff * easeProgress;
                updateAllPositions();
                
                if (progress < 1) {
                    requestAnimationFrame(animateReset);
                }
            }
            
            requestAnimationFrame(animateReset);
        }
        
        // Change rotation speed
        function changeSpeed() {
            const speedSelect = document.getElementById('speedSelect');
            speedMultiplier = parseFloat(speedSelect.value);
            updateSpeedDisplay();
        }
        
        function increaseSpeed() {
            const speedSelect = document.getElementById('speedSelect');
            const currentIndex = speedSelect.selectedIndex;
            if (currentIndex < speedSelect.options.length - 1) {
                speedSelect.selectedIndex = currentIndex + 1;
                speedMultiplier = parseFloat(speedSelect.value);
                updateSpeedDisplay();
            }
        }
        
        function decreaseSpeed() {
            const speedSelect = document.getElementById('speedSelect');
            const currentIndex = speedSelect.selectedIndex;
            if (currentIndex > 0) {
                speedSelect.selectedIndex = currentIndex - 1;
                speedMultiplier = parseFloat(speedSelect.value);
                updateSpeedDisplay();
            }
        }
        
        function updateSpeedDisplay() {
            const speedText = document.querySelector('.rotation-status');
            if (speedText) {
                let speedName = '';
                switch(speedMultiplier) {
                    case 0.5: speedName = 'Very Slow'; break;
                    case 1: speedName = 'Normal'; break;
                    case 1.5: speedName = 'Fast'; break;
                    case 2: speedName = 'Very Fast'; break;
                    case 3: speedName = 'Extreme'; break;
                    default: speedName = 'Custom';
                }
                speedText.innerHTML = `<span>🔄</span><span>Rotating - ${speedName}</span><span>⟳</span>`;
            }
        }
        
        // Drag to rotate functionality
        function initDragToRotate() {
            const container = document.getElementById('carouselContainer');
            if (!container) return;
            
            container.addEventListener('mousedown', (e) => {
                if (e.target.closest('.control-btn')) return;
                isDragging = true;
                dragStartX = e.clientX;
                dragStartAngle = currentAngle;
                container.style.cursor = 'grabbing';
                e.preventDefault();
            });
            
            window.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                const deltaX = e.clientX - dragStartX;
                const deltaAngle = deltaX * 0.5; // Sensitivity
                currentAngle = dragStartAngle + deltaAngle;
                updateAllPositions();
            });
            
            window.addEventListener('mouseup', () => {
                isDragging = false;
                container.style.cursor = 'grab';
            });
            
            // Touch support for mobile
            container.addEventListener('touchstart', (e) => {
                if (e.target.closest('.control-btn')) return;
                isDragging = true;
                dragStartX = e.touches[0].clientX;
                dragStartAngle = currentAngle;
                e.preventDefault();
            });
            
            window.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                const deltaX = e.touches[0].clientX - dragStartX;
                const deltaAngle = deltaX * 0.5;
                currentAngle = dragStartAngle + deltaAngle;
                updateAllPositions();
            });
            
            window.addEventListener('touchend', () => {
                isDragging = false;
            });
        }
        
        // Handle window resize
        let resizeTimeout;
        function handleResize() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                updateAllPositions();
            }, 100);
        }
        
        // Initialize everything
        document.addEventListener('DOMContentLoaded', () => {
            if (imageCount > 0) {
                initCarousel();
                startContinuousRotation();
                initDragToRotate();
                window.addEventListener('resize', handleResize);
                
                // Set initial speed display
                updateSpeedDisplay();
            }
        });
        
        // Auto-refresh on upload
        const uploadForm = document.getElementById('uploadForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function() {
                setTimeout(() => {
                    location.reload();
                }, 1500);
            });
        }
        
        // Keyboard controls
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                currentAngle -= 10;
                updateAllPositions();
                e.preventDefault();
            } else if (e.key === 'ArrowRight') {
                currentAngle += 10;
                updateAllPositions();
                e.preventDefault();
            } else if (e.key === 'Home') {
                resetRotation();
                e.preventDefault();
            } else if (e.key === '+' || e.key === '=') {
                increaseSpeed();
                e.preventDefault();
            } else if (e.key === '-' || e.key === '_') {
                decreaseSpeed();
                e.preventDefault();
            }
        });
    </script>
</body>
</html>