<?php
// Script to generate luxury perfume SVG bottle illustrations

$perfumes = [
    1 => ['title' => 'OUD IMPERIAL', 'sub' => 'ROYALE', 'bg1' => '#1e1b4b', 'bg2' => '#0f172a', 'bottle' => '#b91c1c', 'glow' => '#ef4444', 'cap' => '#f59e0b', 'accent' => '#fbbf24'],
    2 => ['title' => 'MIDNIGHT VELVET', 'sub' => 'ROSE', 'bg1' => '#311042', 'bg2' => '#0f172a', 'bottle' => '#701a75', 'glow' => '#e879f9', 'cap' => '#e2e8f0', 'accent' => '#f472b6'],
    3 => ['title' => 'SAPPHIRE AQUA', 'sub' => 'INTENSE', 'bg1' => '#0369a1', 'bg2' => '#0f172a', 'bottle' => '#1d4ed8', 'glow' => '#60a5fa', 'cap' => '#94a3b8', 'accent' => '#38bdf8'],
    4 => ['title' => 'CRIMSON SAFFRON', 'sub' => '& GOLD', 'bg1' => '#7f1d1d', 'bg2' => '#0f172a', 'bottle' => '#dc2626', 'glow' => '#f87171', 'cap' => '#f59e0b', 'accent' => '#fde047'],
    5 => ['title' => 'NOIR VETIVER', 'sub' => '& LEATHER', 'bg1' => '#1e293b', 'bg2' => '#090d16', 'bottle' => '#334155', 'glow' => '#94a3b8', 'cap' => '#64748b', 'accent' => '#cbd5e1'],
    6 => ['title' => 'CELESTIAL JASMINE', 'sub' => 'BLOSSOM', 'bg1' => '#831843', 'bg2' => '#0f172a', 'bottle' => '#be185d', 'glow' => '#f472b6', 'cap' => '#fbbf24', 'accent' => '#fecdd3'],
    7 => ['title' => 'AMBER NECTAR', 'sub' => 'ABSOLUTE', 'bg1' => '#78350f', 'bg2' => '#0f172a', 'bottle' => '#d97706', 'glow' => '#fbbf24', 'cap' => '#f59e0b', 'accent' => '#fef08a'],
    8 => ['title' => 'AZURE CITRUS', 'sub' => 'ELIXIR', 'bg1' => '#115e59', 'bg2' => '#0f172a', 'bottle' => '#0d9488', 'glow' => '#2dd4bf', 'cap' => '#e2e8f0', 'accent' => '#99f6e4']
];

foreach ($perfumes as $id => $p) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 500" width="100%" height="100%">
  <defs>
    <radialGradient id="bgGlow{$id}" cx="50%" cy="40%" r="60%">
      <stop offset="0%" stop-color="{$p['bg1']}" stop-opacity="0.9"/>
      <stop offset="100%" stop-color="{$p['bg2']}" stop-opacity="1"/>
    </radialGradient>
    <linearGradient id="bottleGrad{$id}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="{$p['bottle']}" stop-opacity="0.9"/>
      <stop offset="50%" stop-color="{$p['glow']}" stop-opacity="0.75"/>
      <stop offset="100%" stop-color="{$p['bottle']}" stop-opacity="0.95"/>
    </linearGradient>
    <linearGradient id="capGrad{$id}" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="{$p['cap']}" stop-opacity="0.7"/>
      <stop offset="50%" stop-color="#ffffff" stop-opacity="0.95"/>
      <stop offset="100%" stop-color="{$p['cap']}" stop-opacity="0.8"/>
    </linearGradient>
    <filter id="glassGlow{$id}" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="8" result="blur" />
      <feComposite in="SourceGraphic" in2="blur" operator="over" />
    </filter>
  </defs>

  <!-- Background -->
  <rect width="400" height="500" rx="20" fill="url(#bgGlow{$id})" />
  
  <!-- Ambient backdrop lighting -->
  <circle cx="200" cy="240" r="140" fill="{$p['glow']}" opacity="0.18" filter="url(#glassGlow{$id})"/>

  <!-- Pedestal shadow -->
  <ellipse cx="200" cy="420" rx="90" ry="18" fill="#000000" opacity="0.5" filter="url(#glassGlow{$id})"/>

  <!-- Bottle Cap & Spray Head -->
  <rect x="175" y="100" width="50" height="45" rx="6" fill="url(#capGrad{$id})" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"/>
  <rect x="188" y="85" width="24" height="15" rx="3" fill="url(#capGrad{$id})"/>
  <rect x="195" y="75" width="10" height="10" rx="2" fill="{$p['accent']}"/>

  <!-- Bottle Neck -->
  <rect x="165" y="145" width="70" height="25" rx="4" fill="url(#capGrad{$id})" opacity="0.9"/>
  <rect x="170" y="152" width="60" height="4" fill="{$p['accent']}"/>

  <!-- Main Glass Bottle Body -->
  <path d="M 120 170 L 280 170 C 300 170 310 185 310 205 L 300 380 C 300 395 285 405 265 405 L 135 405 C 115 405 100 395 100 380 L 90 205 C 90 185 100 170 120 170 Z" 
        fill="url(#bottleGrad{$id})" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>

  <!-- Glass Highlight Reflections -->
  <path d="M 110 190 Q 120 370 125 390" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="4" stroke-linecap="round"/>
  <path d="M 290 190 Q 280 370 275 390" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>

  <!-- Glass Label Box (Glassmorphic plaque) -->
  <rect x="135" y="240" width="130" height="100" rx="12" fill="rgba(15, 23, 42, 0.75)" stroke="{$p['accent']}" stroke-width="1.5" />

  <!-- Label Typography -->
  <text x="200" y="268" text-anchor="middle" fill="#ffffff" font-family="'Playfair Display', Georgia, serif" font-size="12" font-weight="bold" letter-spacing="1.5">ÉLIXIR DE LUXE</text>
  <line x1="155" y1="278" x2="245" y2="278" stroke="{$p['accent']}" stroke-width="1" />
  <text x="200" y="296" text-anchor="middle" fill="{$p['accent']}" font-family="'Outfit', sans-serif" font-size="11" font-weight="700" letter-spacing="1">{$p['title']}</text>
  <text x="200" y="312" text-anchor="middle" fill="#e2e8f0" font-family="'Outfit', sans-serif" font-size="9" letter-spacing="2">{$p['sub']}</text>
  <text x="200" y="328" text-anchor="middle" fill="#94a3b8" font-family="'Outfit', sans-serif" font-size="8">100ml / 3.4 fl oz</text>
</svg>
SVG;
    file_put_contents(__DIR__ . "/assets/images/perfume{$id}.svg", $svg);
    // Also save copy as perfume{$id}.jpg for fallback compatibility
    file_put_contents(__DIR__ . "/assets/images/perfume{$id}.jpg", $svg);
}

// Update DB to use .svg
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=perfume_db', 'root', '');
    for ($i = 1; $i <= 8; $i++) {
        $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
        $stmt->execute(["assets/images/perfume{$i}.svg", $i]);
    }
    echo "SVG Perfumes created and DB updated successfully!\n";
} catch (Exception $e) {
    echo "SVG created. DB note: " . $e->getMessage() . "\n";
}
?>
