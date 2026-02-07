<?php
// Generate all coffee images
$coffees = [
    'espresso' => 'Espresso',
    'cappuccino' => 'Cappuccino', 
    'latte' => 'Latte',
    'americano' => 'Americano',
    'mocha' => 'Mocha',
    'coldbrew' => 'Cold Brew',
    'macchiato' => 'Macchiato',
    'flatwhite' => 'Flat White',
    'turkish' => 'Turkish',
    'frappuccino' => 'Frappuccino'
];

foreach ($coffees as $filename => $name) {
    // Create image file using placeholder
    $url = "http://localhost/Savori/assets/images/placeholder.php?text=$name&w=400&h=300&bg=8B4513&color=FFFFFF";
    $image_data = file_get_contents($url);
    
    if ($image_data) {
        file_put_contents(__DIR__ . "/$filename.jpg", $image_data);
        echo "Generated: $filename.jpg<br>";
    }
}
?>