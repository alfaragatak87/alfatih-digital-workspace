<?php
function h(?string $str): string { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
function generateCSRF(): string {
function validateCSRF(): bool {
$csrf_token = generateCSRF();
