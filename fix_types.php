<?php

$dir = __DIR__ . '/app/Filament/Resources';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), 'Resource.php') !== false) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Fix navigationGroup type
        $content = preg_replace('/protected static \?string \$navigationGroup = /', 'protected static string|\UnitEnum|null $navigationGroup = ', $content);
        
        // Fix navigationIcon type
        $content = preg_replace('/protected static \?string \$navigationIcon = /', 'protected static string|\BackedEnum|null $navigationIcon = ', $content);
        
        file_put_contents($path, $content);
        echo "Fixed type in " . $file->getFilename() . "\n";
    }
}
