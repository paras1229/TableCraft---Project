<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function app_name(): string {
    return 'TableCraft';
}

function theme_mode(): string {
    if (!empty($_COOKIE['tc_theme']) && in_array($_COOKIE['tc_theme'], ['bright', 'dark'], true)) {
        return $_COOKIE['tc_theme'];
    }
    return 'bright';
}

function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function render_flash(): string {
    if (empty($_SESSION['flash'])) return '';
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $type = in_array($flash['type'], ['success', 'danger', 'warning', 'info'], true) ? $flash['type'] : 'info';
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show shadow-sm" role="alert">'
        . e($flash['message']) .
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: /TableCraft_Project/auth/login.php');
        exit;
    }
}

function category_templates(): array {
    return [
        'student_record' => [
            'label' => 'Student Record',
            'icon' => 'bi-mortarboard-fill',
            'columns' => ['Roll No', 'Student Name', 'Class', 'Semester', 'Mobile No'],
            'hint' => 'Best for class lists, attendance, result sheets and student profiles.',
        ],
        'shop_order' => [
            'label' => 'Shop Order',
            'icon' => 'bi-bag-check-fill',
            'columns' => ['Order No', 'Customer Name', 'Item', 'Qty', 'Amount'],
            'hint' => 'Use for bill slips, POS records and daily shop orders.',
        ],
        'employee_info' => [
            'label' => 'Employee Info',
            'icon' => 'bi-people-fill',
            'columns' => ['Employee ID', 'Employee Name', 'Department', 'Designation', 'Salary'],
            'hint' => 'Useful for HR master records and payroll summaries.',
        ],
        'library_book_list' => [
            'label' => 'Library Book List',
            'icon' => 'bi-book-fill',
            'columns' => ['Book ID', 'Title', 'Author', 'Category', 'Status'],
            'hint' => 'Track issue status, shelves and catalog entries.',
        ],
        'product_stock' => [
            'label' => 'Product Stock',
            'icon' => 'bi-box-seam-fill',
            'columns' => ['Product ID', 'Product Name', 'Category', 'Stock', 'Rate'],
            'hint' => 'Ideal for stock register, inventory and warehouse tables.',
        ],
        'commercial_category' => [
            'label' => 'Commercial Category',
            'icon' => 'bi-receipt-cutoff',
            'columns' => ['Invoice No', 'Client Name', 'Service', 'Amount', 'Due Date'],
            'hint' => 'Great for commercial records, invoices and service lists.',
        ],
        'custom_category' => [
            'label' => 'Custom Category Editor',
            'icon' => 'bi-sliders2',
            'columns' => ['Column 1', 'Column 2', 'Column 3', 'Column 4'],
            'hint' => 'Create your own fields for any business or academic use.',
        ],
    ];
}

function category_list_for_select(array $custom = []): array {
    $categories = category_templates();
    $out = [];
    foreach ($categories as $key => $item) {
        $out[$key] = $item['label'];
    }
    foreach ($custom as $cat) {
        $out['custom_' . $cat['id']] = $cat['name'];
    }
    return $out;
}

function parse_rows(string $raw, array $columns): array {
    $rows = [];
    $lines = preg_split('/\r\n|\r|\n/', trim($raw));
    if (!$lines || trim($raw) === '') {
        return [];
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = preg_split('/\s*\|\s*|\t|,/', $line);
        $parts = array_map('trim', $parts);
        $row = [];
        foreach ($columns as $idx => $column) {
            $row[$column] = $parts[$idx] ?? '';
        }
        $rows[] = $row;
    }
    return $rows;
}

function normalize_columns_from_text(string $text): array {
    $parts = preg_split('/\s*\|\s*|\s*,\s*/', trim($text));
    $cols = [];
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part !== '') $cols[] = $part;
    }
    return $cols ?: ['Column 1', 'Column 2', 'Column 3'];
}

function user_stats(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_tables FROM saved_tables WHERE user_id = ?");
    $stmt->execute([$userId]);
    $tables = (int)($stmt->fetch()['total_tables'] ?? 0);

    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_custom FROM custom_categories WHERE user_id = ?");
    $stmt->execute([$userId]);
    $custom = (int)($stmt->fetch()['total_custom'] ?? 0);

    $stmt = $pdo->prepare("SELECT rows_json FROM saved_tables WHERE user_id = ?");
    $stmt->execute([$userId]);
    $rows = 0;
    foreach ($stmt->fetchAll() as $row) {
        $decoded = json_decode($row['rows_json'] ?? '[]', true);
        $rows += is_array($decoded) ? count($decoded) : 0;
    }

    return [
        'tables' => $tables,
        'custom' => $custom,
        'rows' => $rows,
    ];
}

function fetch_recent_tables(PDO $pdo, int $userId, int $limit = 5): array {
    $stmt = $pdo->prepare("SELECT * FROM saved_tables WHERE user_id = ? ORDER BY updated_at DESC, id DESC LIMIT ?");
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetch_custom_categories(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("SELECT * FROM custom_categories WHERE user_id = ? ORDER BY created_at DESC, id DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function table_columns_for_category(PDO $pdo, int $userId, string $category): array {
    if (str_starts_with($category, 'custom_')) {
        $id = (int)substr($category, 7);
        $stmt = $pdo->prepare("SELECT columns_json FROM custom_categories WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch();
        if ($row && !empty($row['columns_json'])) {
            $columns = json_decode($row['columns_json'], true);
            if (is_array($columns) && $columns) return $columns;
        }
        return ['Column 1', 'Column 2', 'Column 3'];
    }

    $templates = category_templates();
    return $templates[$category]['columns'] ?? ['Column 1', 'Column 2', 'Column 3'];
}

function category_label(PDO $pdo, int $userId, string $category): string {
    if (str_starts_with($category, 'custom_')) {
        $id = (int)substr($category, 7);
        $stmt = $pdo->prepare("SELECT name FROM custom_categories WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch();
        return $row['name'] ?? 'Custom Category';
    }
    $templates = category_templates();
    return $templates[$category]['label'] ?? 'Category';
}

function save_table_record(PDO $pdo, int $userId, array $data, ?int $existingId = null): int {
    $now = date('Y-m-d H:i:s');
    $payload = [
        'user_id' => $userId,
        'title' => $data['title'],
        'category' => $data['category'],
        'columns_json' => json_encode($data['columns'], JSON_UNESCAPED_UNICODE),
        'rows_json' => json_encode($data['rows'], JSON_UNESCAPED_UNICODE),
        'theme_mode' => $data['theme_mode'] ?? 'bright',
        'notes' => $data['notes'] ?? '',
        'updated_at' => $now,
    ];
    if ($existingId) {
        $stmt = $pdo->prepare("UPDATE saved_tables SET title=:title, category=:category, columns_json=:columns_json, rows_json=:rows_json, theme_mode=:theme_mode, notes=:notes, updated_at=:updated_at WHERE id=:id AND user_id=:user_id");
        $payload['id'] = $existingId;
        $stmt->execute($payload);
        return $existingId;
    }
    $stmt = $pdo->prepare("INSERT INTO saved_tables (user_id, title, category, columns_json, rows_json, theme_mode, notes, created_at, updated_at) VALUES (:user_id, :title, :category, :columns_json, :rows_json, :theme_mode, :notes, :updated_at, :updated_at)");
    $stmt->execute($payload);
    return (int)$pdo->lastInsertId();
}

function get_table_record(PDO $pdo, int $userId, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM saved_tables WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}
