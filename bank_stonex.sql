-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 21 Lis 2022, 14:08
-- Wersja serwera: 10.4.14-MariaDB
-- Wersja PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `bank_stonex`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `konta`
--

CREATE TABLE `konta` (
  `nr_konta` varchar(26) COLLATE utf8_polish_ci NOT NULL,
  `saldo` decimal(22,2) NOT NULL,
  `rodzaj` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `konta`
--

INSERT INTO `konta` (`nr_konta`, `saldo`, `rodzaj`, `user_id`) VALUES
('12345678910111213141516171', '1000000.00', 'bank', 1),
('26490268219468512890447231', '0.00', 'przekorzystne', 19),
('26490268219468512890447232', '0.00', 'oszczednosciowe', 19),
('26490268219468512890447233', '0.00', 'walutowe', 19),
('33490268919468012890447201', '0.00', 'przekorzystne', 20),
('33490268919468012890447202', '0.00', 'oszczednosciowe', 20),
('33490268919468012890447203', '0.00', 'walutowe', 20),
('70490268219468012890444265', '0.00', 'przekorzystne', 18),
('70490268219468012890444266', '0.00', 'oszczednosciowe', 18),
('70490268219468012890444267', '0.00', 'walutowe', 18);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `przelewy`
--

CREATE TABLE `przelewy` (
  `Id` int(11) NOT NULL,
  `tytul` varchar(15) COLLATE utf8_polish_ci NOT NULL,
  `nr_konta_nadawcy` varchar(26) COLLATE utf8_polish_ci NOT NULL,
  `nr_konta_odbiorcy` varchar(26) COLLATE utf8_polish_ci NOT NULL,
  `kwota` decimal(22,2) NOT NULL,
  `data` datetime NOT NULL,
  `waluta` varchar(2) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `pin` int(4) NOT NULL,
  `name` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  `surname` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  `address` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `city` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `numer_telefonu` int(9) NOT NULL,
  `nr_konta_przekorzystnego` varchar(26) COLLATE utf8_polish_ci NOT NULL COMMENT 'w złotówkach',
  `nr_konta_oszczednosciowego` varchar(26) COLLATE utf8_polish_ci NOT NULL COMMENT 'w złotówkach',
  `nr_konta_walutowego` varchar(26) COLLATE utf8_polish_ci NOT NULL COMMENT 'w euro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `pin`, `name`, `surname`, `address`, `city`, `email`, `numer_telefonu`, `nr_konta_przekorzystnego`, `nr_konta_oszczednosciowego`, `nr_konta_walutowego`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1234, 'admin', 'admin', 'ul. Szlak 49', 'Kraków', 'administracja@bank.com', 111222333, '12345678910111213141516171', '20000000000000000000000000', '30000000000000000000000000'),
(18, 'tomaszadamski', 'c7a532bb945ad7eb9f63132411b156f3', 9935, 'Tomasz', 'Adamski', 'ul. Fioletowa 14', 'Katowice', 'tomaszadamski@mail.com', 720971426, '70490268219468012890444265', '70490268219468012890444266', '70490268219468012890444267'),
(19, 'michalwybicki', 'e884320566ad81608aa9d64362c86b2a', 4249, 'Michał', 'Wybicki', 'ul. Radosna 8', 'Wrocław', 'michalwybicki@mail.com', 789446018, '26490268219468512890447231', '26490268219468512890447232', '26490268219468512890447233'),
(20, 'martynarozek', '63b383cdad71ce68bc17e049c5d0006a', 2805, 'Martyna', 'Rożek', 'ul. Diamentowa 19', 'Gdynia', 'martynarozek@mail.com', 223951016, '33490268919468012890447201', '33490268919468012890447202', '33490268919468012890447203');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `konta`
--
ALTER TABLE `konta`
  ADD PRIMARY KEY (`nr_konta`);

--
-- Indeksy dla tabeli `przelewy`
--
ALTER TABLE `przelewy`
  ADD PRIMARY KEY (`Id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `przelewy`
--
ALTER TABLE `przelewy`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
