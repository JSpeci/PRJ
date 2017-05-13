INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'volno', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'obsazeno', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'vedle', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'daleko', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'nepracuje', '0');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'čekání', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'pracuje', '1');


INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (4, 'Honza', 'Jan Špecián', 'Schillerova 177, Liberec 12', 'jan.specian@seznam.cz', '602440287', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (2, 'Radek', 'Radek Kříž', NULL , NULL, '602106633', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (14, 'Michal', 'Michal Zolák', NULL , NULL, '603410083', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (20, 'Jarda', 'Jaroslav Jeřábek', NULL , NULL, '608519096', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (3, 'Radim', 'Radim Spiler', NULL , NULL, '776631492', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (8, 'Dalča', 'Dalibor Čirlič', NULL , NULL, '123456789', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (19, 'Tomin', 'Tomáš Dvořáček', NULL , NULL, '123456789', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (15, 'Luďek', 'Luděk Dousek', NULL , NULL, '123456789', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (17, 'Jenda', 'Jan Špecián', NULL , NULL, '739551887', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (1, 'Sváťa', 'Sváťa', NULL , NULL, '773003003', md5('heslo'));

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`, `pass`) 
VALUES (5, 'Jana', 'Jana', NULL , NULL, '773003003', md5('heslo'));


INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (15, '1', '4');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '3', '2');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '4', '14');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '2', '20');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '3');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '8');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '1', '19');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '15');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '17');

INSERT INTO `blesk1`.`Dispecer` (`idDispecer`, `idOsoba`, `idStav`) VALUES (NULL, '1', '7');
INSERT INTO `blesk1`.`Dispecer` (`idDispecer`, `idOsoba`, `idStav`) VALUES (NULL, '5', '5');


INSERT INTO `blesk1`.`Auto` (`idAuto`, `idRidic`, `idVysilacka`, `znacka`, `model`, `barva`, `pocetMist`, `regZnacka`, `taxiOdDne`) 
VALUES (NULL, '1', '4', 'Audi', 'A6', 'červená', '4', '4L7', NOW());

INSERT INTO `blesk1`.`Auto` (`idAuto`, `idRidic`, `idVysilacka`, `znacka`, `model`, `barva`, `pocetMist`, `regZnacka`, `taxiOdDne`) 
VALUES (NULL, '1', '17', 'Audi', 'A6', 'modrá', '4', '8B6', '2016-09-01 01:01:02.000');


INSERT INTO `blesk1`.`Adresa` (`idAdresa`, `ulice`, `cislo`, `mesto`, `pocetOdjezdu`, `pocetPrijezdu`, `gps_lat`, `gps_lgn`, `poznamka`) 
VALUES (NULL, 'nám. Dr. E. Beneše ', '1', 'Liberec', '11', '21', '50.7700586', '15.0585467', 'divadlo, radnice');

INSERT INTO `blesk1`.`Adresa` (`idAdresa`, `ulice`, `cislo`, `mesto`, `pocetOdjezdu`, `pocetPrijezdu`, `gps_lat`, `gps_lgn`, `poznamka`) 
VALUES (NULL, 'Fugnerova', '1', 'Liberec', '1', '2', '50.7665369', '15.0562775', 'jednosměrná');

INSERT INTO `blesk1`.`Misto` (`idMisto`, `idAdresa`, `nazev`) VALUES (NULL, '1', 'Radnice');
INSERT INTO `blesk1`.`Misto` (`idMisto`, `idAdresa`, `nazev`) VALUES (NULL, '2', 'KFC');

INSERT INTO `blesk1`.`Smena` (`idSmena`, `od`, `do`, `poznamka`) VALUES (NULL, '2017-04-29 20:00:00.000', '2017-04-30 8:00:00.000', NULL);
INSERT INTO `blesk1`.`Smena` (`idSmena`, `od`, `do`, `poznamka`) VALUES (NULL, '2017-04-30 08:00:00.000', '2017-04-30 20:00:00.000', NULL);
INSERT INTO `blesk1`.`Smena` (`idSmena`, `od`, `do`, `poznamka`) VALUES (NULL, '2017-05-10 08:00:00.000', '2017-5-10 20:00:00.000', "streda");

INSERT INTO `blesk1`.`Osoba_has_Smena` (`idOsoba`, `idSmena`) VALUES ('1', '3'), ('4', '3'), ('10', '3');


INSERT INTO `blesk1`.`Objednavka` (`idObjednavka`, `casVytvoreni`, `casPristaveniTaxi`, `idAdresa`, `pocetVozu`, `poznamka`) 
VALUES (NULL, '2017-04-28 09:18:15.000', '2017-04-30 16:00:00.000', '1', '1', 'se psem');

/* Atribut idObjednavka muze byt null - v tom pripade byla jizda bez objednavky = nalozeni zakaznika nekde po meste a nahlaseni kam ho vezu*/

INSERT INTO `blesk1`.`Jizda` (`idJizda`, `idRidic`, `Ridic_idOsoba`, `idAdresaOdkud`, `idAdresaKam`, `casStart`, `casKonec`, `idSmena`, `idObjednavka`, `pribliznaCena`, `pocetOsob`) 
VALUES (NULL, '1', '4', '1','2', '2017-04-30 16:02:00.000', '2017-04-30 16:09:00.000', '13', NULL, '65', '1');

    
INSERT INTO Smena values(null,'2017-5-13 8:00:00.0', '2017-5-13 20:00:00.0',null);





