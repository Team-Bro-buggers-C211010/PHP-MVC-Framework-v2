<?php

namespace Helper;

function isValidIdentifier(string $name): bool
{
    // Validates table/column names: only letters, numbers, and underscores
    return preg_match('/^[a-zA-Z0-9_]+$/', $name) === 1;
}