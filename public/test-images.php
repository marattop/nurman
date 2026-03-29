<!DOCTYPE html>
<html>
<head>
    <title>Тест доступа к фото</title>
</head>
<body>
    <h1>Тест доступа к фото</h1>
    
    <h2>1. Прямой доступ к файлу:</h2>
    <img src="storage/products/n7Bjz7ZawNvj19uyXYhRGmiMEvkmky0rpCW6r1RM.png" alt="Direct" style="width: 200px;">
    
    <h2>2. Через asset():</h2>
    <?php
    require __DIR__.'/../../vendor/autoload.php';
    $app = require __DIR__.'/../../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    ?>
    <img src="<?= asset('storage/products/n7Bjz7ZawNvj19uyXYhRGmiMEvkmky0rpCW6r1RM.png') ?>" alt="Asset" style="width: 200px;">
    
    <h2>3. URL:</h2>
    <p>Direct: storage/products/n7Bjz7ZawNvj19uyXYhRGmiMEvkmky0rpCW6r1RM.png</p>
    <p>Asset: <?= asset('storage/products/n7Bjz7ZawNvj19uyXYhRGmiMEvkmky0rpCW6r1RM.png') ?></p>
</body>
</html>
