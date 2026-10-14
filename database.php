<?php
/**
 * Arti Vaniyar Internships Hub storage layer.
 *
 * The original project used mysqli, but some ByteXL PHP runtimes do not ship
 * with the mysqli extension. This version uses a small JSON datastore, so the
 * portal runs without any PHP database extension. The MySQL credentials are
 * kept below for reference. The current app still reads/writes JSON files; these
 * constants do not establish a MySQL connection by themselves.
 */

const DB_HOST = 'db01.dbhost.dev';
const DB_PORT = 5051;
const DB_NAME = 'db_455n888f7';
const DB_USER = 'user_455n888f7';
const DB_PASS = 'p455n888f7';

define('DATA_DIR', dirname(__DIR__, 2) . '/arti_vaniyar_internships_hub_data');
define('DATA_FILE', DATA_DIR . '/portal.json');

function db_default(): array {
    return [
        'users' => [],
        'students' => [],
        'companies' => [],
        'internships' => [],
        'applications' => [],
        'counters' => [
            'users' => 0,
            'students' => 0,
            'companies' => 0,
            'internships' => 0,
            'applications' => 0,
        ],
    ];
}

function db_boot(): void {
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0775, true);
    }
    if (!file_exists(DATA_FILE)) {
        @file_put_contents(DATA_FILE, json_encode(db_default(), JSON_PRETTY_PRINT), LOCK_EX);
    }
}

function db_load(): array {
    db_boot();
    $raw = @file_get_contents(DATA_FILE);
    $data = $raw ? json_decode($raw, true) : null;
    if (!is_array($data)) {
        $data = db_default();
    }
    foreach (db_default() as $key => $value) {
        if (!array_key_exists($key, $data) || !is_array($data[$key])) {
            $data[$key] = $value;
        }
    }
    return $data;
}

function db_save(array $data): bool {
    db_boot();
    $tmp = DATA_FILE . '.tmp';
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) return false;
    if (@file_put_contents($tmp, $json, LOCK_EX) === false) return false;
    return @rename($tmp, DATA_FILE);
}

function db_next_id(array &$data, string $table): int {
    $data['counters'][$table] = (int)($data['counters'][$table] ?? 0) + 1;
    return $data['counters'][$table];
}

function db_now(): string {
    return date('Y-m-d H:i:s');
}

function db_find_user_by_email(array $data, string $email): ?array {
    foreach ($data['users'] as $user) {
        if (strcasecmp($user['email'], $email) === 0) return $user;
    }
    return null;
}

function db_user(array $data, int $id): ?array {
    foreach ($data['users'] as $user) if ((int)$user['id'] === $id) return $user;
    return null;
}

function db_student_by_user(array $data, int $userId): ?array {
    foreach ($data['students'] as $row) if ((int)$row['user_id'] === $userId) return $row;
    return null;
}

function db_company_by_user(array $data, int $userId): ?array {
    foreach ($data['companies'] as $row) if ((int)$row['user_id'] === $userId) return $row;
    return null;
}

function db_company(array $data, int $id): ?array {
    foreach ($data['companies'] as $row) if ((int)$row['id'] === $id) return $row;
    return null;
}

function db_internship(array $data, int $id): ?array {
    foreach ($data['internships'] as $row) if ((int)$row['id'] === $id) return $row;
    return null;
}

function db_sort_newest(array &$rows, string $field = 'created_at'): void {
    usort($rows, function ($a, $b) use ($field) {
        return strcmp((string)($b[$field] ?? ''), (string)($a[$field] ?? ''));
    });
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function require_role(string $role, string $loginPath = '../login.php'): void {
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: ' . $loginPath);
        exit;
    }
}

function status_class(string $status): string {
    return strtolower(str_replace(' ', '-', $status));
}
?>
