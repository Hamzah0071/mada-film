-- Entité principale : utilisateur
CREATE TABLE USER (
  id_user       INT PRIMARY KEY AUTO_INCREMENT,
  name_user     VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  role          ENUM('admin', 'client') DEFAULT 'client',
  photo_profil  VARCHAR(255)
);

-- Entité film
CREATE TABLE FILM (
  id_film          INT PRIMARY KEY AUTO_INCREMENT,
  title_film       VARCHAR(200) NOT NULL,
  name_realisateur VARCHAR(150) NOT NULL,
  film_year        YEAR NOT NULL,
  description      TEXT,
  img              VARCHAR(255)
);

-- Entité genre (extraite du JSON → table propre)
CREATE TABLE GENRE (
  id_genre   INT PRIMARY KEY AUTO_INCREMENT,
  name_genre VARCHAR(100) NOT NULL UNIQUE
);

-- Table de liaison Film ↔ Genre (un film peut avoir plusieurs genres)
CREATE TABLE FILM_GENRE (
  id_film  INT NOT NULL,
  id_genre INT NOT NULL,
  PRIMARY KEY (id_film, id_genre),
  FOREIGN KEY (id_film)  REFERENCES FILM(id_film)  ON DELETE CASCADE,
  FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre) ON DELETE CASCADE
);

-- Table de liaison User ↔ Genre (préférences)
CREATE TABLE USER_GENRE (
  id_user  INT NOT NULL,
  id_genre INT NOT NULL,
  PRIMARY KEY (id_user, id_genre),
  FOREIGN KEY (id_user)  REFERENCES USER(id_user)   ON DELETE CASCADE,
  FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre) ON DELETE CASCADE
);

-- Notes de films avec contrainte d'unicité user/film
CREATE TABLE FILM_NOTE (
  id_film_note INT PRIMARY KEY AUTO_INCREMENT,
  id_user      INT NOT NULL,
  id_film      INT NOT NULL,
  note         TINYINT NOT NULL CHECK (note BETWEEN 1 AND 10),
  commentaire  TEXT,
  date_note    DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_user_film (id_user, id_film),   -- un user = une seule note par film
  FOREIGN KEY (id_user) REFERENCES USER(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_film) REFERENCES FILM(id_film) ON DELETE CASCADE
);