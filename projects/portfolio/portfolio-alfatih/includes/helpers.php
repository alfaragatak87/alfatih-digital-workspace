<?php
// Session functions
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Alert message functions
function setAlert($type, $message) {
    startSession();
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getAlert() {
    startSession();
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

function displayAlert() {
    $alert = getAlert();
    if ($alert) {
        $type = htmlspecialchars($alert['type']);
        $message = htmlspecialchars($alert['message']);
        echo "<div class='alert alert-{$type}'>{$message}</div>";
    }
}

// Text helper functions
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $last_space = strrpos($text, ' ');
    if ($last_space !== false) {
        $text = substr($text, 0, $last_space);
    }
    return $text . '...';
}

// URL and navigation helpers
function isCurrentPage($pageName) {
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    return ($currentPage == $pageName) ? 'active' : '';
}

// Slug generator
function generateSlug($text) {
    //
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate dividers
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

// File upload helper
function uploadFile($file, $targetDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']) {
    $fileName = basename($file['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Validate file type
    if (!in_array(strtolower($fileType), $allowedTypes)) {
        return [
            'success' => false,
            'message' => 'Hanya file ' . implode(', ', $allowedTypes) . ' yang diperbolehkan.'
        ];
    }
    
    // Generate unique filename to prevent overwriting
    $newFileName = uniqid('file_', true) . '.' . $fileType;
    $targetFilePath = $targetDir . $newFileName;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        return [
            'success' => true,
            'filename' => $newFileName
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Gagal mengupload file.'
        ];
    }
}

/**
 * Adjusts the brightness of a HEX color.
 * @param string $hex The hex color code.
 * @param int $steps A value between -255 and 255. Negative to darken, positive to lighten.
 * @return string The new hex color code.
 */
function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255.
    $steps = max(-255, min(255, $steps));

    // Normalize hex color
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Get RGB values
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));

    // Adjust brightness
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));

    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    return '#'.$r_hex.$g_hex.$b_hex;
}

// Analytics tracking function
function trackPageView() {
    global $pdo;
    
    // Periksa apakah koneksi database tersedia
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        return; // Keluar fungsi jika tidak ada koneksi
    }
    
    try {
        // Periksa apakah tabel website_analytics ada
        $stmt = $pdo->query("SHOW TABLES LIKE 'website_analytics'");
        if ($stmt->rowCount() === 0) {
            // Tabel belum ada, buat tabel dulu
            $sql = "CREATE TABLE IF NOT EXISTS `website_analytics` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `page_url` varchar(255) NOT NULL,
                `page_title` varchar(255) DEFAULT NULL,
                `visitor_ip` varchar(45) DEFAULT NULL,
                `visitor_country` varchar(100) DEFAULT NULL,
                `visitor_device` varchar(100) DEFAULT NULL,
                `visitor_browser` varchar(100) DEFAULT NULL,
                `visit_date` timestamp NOT NULL DEFAULT current_timestamp(),
                `referrer` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $pdo->exec($sql);
        }
        
        // Get page info
        $pageUrl = $_SERVER['REQUEST_URI'];
        $pageTitle = isset($GLOBALS['pageTitle']) ? $GLOBALS['pageTitle'] : basename($_SERVER['PHP_SELF']);
        
        // Get visitor info
        $visitorIp = $_SERVER['REMOTE_ADDR'] ?? null;
        $visitorCountry = null; // Would need IP geolocation service
        $visitorDevice = detectDevice();
        $visitorBrowser = detectBrowser();
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        
        // Insert data
        $stmt = $pdo->prepare("INSERT INTO website_analytics (page_url, page_title, visitor_ip, visitor_country, visitor_device, visitor_browser, referrer) VALUES (:page_url, :page_title, :visitor_ip, :visitor_country, :visitor_device, :visitor_browser, :referrer)");
        
        $stmt->bindParam(':page_url', $pageUrl);
        $stmt->bindParam(':page_title', $pageTitle);
        $stmt->bindParam(':visitor_ip', $visitorIp);
        $stmt->bindParam(':visitor_country', $visitorCountry);
        $stmt->bindParam(':visitor_device', $visitorDevice);
        $stmt->bindParam(':visitor_browser', $visitorBrowser);
        $stmt->bindParam(':referrer', $referrer);
        
        $stmt->execute();
    } catch (PDOException $e) {
        // Diam-diam tangani error, jangan mengganggu pengalaman pengguna
        // Opsional: log error ke file log jika diperlukan
        // error_log('Analytics error: ' . $e->getMessage());
    }
}

// Detect device type
function detectDevice() {
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
        return 'Mobile';
    } elseif (preg_match('/tablet|ipad|playbook|silk|android(?!.*mobile)/i', $userAgent)) {
        return 'Tablet';
    } else {
        return 'Desktop';
    }
}

// Detect browser
function detectBrowser() {
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    if (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
        return 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $userAgent)) {
        return 'Firefox';
    } elseif (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edge/i', $userAgent)) {
        return 'Chrome';
    } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
        return 'Safari';
    } elseif (preg_match('/Edge/i', $userAgent)) {
        return 'Edge';
    } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
        return 'Opera';
    } else {
        return 'Unknown';
    }
}

// Add notification for admin
function addNotification($adminId, $title, $message, $type = 'info', $link = null) {
    global $pdo;
    
    // Check if notifications table exists
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
        if ($stmt->rowCount() === 0) {
            // Tabel belum ada, buat tabel dulu
            $sql = "CREATE TABLE IF NOT EXISTS `notifications` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `admin_id` int(11) DEFAULT NULL,
                `title` varchar(255) NOT NULL,
                `message` text NOT NULL,
                `type` enum('info','success','warning','error') DEFAULT 'info',
                `is_read` tinyint(1) DEFAULT 0,
                `link` varchar(255) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $pdo->exec($sql);
        }
        
        $stmt = $pdo->prepare("INSERT INTO notifications (admin_id, title, message, type, link) VALUES (:admin_id, :title, :message, :type, :link)");
        
        $stmt->bindParam(':admin_id', $adminId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':link', $link);
        
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        // Silently fail if table doesn't exist yet
        return false;
    }
}

// Log admin activity
function logActivity($action, $details = null) {
    global $pdo;
    
    // Check if admin_logs table exists
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'admin_logs'");
        if ($stmt->rowCount() === 0) {
            // Tabel belum ada, buat tabel dulu
            $sql = "CREATE TABLE IF NOT EXISTS `admin_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `admin_id` int(11) DEFAULT NULL,
                `action` varchar(255) NOT NULL,
                `details` text DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT NULL,
                `user_agent` text DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $pdo->exec($sql);
        }
        
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, details, ip_address, user_agent) VALUES (:admin_id, :action, :details, :ip_address, :user_agent)");
        
        $adminId = $_SESSION['admin_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $stmt->bindParam(':admin_id', $adminId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->bindParam(':user_agent', $userAgent);
        
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        // Silently fail if table doesn't exist yet
        return false;
    }
}

// Check if user has permission
function hasPermission($permissionKey) {
    global $pdo;
    
    // If user is not logged in, deny access
    if (!isset($_SESSION['admin_id'])) {
        return false;
    }
    
    // Get user permissions
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'admin_roles'");
        if ($stmt->rowCount() === 0) {
            // Tabel belum ada, buat tabel dulu
            $sql = "CREATE TABLE IF NOT EXISTS `admin_roles` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `role_name` varchar(50) NOT NULL,
                `permissions` text DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            
            $pdo->exec($sql);
            
            // Insert default admin role
            $sql = "INSERT INTO `admin_roles` (`id`, `role_name`, `permissions`) VALUES
            (1, 'Super Admin', '{\"all\":true}'),
            (2, 'Editor', '{\"dashboard\":true,\"content\":true,\"media\":true,\"settings\":false,\"users\":false}'),
            (3, 'Author', '{\"dashboard\":true,\"content\":{\"view\":true,\"add\":true,\"edit\":true,\"delete\":false},\"media\":{\"view\":true,\"add\":true,\"delete\":false}}');";
            
            $pdo->exec($sql);
        }
        
        // Tambahkan kolom role_id ke tabel admin jika belum ada
        $stmt = $pdo->query("SHOW COLUMNS FROM `admin` LIKE 'role_id'");
        if ($stmt->rowCount() === 0) {
            $sql = "ALTER TABLE `admin` ADD COLUMN `role_id` int(11) DEFAULT 1";
            $pdo->exec($sql);
        }
        
        $stmt = $pdo->prepare("SELECT r.permissions 
                              FROM admin a 
                              LEFT JOIN admin_roles r ON a.role_id = r.id 
                              WHERE a.id = :id");
        $stmt->bindParam(':id', $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        $permissions = json_decode($result['permissions'] ?? '{}', true);
        
        // Super admin has all permissions
        if (isset($permissions['all']) && $permissions['all']) {
            return true;
        }
        
        // Check specific permission
        $keys = explode('.', $permissionKey);
        $currentLevel = $permissions;
        
        foreach ($keys as $key) {
            if (!isset($currentLevel[$key])) {
                return false;
            }
            
            if (is_array($currentLevel[$key])) {
                $currentLevel = $currentLevel[$key];
            } else {
                return (bool)$currentLevel[$key];
            }
        }
        
        return true;
    } catch (PDOException $e) {
        // If roles table doesn't exist yet, default to true for backward compatibility
        return true;
    }
}
?>