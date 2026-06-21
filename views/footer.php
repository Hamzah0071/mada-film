<?php
// views/footer.php
?>
  <footer class="main-footer">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg); text-align: left; margin-bottom: var(--spacing-xl);">
        
        <div class="footer-about">
          <a href="../views/film_page.view.php" class="logo">
            <h2 style="color: var(--text-light); margin-bottom: var(--spacing-sm);">MG<span style="color: var(--primary-color);">Film</span></h2>
          </a>
          <p style="font-size: 0.9rem; color: var(--text-muted);">Magazine de cinéma indépendant basé à Paris, France.</p>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: var(--spacing-sm);"><strong>Email :</strong> contact@mgfilm.fr</p>
        </div>

        <div class="footer-links">
          <h4 style="color: var(--text-light); margin-bottom: var(--spacing-md);">Navigation</h4>
          <ul style="display: flex; flex-direction: column; gap: var(--spacing-sm); font-size: 0.9rem;">
            <li><a href="../views/film_page.view.php">Accueil</a></li>
            <li><a href="#">Critiques</a></li>
            <li><a href="#">Festivals</a></li>
            <li><a href="#">À propos</a></li>
          </ul>
        </div>

      </div>

      <div style="border-top: 1px solid #333; padding-top: var(--spacing-md); font-size: 0.85rem; color: var(--text-muted);">
        <p>© <?= date('Y') ?> <strong>MG Film</strong>. Tous droits réservés.</p>
      </div>
    </div>
  </footer>
</body>
</html>
