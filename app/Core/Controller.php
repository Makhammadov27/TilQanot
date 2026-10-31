<?php
declare(strict_types=1);
abstract class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data, EXTR_SKIP);
        $user = $_SESSION['user'] ?? null;
        $notice = $_SESSION['notice'] ?? null;
        unset($_SESSION['notice']);
        require dirname(__DIR__) . '/Views/layout.php';
    }
    protected function redirect(string $route): never {
        header('Location: index.php?route=' . rawurlencode($route)); exit;
    }
    protected function requireLogin(): array {
        if (empty($_SESSION['user'])) { $_SESSION['notice'] = 'Davom etish uchun tizimga kiring.'; $this->redirect('login'); }
        return $_SESSION['user'];
    }
    protected function requireAdmin(): array {
        $u = $this->requireLogin();
        if ($u['role'] !== 'admin') { http_response_code(403); $this->render('error', ['title'=>'Ruxsat yo‘q','message'=>'Bu bo‘lim faqat administrator uchun.']); exit; }
        return $u;
    }
    protected function requireEditor(): array {
        $u=$this->requireLogin();
        if (!in_array($u['role'],['admin','operator'],true)) { http_response_code(403); $this->render('error', ['title'=>'Ruxsat yo‘q','message'=>'Bu bo‘lim faqat administrator yoki muharrir uchun.']); exit; }
        return $u;
    }
    protected function csrf(): string {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }
    protected function verifyCsrf(): void {
        if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['_token'] ?? ''))) { http_response_code(419); exit('Sahifa muddati tugadi. Yangilab qayta urinib ko‘ring.'); }
    }
    protected function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}
