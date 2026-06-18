-- 1. Table des Utilisateurs (Clients + Admins)

CREATE TABLE Users (
    id_user       INT          PRIMARY KEY AUTO_INCREMENT,
    name_user     VARCHAR(100) NOT NULL,
    email         VARCHAR(150) UNIQUE NOT NULL,
   
    password      VARCHAR(60)  NOT NULL,
    
    role          VARCHAR(50)  NOT NULL DEFAULT 'client'
                               CHECK (role IN ('client', 'admin')),
    photo_profil  VARCHAR(255) NULL
);

-- 2. Table des Genres
CREATE TABLE Genre (
    id_genre   INT          PRIMARY KEY AUTO_INCREMENT,
    name_genre VARCHAR(100) NOT NULL UNIQUE
);

-- 3. Table des Films
CREATE TABLE Film (
    id_film          INT          PRIMARY KEY AUTO_INCREMENT,
    title_film       VARCHAR(255) NOT NULL,
    name_realisateur VARCHAR(150),
   
    film_year        INT          CHECK (film_year BETWEEN 1888 AND 2100),
    description      TEXT,
    img              VARCHAR(255)
);

-- 4. Table de liaison Film <-> Genre
CREATE TABLE Film_Genre (
    id_film  INT NOT NULL,
    id_genre INT NOT NULL,
    PRIMARY KEY (id_film, id_genre),
    FOREIGN KEY (id_film)  REFERENCES Film(id_film)   ON DELETE CASCADE,
    FOREIGN KEY (id_genre) REFERENCES Genre(id_genre) ON DELETE CASCADE
);

CREATE INDEX idx_film_genre_genre ON Film_Genre(id_genre);

-- 5. Table des Préférences (Relation : Users aime Genre)
CREATE TABLE User_Preference (
    id_user  INT NOT NULL,
    id_genre INT NOT NULL,
    PRIMARY KEY (id_user, id_genre),
   
    FOREIGN KEY (id_user)  REFERENCES Users(id_user)   ON DELETE CASCADE,
    FOREIGN KEY (id_genre) REFERENCES Genre(id_genre)  ON DELETE CASCADE
);
CREATE INDEX idx_user_pref_genre ON User_Preference(id_genre);

-- 6. Table des Notes (Relation : Users note Film)
CREATE TABLE Film_Note (
    id_user    INT NOT NULL,
    id_film    INT NOT NULL,
    note_value INT NOT NULL CHECK (note_value BETWEEN 1 AND 10),
    PRIMARY KEY (id_user, id_film),

    FOREIGN KEY (id_user)  REFERENCES Users(id_user)  ON DELETE CASCADE,
    FOREIGN KEY (id_film)  REFERENCES Film(id_film)   ON DELETE CASCADE
);

CREATE INDEX idx_film_note_film ON Film_Note(id_film);
