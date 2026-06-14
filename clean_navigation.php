<?php

$dir = __DIR__ . '/app/Filament/Resources';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), 'Resource.php') !== false) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Find multiple navigationGroup
        $content = preg_replace('/(\s*protected static \?string \$navigationGroup = \'.*?\';\s*){2,}/s', "\n    protected static ?string \$navigationGroup = '$1';\n", $content);
        
        // Actually, let's just do it manually by finding all instances and keeping only the last one, or just clean up the exact duplicates.
        // The exact duplication is:
        //         protected static ?string $navigationGroup = '...';
        //         protected static ?string $navigationGroup = '...';
        //     protected static ?string $navigationIcon = '...';
        //     protected static ?int $navigationSort = ...;
        //
        //     protected static ?int $navigationSort = ...;
        
        // Let's use regex to remove any duplicate property declarations in the class.
        $lines = explode("\n", $content);
        $newLines = [];
        $seen = [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*protected static \?(string|int) \$(navigationGroup|navigationIcon|navigationSort) =/', $line, $matches)) {
                $prop = $matches[2];
                if (!isset($seen[$prop])) {
                    $seen[$prop] = true;
                    $newLines[] = $line;
                }
            } else {
                $newLines[] = $line;
            }
        }
        
        file_put_contents($path, implode("\n", $newLines));
        echo "Cleaned " . $file->getFilename() . "\n";
    }
}
