<?php namespace App\Core {
function csrf_token(): string {
    if (!Session::get("_csrf_token")) {
        Session::set("_csrf_token", bin2hex(random_bytes(32)));
    }
    return Session::get("_csrf_token");
}
function csrf_field(): string {
    return "<input type=\"hidden\" name=\"_token\" value=\"" . csrf_token() . "\">";
}
function verify_csrf_token(string $token): bool {
    return hash_equals(Session::get("_csrf_token", ""), $token);
}
function url(string $path = ""): string {
    if (preg_match("#^(https?:)?//#i", $path)) return $path;
    $baseUrl = defined("APP_URL") ? rtrim(APP_URL, "/") : "/doan/public";
    return $baseUrl . "/" . ltrim($path, "/");
}
function route(string $path = ""): string {
   return url($path);
}
function asset(string $path): string {
    $baseUrl = defined("APP_URL") ? rtrim(APP_URL, "/") : "";
    return $baseUrl . "/assets/" . ltrim($path, "/");
}
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
function old(string $key, $default = null) {
    return Session::getFlash("_old." . $key, $default);
}
function money(float $amount, string $currency = "VND"): string {
    return number_format($amount, 0, ",", ".") . " " . $currency;
}
function date_format_custom(string $date, string $format = "d/m/Y"): string {
    if (empty($date)) return "";
    $t = strtotime($date);
    return $t ? date($format, $t) : "";
}
function pagination_range(int $c, int $t, int $r = 2): array {
    $c = max(1, $c); $t = max(1, $t);
    if ($t <= 7) return range(1, $t);
    $p = [1]; $s = max(2, $c - $r); $e = min($t - 1, $c + $r);
    if ($s > 2) $p[] = "...";
    for ($i = $s; $i <= $e; $i++) $p[] = $i;
    if ($e < $t - 1) $p[] = "...";
    $p[] = $t;
    return $p;
}
}
namespace {
function csrf_field(): string { return \App\Core\csrf_field(); }
function csrf_token(): string { return \App\Core\csrf_token(); }
function verify_csrf_token(string $token): bool { return \App\Core\verify_csrf_token($token); }
function url(string $path = ""): string { return \App\Core\url($path); }
function route(string $path = ""): string { return \App\Core\route($path); }
function asset(string $path): string { return \App\Core\asset($path); }
function e(string $value): string { return \App\Core\e($value); }
function old(string $key, $default = null) { return \App\Core\old($key, $default); }
function money(float $amount, string $currency = "VND"): string { return \App\Core\money($amount, $currency); }
function date_format_custom(string $date, string $format = "d/m/Y"): string { return \App\Core\date_format_custom($date, $format); }
function pagination_range(int $c, int $t, int $r = 2): array { return \App\Core\pagination_range($c, $t, $r); }
}
