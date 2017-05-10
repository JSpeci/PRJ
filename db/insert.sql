INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'volno', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'obsazeno', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'vedle', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'daleko', '1');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'nepracuje', '0');
INSERT INTO `blesk1`.`Stav` (`idStav`, `nazev`, `volat`) VALUES (NULL, 'čekání', '1');


INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`) 
VALUES (NULL, 'Honza', 'Jan špecián', 'Schillerova 177, Liberec 12', 'jan.specian@seznam.cz', '602440287');

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`) 
VALUES (NULL, 'Radek', 'Radek Kříž', NULL , NULL, '602106633');

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`) 
VALUES (NULL, 'Michal', 'Michal Zolák', NULL , NULL, '603410083');

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`) 
VALUES (NULL, 'Jarda', 'Jaroslav Jeřábek', NULL , NULL, '608519096');

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`) 
VALUES (NULL, 'Radim', 'Radim Spiler', NULL , NULL, '776631492');

INSERT INTO `blesk1`.`Osoba` (`idOsoba`, `prezdivka`, `jmeno`, `adresa`, `email`, `telefon`) 
VALUES (NULL, 'Sváťa', 'Sváťa', NULL , NULL, '773003003');


INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '1');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '2');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '3');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '4');
INSERT INTO `blesk1`.`Ridic` (`idRidic`, `idStav`, `idOsoba`) VALUES (NULL, '5', '5');

INSERT INTO `blesk1`.`Dispecer` (`idDispecer`, `idOsoba`, `idStav`) VALUES (NULL, '6', '5');


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

INSERT INTO `blesk1`.`Osoba_has_Smena` (`idOsoba`, `idSmena`) VALUES ('6', '13'), ('1', '13');

INSERT INTO `blesk1`.`Prichod` (`idPrichod`, `idOsoba`, `kdy`) VALUES (NULL, '1', '2017-04-30 07:59:00.000');
INSERT INTO `blesk1`.`Prichod` (`idPrichod`, `idOsoba`, `kdy`) VALUES (NULL, '6', '2017-04-30 08:15:00.000');


INSERT INTO `blesk1`.`Objednavka` (`idObjednavka`, `casVytvoreni`, `casPristaveniTaxi`, `idAdresa`, `pocetVozu`, `poznamka`) 
VALUES (NULL, '2017-04-28 09:18:15.000', '2017-04-30 16:00:00.000', '1', '1', 'se psem');

/* Atribut idObjednavka muze byt null - v tom pripade byla jizda bez objednavky = nalozeni zakaznika nekde po meste a nahlaseni kam ho vezu*/

INSERT INTO `blesk1`.`Jizda` (`idJizda`, `idAdresaOdkud`, `idAdresaKam`, `idRidic`, `casStart`, `casKonec`, `idSmena`, `idObjednavka`, `pribliznaCena`, `Ridic_idOsoba`, `pocetOsob`) 
VALUES (NULL, '1', '2', '1', '2017-04-30 16:02:00.000', '2017-04-30 16:09:00.000', '13', NULL, '65', '1', '1')








