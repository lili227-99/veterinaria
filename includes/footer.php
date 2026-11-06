    </main>
</div> <!-- cierra .layout -->

<script src="assets/js/app.js"></script>
<?php
$js_pagina = __DIR__ . '/../assets/js/' . ($_GET['pages'] ?? 'inicio') . '.js';
if (file_exists($js_pagina)): ?>
    <script src="assets/js/<?= htmlspecialchars($_GET['pages']) ?>.js"></script>
<?php endif; ?>
</body>
</html>