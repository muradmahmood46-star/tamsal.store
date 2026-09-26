<?php
// Standalone View & Server Cache Cleaner for Namartzone
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

$gitOutput = 'Git not executed';
if (function_exists('shell_exec')) {
    $gitOutput = shell_exec('git pull origin main 2>&1');
}


$viewPath = __DIR__ . '/core/storage/framework/views';
$deleted = 0;
if (file_exists($viewPath)) {
    foreach (glob($viewPath . '/*.php') as $file) {
        @unlink($file);
        $deleted++;
    }
}

// Find last exception in laravel.log
$lastException = 'No errors logged';
$logFile = __DIR__ . '/core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    if ($content) {
        $errPos = strrpos($content, 'local.ERROR:');
        if ($errPos === false) {
            $errPos = strrpos($content, '.ERROR:');
        }
        if ($errPos !== false) {
            $lastException = substr($content, $errPos, 1000);
        }
    }
}
echo "<div style='background:#b91c1c; color:#fff; padding:15px; font-family:monospace; font-size:14px; white-space:pre-wrap; word-break:break-all;'>=== LAST ERROR ===\n" . htmlspecialchars($lastException) . "</div>";


$cachePath = __DIR__ . '/core/storage/framework/cache/data';
if (file_exists($cachePath)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        if ($fileinfo->isFile()) {
            @unlink($fileinfo->getRealPath());
        }
    }
}

// Clear Bootstrap Cache
$bootstrapCache = __DIR__ . '/core/bootstrap/cache';
if (file_exists($bootstrapCache)) {
    foreach (['config.php', 'routes.php', 'packages.php', 'services.php'] as $bFile) {
        if (file_exists($bootstrapCache . '/' . $bFile)) {
            @unlink($bootstrapCache . '/' . $bFile);
        }
    }
}

// Auto-sync database columns safely if .env exists
$envFile = __DIR__ . '/core/.env';
if (!file_exists($envFile)) {
    $envFile = __DIR__ . '/.env';
}
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    preg_match('/DB_HOST=(.*)/', $envContent, $dbHost);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $dbName);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $dbUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $dbPass);
    preg_match('/DB_PORT=(.*)/', $envContent, $dbPort);
    
    $host = isset($dbHost[1]) ? trim($dbHost[1], " \t\n\r\0\x0B\"'") : 'localhost';
    $dbname = isset($dbName[1]) ? trim($dbName[1], " \t\n\r\0\x0B\"'") : '';
    $user = isset($dbUser[1]) ? trim($dbUser[1], " \t\n\r\0\x0B\"'") : '';
    $pass = isset($dbPass[1]) ? trim($dbPass[1], " \t\n\r\0\x0B\"'") : '';
    $port = isset($dbPort[1]) ? trim($dbPort[1], " \t\n\r\0\x0B\"'") : '3306';
}

$dbStatus = [];
if (!empty($dbname)) {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // items.item_variants
        $stmt = $pdo->query("SHOW COLUMNS FROM `items` LIKE 'item_variants'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `items` ADD COLUMN `item_variants` LONGTEXT NULL AFTER `stock`");
            $dbStatus[] = "✔ Database column `items.item_variants` created successfully!";
        }

        // items.is_custom_rating
        $stmt = $pdo->query("SHOW COLUMNS FROM `items` LIKE 'is_custom_rating'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `items` ADD COLUMN `is_custom_rating` TINYINT DEFAULT 0 AFTER `stock`");
            $dbStatus[] = "✔ Database column `items.is_custom_rating` created successfully!";
        }

        // items.custom_rating
        $stmt = $pdo->query("SHOW COLUMNS FROM `items` LIKE 'custom_rating'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `items` ADD COLUMN `custom_rating` DECIMAL(3,2) NULL DEFAULT 5.00 AFTER `is_custom_rating`");
            $dbStatus[] = "✔ Database column `items.custom_rating` created successfully!";
        }

        // items.custom_rating_count
        $stmt = $pdo->query("SHOW COLUMNS FROM `items` LIKE 'custom_rating_count'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `items` ADD COLUMN `custom_rating_count` INT NULL DEFAULT 0 AFTER `custom_rating`");
            $dbStatus[] = "✔ Database column `items.custom_rating_count` created successfully!";
        }

        // Auto-sync items slug to sku where sku is available for short URLs and trim any whitespaces
        try {
            $pdo->exec("UPDATE `items` SET `sku` = TRIM(`sku`) WHERE `sku` IS NOT NULL");
            $pdo->exec("UPDATE `items` SET `slug` = TRIM(`slug`) WHERE `slug` IS NOT NULL");
            $pdo->exec("UPDATE `items` SET `slug` = `sku` WHERE `sku` IS NOT NULL AND `sku` != '' AND (`slug` IS NULL OR `slug` != `sku`)");
            $dbStatus[] = "✔ Product slugs and SKUs trimmed and synced for clean /p/{sku} links!";
        } catch (\Throwable $e) {}

        // reviews.customer_name
        $stmt = $pdo->query("SHOW COLUMNS FROM `reviews` LIKE 'customer_name'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `reviews` ADD COLUMN `customer_name` VARCHAR(255) NULL AFTER `user_id`");
            $dbStatus[] = "✔ Database column `reviews.customer_name` created successfully!";
        }

        // reviews.is_admin_added
        $stmt = $pdo->query("SHOW COLUMNS FROM `reviews` LIKE 'is_admin_added'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `reviews` ADD COLUMN `is_admin_added` TINYINT DEFAULT 0 AFTER `customer_name`");
            $dbStatus[] = "✔ Database column `reviews.is_admin_added` created successfully!";
        }

        // settings.whatsapp_enabled
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_enabled'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_enabled` TINYINT DEFAULT 0");
            $dbStatus[] = "✔ Database column `settings.whatsapp_enabled` created successfully!";
        }

        // settings.whatsapp_phone_number_id
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_phone_number_id'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_phone_number_id` VARCHAR(100) NULL");
            $dbStatus[] = "✔ Database column `settings.whatsapp_phone_number_id` created successfully!";
        }

        // settings.whatsapp_access_token
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_access_token'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_access_token` TEXT NULL");
            $dbStatus[] = "✔ Database column `settings.whatsapp_access_token` created successfully!";
        }

        // settings.whatsapp_from_number
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_from_number'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_from_number` VARCHAR(30) NULL");
            $dbStatus[] = "✔ Database column `settings.whatsapp_from_number` created successfully!";
        }

        // settings.whatsapp_template_order_confirmed
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_template_order_confirmed'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_template_order_confirmed` VARCHAR(100) DEFAULT 'order_confirmed'");
            $dbStatus[] = "✔ Database column `settings.whatsapp_template_order_confirmed` created successfully!";
        }

        // settings.whatsapp_template_in_progress
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_template_in_progress'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_template_in_progress` VARCHAR(100) DEFAULT 'order_in_progress'");
            $dbStatus[] = "✔ Database column `settings.whatsapp_template_in_progress` created successfully!";
        }

        // settings.whatsapp_template_delivered
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_template_delivered'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_template_delivered` VARCHAR(100) DEFAULT 'order_delivered'");
            $dbStatus[] = "✔ Database column `settings.whatsapp_template_delivered` created successfully!";
        }

        // settings.whatsapp_template_canceled
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'whatsapp_template_canceled'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` ADD COLUMN `whatsapp_template_canceled` VARCHAR(100) DEFAULT 'order_canceled'");
            $dbStatus[] = "✔ Database column `settings.whatsapp_template_canceled` created successfully!";
        }

        // deals table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'deals'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `deals` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `vendor_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `photo` varchar(255) DEFAULT NULL,
                `description` text DEFAULT NULL,
                `discount_type` enum('fixed','percent') NOT NULL DEFAULT 'percent',
                `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
                `original_price` decimal(12,2) NOT NULL DEFAULT 0.00,
                `discounted_price` decimal(12,2) NOT NULL DEFAULT 0.00,
                `delivery_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
                `is_free_delivery` tinyint(1) NOT NULL DEFAULT 0,
                `duration_days` int(11) NOT NULL DEFAULT 1,
                `start_date` datetime DEFAULT NULL,
                `end_date` datetime DEFAULT NULL,
                `status` tinyint(4) NOT NULL DEFAULT 1,
                `orders_count` int(10) unsigned NOT NULL DEFAULT 0,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `deals_slug_unique` (`slug`),
                KEY `deals_vendor_id_index` (`vendor_id`),
                KEY `deals_end_date_index` (`end_date`),
                KEY `deals_status_index` (`status`),
                KEY `deals_orders_count_index` (`orders_count`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `deals` created successfully!";
        } else {
            // Check individual columns of deals
            $stmt = $pdo->query("SHOW COLUMNS FROM `deals` LIKE 'photo'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `deals` ADD COLUMN `photo` VARCHAR(255) NULL AFTER `slug`");
                $dbStatus[] = "✔ Database column `deals.photo` created successfully!";
            }

            $stmt = $pdo->query("SHOW COLUMNS FROM `deals` LIKE 'delivery_charge'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `deals` ADD COLUMN `delivery_charge` DECIMAL(12,2) DEFAULT 0.00 AFTER `discounted_price`");
                $dbStatus[] = "✔ Database column `deals.delivery_charge` created successfully!";
            }

            $stmt = $pdo->query("SHOW COLUMNS FROM `deals` LIKE 'is_free_delivery'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `deals` ADD COLUMN `is_free_delivery` TINYINT(1) DEFAULT 0 AFTER `delivery_charge`");
                $dbStatus[] = "✔ Database column `deals.is_free_delivery` created successfully!";
            }

            $stmt = $pdo->query("SHOW COLUMNS FROM `deals` LIKE 'advance_discount'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `deals` ADD COLUMN `advance_discount` DECIMAL(12,2) DEFAULT 0.00 AFTER `is_free_delivery`");
                $dbStatus[] = "✔ Database column `deals.advance_discount` created successfully!";
            }

            $stmt = $pdo->query("SHOW COLUMNS FROM `deals` LIKE 'duration_days'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `deals` ADD COLUMN `duration_days` INT DEFAULT 1 AFTER `is_free_delivery`");
                $dbStatus[] = "✔ Database column `deals.duration_days` created successfully!";
            }

            $stmt = $pdo->query("SHOW COLUMNS FROM `deals` LIKE 'orders_count'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `deals` ADD COLUMN `orders_count` INT UNSIGNED DEFAULT 0 AFTER `status`");
                $dbStatus[] = "✔ Database column `deals.orders_count` created successfully!";
            }
        }

        // deal_items table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'deal_items'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `deal_items` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `deal_id` bigint(20) unsigned NOT NULL,
                `item_id` bigint(20) unsigned NOT NULL,
                `original_price` decimal(12,2) NOT NULL DEFAULT 0.00,
                `discounted_price` decimal(12,2) NOT NULL DEFAULT 0.00,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `deal_items_deal_id_index` (`deal_id`),
                KEY `deal_items_item_id_index` (`item_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `deal_items` created successfully!";
        }

        // sellers table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'sellers'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `sellers` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
                `shop_name` varchar(255) DEFAULT NULL,
                `shop_address` text DEFAULT NULL,
                `product_types` varchar(255) DEFAULT NULL,
                `courier_company` varchar(255) DEFAULT NULL,
                `shop_phone` varchar(255) DEFAULT NULL,
                `shop_email` varchar(255) DEFAULT NULL,
                `shop_logo` varchar(255) DEFAULT NULL,
                `shop_banner` varchar(255) DEFAULT NULL,
                `shop_details` text DEFAULT NULL,
                `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
                `status` tinyint(4) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `sellers_user_id_index` (`user_id`),
                KEY `sellers_status_index` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `sellers` created successfully!";
        } else {
            $stmt = $pdo->query("SHOW COLUMNS FROM `sellers` LIKE 'balance'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `sellers` ADD COLUMN `balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `shop_details`");
                $dbStatus[] = "✔ Database column `sellers.balance` created successfully!";
            }
            $stmt = $pdo->query("SHOW COLUMNS FROM `sellers` LIKE 'status'");
            if ($stmt && $stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `sellers` ADD COLUMN `status` TINYINT NOT NULL DEFAULT 1 AFTER `balance`");
                $dbStatus[] = "✔ Database column `sellers.status` created successfully!";
            }
        }

        // store_requests table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'store_requests'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `store_requests` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `first_name` varchar(255) DEFAULT NULL,
                `last_name` varchar(255) DEFAULT NULL,
                `email` varchar(255) DEFAULT NULL,
                `phone` varchar(255) DEFAULT NULL,
                `cnic` varchar(255) DEFAULT NULL,
                `shop_name` varchar(255) DEFAULT NULL,
                `shop_address` text DEFAULT NULL,
                `product_types` varchar(255) DEFAULT NULL,
                `courier_company` varchar(255) DEFAULT NULL,
                `payment_method` varchar(255) DEFAULT NULL,
                `transaction_id` varchar(255) DEFAULT NULL,
                `payment_screenshot` varchar(255) DEFAULT NULL,
                `status` varchar(50) NOT NULL DEFAULT 'Pending',
                `seller_status` varchar(50) NOT NULL DEFAULT 'Pending',
                `reject_reason` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `store_requests_user_id_index` (`user_id`),
                KEY `store_requests_status_index` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `store_requests` created successfully!";
        }

        // receiving_accounts table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'receiving_accounts'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `receiving_accounts` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `payment_method` varchar(255) NOT NULL,
                `account_name` varchar(255) NOT NULL,
                `account_number` varchar(255) NOT NULL,
                `note` text DEFAULT NULL,
                `status` tinyint(4) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `receiving_accounts_status_index` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `receiving_accounts` created successfully!";
        }

        // vendor_transactions table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'vendor_transactions'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `vendor_transactions` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `seller_id` bigint(20) unsigned NOT NULL,
                `order_id` bigint(20) unsigned DEFAULT NULL,
                `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
                `type` varchar(50) NOT NULL DEFAULT 'credit',
                `details` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `vendor_transactions_seller_id_index` (`seller_id`),
                KEY `vendor_transactions_order_id_index` (`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `vendor_transactions` created successfully!";
        }

        // deposit_requests table check and creation
        $stmt = $pdo->query("SHOW TABLES LIKE 'deposit_requests'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE `deposit_requests` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `seller_id` bigint(20) unsigned NOT NULL,
                `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
                `payment_method` varchar(255) DEFAULT NULL,
                `transaction_id` varchar(255) DEFAULT NULL,
                `screenshot` varchar(255) DEFAULT NULL,
                `status` varchar(50) NOT NULL DEFAULT 'Pending',
                `note` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `deposit_requests_seller_id_index` (`seller_id`),
                KEY `deposit_requests_status_index` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $dbStatus[] = "✔ Database table `deposit_requests` created successfully!";
        }

        // users columns check
        $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'is_seller'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `is_seller` TINYINT DEFAULT 0 AFTER `email_verify`");
            $dbStatus[] = "✔ Database column `users.is_seller` created successfully!";
        }
        $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'is_seller_blocked'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `is_seller_blocked` TINYINT DEFAULT 0 AFTER `is_seller`");
            $dbStatus[] = "✔ Database column `users.is_seller_blocked` created successfully!";
        }

        // items columns check
        $stmt = $pdo->query("SHOW COLUMNS FROM `items` LIKE 'vendor_id'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `items` ADD COLUMN `vendor_id` BIGINT UNSIGNED DEFAULT 0 AFTER `tax_id`");
            $dbStatus[] = "✔ Database column `items.vendor_id` created successfully!";
        }
        $stmt = $pdo->query("SHOW COLUMNS FROM `items` LIKE 'is_hidden_by_block'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `items` ADD COLUMN `is_hidden_by_block` TINYINT DEFAULT 0 AFTER `status`");
            $dbStatus[] = "✔ Database column `items.is_hidden_by_block` created successfully!";
        }

        // orders columns check
        $stmt = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'vendor_id'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `orders` ADD COLUMN `vendor_id` BIGINT UNSIGNED DEFAULT 0 AFTER `user_id`");
            $dbStatus[] = "✔ Database column `orders.vendor_id` created successfully!";
        }

        // settings columns check
        $stmt = $pdo->query("SHOW COLUMNS FROM `settings` LIKE 'store_opening_fee'");
        if ($stmt && $stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE `settings` 
                ADD COLUMN `store_opening_fee` DECIMAL(12,2) DEFAULT 0.00,
                ADD COLUMN `is_store_opening_free` TINYINT DEFAULT 1,
                ADD COLUMN `vendor_free_orders` INT DEFAULT 5,
                ADD COLUMN `vendor_min_balance` DECIMAL(12,2) DEFAULT 500.00,
                ADD COLUMN `vendor_commission_percent` DECIMAL(5,2) DEFAULT 5.00");
            $dbStatus[] = "✔ Database columns for store settings created successfully!";
        }

        // Favicon Sync
        try {
            $favStmt = $pdo->query("SELECT `favicon` FROM `settings` WHERE `id` = 1 LIMIT 1");
            if ($favStmt && $row = $favStmt->fetch(PDO::FETCH_ASSOC)) {
                $favFile = $row['favicon'] ?? null;
                if ($favFile) {
                    $srcCandidates = [
                        __DIR__ . '/core/public/storage/images/' . $favFile,
                        __DIR__ . '/core/storage/app/public/images/' . $favFile,
                        __DIR__ . '/assets/images/' . $favFile
                    ];
                    foreach ($srcCandidates as $cand) {
                        if (file_exists($cand)) {
                            @copy($cand, __DIR__ . '/favicon.ico');
                            @copy($cand, __DIR__ . '/core/public/favicon.ico');
                            $dbStatus[] = "✔ Favicon synced to root `favicon.ico` successfully!";
                            break;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {}

        if (empty($dbStatus)) {
            $dbStatus[] = "✔ All database columns (variants, rating management, bundles & WhatsApp) are up to date.";
        }
    } catch (\Exception $e) {
        $dbStatus[] = "DB Status Note: " . htmlspecialchars($e->getMessage());
    }
}

echo '<div style="font-family: Arial, sans-serif; text-align: center; padding: 50px; max-width: 600px; margin: 0 auto;">';
echo '<h2 style="color: #16a34a;">All Server & View Caches Cleared Successfully!</h2>';
echo '<p style="font-size: 16px; color: #555;">Cleared ' . $deleted . ' cached Blade templates and system cache files.</p>';
foreach ($dbStatus as $statusLine) {
    echo '<p style="font-size: 14.5px; color: #15803d; margin: 5px 0;">' . $statusLine . '</p>';
}
echo '<a href="/" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold;">Go to Website</a>';
echo '</div>';

// === DEBUG INFO FOR 404 ISSUE ===
echo '<div style="font-family: monospace; text-align: left; padding: 20px; max-width: 800px; margin: 30px auto; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px;">';
echo '<h3 style="color: #1e40af;">Debug Info (Home 404 Issue)</h3>';
echo '<pre style="white-space: pre-wrap; word-break: break-all;">';

echo "Script location: " . __FILE__ . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Server name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "PHP version: " . phpversion() . "\n";
echo "Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n\n";

// Check .env APP_URL
echo "=== .ENV CHECK ===\n";
if (file_exists($envFile)) {
    $lines2 = file($envFile);
    foreach ($lines2 as $line2) {
        $line2 = trim($line2);
        if (strpos($line2, 'APP_URL') !== false || strpos($line2, 'APP_ENV') !== false || strpos($line2, 'APP_DEBUG') !== false) {
            echo $line2 . "\n";
        }
    }
}

// Check homepage categories  
echo "\n=== HOMEPAGE CATEGORIES CHECK ===\n";
if (!empty($dbname)) {
    try {
        $stmt = $pdo->query("SELECT feature_category, popular_category, home_4_popular_category FROM home_cutomizes LIMIT 1");
        $hc = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($hc) {
            $fc = json_decode($hc['feature_category'], true);
            if ($fc) {
                for ($i = 1; $i <= 4; $i++) {
                    $catId = $fc['category_id' . $i] ?? null;
                    if ($catId) {
                        $cs = $pdo->query("SELECT id, name FROM categories WHERE id = " . intval($catId));
                        $cat = $cs->fetch(PDO::FETCH_ASSOC);
                        echo "Feature Cat $i (ID:$catId): " . ($cat ? $cat['name'] : "*** MISSING ***") . "\n";
                    }
                }
            }
            
            $pc = json_decode($hc['popular_category'], true);
            if ($pc) {
                for ($i = 1; $i <= 4; $i++) {
                    $catId = $pc['category_id' . $i] ?? null;
                    if ($catId) {
                        $cs = $pdo->query("SELECT id, name FROM categories WHERE id = " . intval($catId));
                        $cat = $cs->fetch(PDO::FETCH_ASSOC);
                        echo "Popular Cat $i (ID:$catId): " . ($cat ? $cat['name'] : "*** MISSING ***") . "\n";
                    }
                }
            }

            $ts = $pdo->query("SELECT theme FROM settings LIMIT 1");
            $theme = $ts->fetch(PDO::FETCH_ASSOC);
            echo "Theme: " . ($theme['theme'] ?? 'unknown') . "\n";
        }
        
        echo "\n=== MENU DATA ===\n";
        try {
            $ms = $pdo->query("SELECT menus FROM menus WHERE id = 1");
            if ($ms) {
                $menu = $ms->fetch(PDO::FETCH_ASSOC);
                if ($menu) {
                    $links = json_decode($menu['menus'], true);
                    if ($links) {
                        foreach ($links as $lnk) {
                            if (!isset($lnk['children'])) {
                                echo "Menu: " . $lnk['text'] . " | type: " . $lnk['type'] . " | href: '" . $lnk['href'] . "'\n";
                            } else {
                                echo "Menu: " . $lnk['text'] . " (has children) | type: " . $lnk['type'] . "\n";
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            echo "Menu query error: " . $e->getMessage() . "\n";
        }
    } catch (\Throwable $e) {
        echo "Debug DB Error: " . $e->getMessage() . "\n";
    }
}

echo "</pre></div>";

echo '<div style="font-family: monospace; text-align: left; padding: 20px; max-width: 900px; margin: 30px auto; background: #1e1e1e; color: #d4d4d4; border-radius: 8px;">';
echo '<h3 style="color: #60a5fa; margin-top:0;">LATEST LARAVEL LOG</h3>';
$logFile = __DIR__ . '/core/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -30);
    foreach ($lastLines as $l) {
        $color = '#d4d4d4';
        if (strpos($l, '.ERROR') !== false) {
            $color = '#f87171; font-weight:bold;';
        } elseif (strpos($l, '#') === 0) {
            $color = '#9ca3af; font-size:12px;';
        }
        echo '<div style="color:' . $color . '; margin-bottom: 2px; word-break: break-all;">' . htmlspecialchars($l, ENT_QUOTES, 'UTF-8') . '</div>';
    }
} else {
    echo "<div>laravel.log not found</div>";
}
echo '</div>';

