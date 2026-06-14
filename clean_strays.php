<?php

$dir = __DIR__ . '/app/Filament/Resources';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), 'Resource.php') !== false) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Remove stray lines containing exactly "    ';" or "';"
        $content = preg_replace('/^\s*\';\s*$/m', '', $content);
        
        file_put_contents($path, $content);
        echo "Cleaned " . $file->getFilename() . "\n";
    }
}
