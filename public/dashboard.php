<?php
$x = 5;
$nome = "MedIA";
$carros = ["Mercedes", "Volvo", "Tata", "BMW", "Toyota"];

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Rascunho PHP</title>
</head>
<body>
    <h1>Rascunho PHP — <?= htmlspecialchars($nome) ?></h1>
    <p>Variável x = <?= $x ?></p>

    <h2>Lista de carros</h2>
    <?php foreach ($carros as $i => $carro): ?>
        <p style="color: <?= $i % 2 === 0 ? 'blue' : 'red' ?>">
            <?= $i % 2 === 0 ? 'PAR' : 'ÍMPAR' ?> — <?= htmlspecialchars($carro) ?>
        </p>
    <?php endforeach; ?>
</body>
</html>
