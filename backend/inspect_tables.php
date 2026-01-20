<?php
try {
    dump(array_map(fn($c) => $c->Field, DB::select('DESCRIBE products')));
} catch (\Exception $e) {
    echo "Products Error: " . $e->getMessage() . "\n";
}

try {
    dump(array_map(fn($c) => $c->Field, DB::select('DESCRIBE orders')));
} catch (\Exception $e) {
    echo "Orders Error: " . $e->getMessage() . "\n";
}
