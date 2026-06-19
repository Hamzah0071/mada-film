<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>MG Film — Cinéma & Critiques</title>
  <meta name="description" content="Actualités cinéma, critiques de films, analyses et tendances du 7ème art">
  <meta name="keywords" content="cinéma, films, critiques, sorties, acteurs, réalisateurs">
  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    /* ─── Film-themed overrides ─── */
    :root {
      --film-gold: #e8b94f;
      --film-dark: #0d0d0d;
      --film-red:  #c0392b;
    }

    /* Star rating helper */
    .stars { color: var(--film-gold); font-size: .85rem; letter-spacing: 1px; }

    /* Badge pill for genre/category */
    .genre-pill {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 20px;
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .06em;
      text-transform: uppercase;
    }
    .genre-pill.action   { background: #c0392b22; color: #c0392b; }
    .genre-pill.drama    { background: #2980b922; color: #2980b9; }
    .genre-pill.sci-fi   { background: #8e44ad22; color: #8e44ad; }
    .genre-pill.thriller { background: #e67e2222; color: #e67e22; }
    .genre-pill.comedy   { background: #27ae6022; color: #27ae60; }
    .genre-pill.animation{ background: #16a08522; color: #16a085; }

    /* Slider overlay tweak — make text pop on film stills */
    .slider .swiper-slide .content {
      background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.25) 70%, transparent 100%);
    }
    .slider .swiper-slide .content .meta-row {
      display: flex; align-items: center; gap: .6rem;
      margin-bottom: .4rem; font-size: .8rem; color: #bbb;
    }
    .slider .swiper-slide .content .stars { font-size: 1rem; }

    /* Post entry mini-meta */
    .film-meta {
      display: flex; align-items: center; gap: .5rem;
      font-size: .75rem; color: #888; margin-bottom: .3rem;
    }
    .film-meta .stars { font-size: .75rem; }

    /* "En salle" badge */
    .in-theaters {
      display: inline-block;
      background: var(--film-red);
      color: #fff;
      font-size: .65rem; font-weight: 700;
      padding: 1px 7px; border-radius: 3px;
      text-transform: uppercase; letter-spacing: .07em;
    }

    /* Trending list tweak */
    .trending .trending-post li a h3 { font-size: .88rem; }

    /* Director credit line */
    .director-line { font-size: .8rem; color: #777; margin-bottom: .3rem; }
    .director-line span { color: #444; font-weight: 600; }

    /* Section accent bar */
    .section-title h2::after {
      content: '';
      display: block;
      width: 40px;
      height: 3px;
      background: var(--film-gold);
      margin-top: 6px;
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
        <h1 class="sitename">MG<span style="color:var(--film-gold)">Film</span></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html" class="active">Accueil</a></li>
          <li><a href="about.html">À propos</a></li>
          <li><a href="single-post.html">Critique</a></li>
          <li class="dropdown"><a href="#"><span>Genres</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="category.html">Action & Aventure</a></li>
              <li><a href="category.html">Drame</a></li>
              <li><a href="category.html">Science-Fiction</a></li>
              <li><a href="category.html">Comédie</a></li>
              <li class="dropdown"><a href="#"><span>Plus de genres</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Thriller</a></li>
                  <li><a href="#">Animation</a></li>
                  <li><a href="#">Horreur</a></li>
                  <li><a href="#">Documentaire</a></li>
                  <li><a href="#">Romance</a></li>
                </ul>
              </li>
            </ul>
          </li>
          <li><a href="contact.html">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <div class="header-social-links">
        <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
      </div>

    </div>
  </header>

  <main class="main">
    <!-- espace de films -->
   <section>

   </section>
   <section>
    
   </section>
   <section>
    
   </section>   

  </main>

  <footer id="footer" class="footer dark-background">
    <div class="container footer-top">
      <div class="row gy-4">

        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">MG<span style="color:var(--film-gold)">Film</span></span>
          </a>
          <div class="footer-contact pt-3">
            <p>Magazine de cinéma indépendant</p>
            <p>Paris, France</p>
            <p class="mt-3"><strong>Email :</strong> <span>contact@mgfilm.fr</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-youtube"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Navigation</h4>
          <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="#">Critiques</a></li>
            <li><a href="#">Festivals</a></li>
            <li><a href="#">Réalisateurs</a></li>
            <li><a href="#">À propos</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Genres</h4>
          <ul>
            <li><a href="#">Action</a></li>
            <li><a href="#">Drame</a></li>
            <li><a href="#">Science-Fiction</a></li>
            <li><a href="#">Thriller</a></li>
            <li><a href="#">Animation</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Plateformes</h4>
          <ul>
            <li><a href="#">Netflix</a></li>
            <li><a href="#">Disney+</a></li>
            <li><a href="#">MUBI</a></li>
            <li><a href="#">Amazon Prime</a></li>
            <li><a href="#">Apple TV+</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>La Rédaction</h4>
          <ul>
            <li><a href="#">Marie Fontaine</a></li>
            <li><a href="#">Pierre Leconte</a></li>
            <li><a href="#">Sofia Marino</a></li>
            <li><a href="#">Clara Dubois</a></li>
            <li><a href="#">Jean-Luc Moreau</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">MG Film</strong> <span>Tous droits réservés</span></p>
      
    </div>
  </footer>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
</html>