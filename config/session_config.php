<?php
// Configurações de cookie antes de iniciar a sessão
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');
ini_set('session.cookie_secure', false);
ini_set('session.cookie_httponly', true);
ini_set('session.use_strict_mode', true);

// Define o tempo de vida da sessão para 8 horas
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Renova o ID da sessão periodicamente para segurança
if (!isset($_SESSION['last_regeneration']) || (time() - $_SESSION['last_regeneration']) > 3600) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Debug para verificar o estado da sessão
error_log('Session Config - Session ID: ' . session_id());
error_log('Session Config - Cookie Path: ' . ini_get('session.cookie_path'));
error_log('Session Config - Session Status: ' . session_status()); 